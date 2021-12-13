<?php

namespace App\Http\Controllers\api\v1;

use App\Models\DeviceToken;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Services\SocialAccountsService;
use Laravel\Socialite\Facades\Socialite;
use DB;

class AuthController extends BaseAPIController
{
    /*
      Delete deviceToken for notifications
     */
    public function logout()
    {
      $accessToken = auth()->user()->token();
      DB::table('oauth_refresh_tokens')
           ->where('access_token_id', $accessToken->id)
           ->update([
               'revoked' => true
           ]);
      $accessToken->revoke();
      return $this->successJsonReponse();
    }

    public function login(Request $request)
    {
        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);


        if (!Auth::attempt($login)) {
            return $this->getInvalidLoginResponse();
        }

        return $this->responseJSON(auth()->user()->getAccessToken());
    }

    public function getInvalidLoginResponse()
    {
      return $this->responseJSON(['errors' => ['message' => [trans('message.invalidLoginCredentials')]]], 422);
    }

    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'sur_name' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:8'],
        ]);
        if ($validator->fails()) {
            return $this->responseJSON(['errors'=>$validator->errors()], 422);
        }

        if (User::where('email', $request->email)->exists()) {
            return $this->responseJSON(['errors' => ['email' => [trans('message.emailAlreadyTaken')]]], 422);
        }
        $user = User::create([
            'name' => $request->firstName,
            'sur_name' => $request->lastName,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->set_password = true;
        $user->update();

        $user->sendEmailVerificationNotification();

        return $this->responseJSON($user->getAccessToken());
    }

    public function socialLogin(Request $request)
    {
      $providerUser = null;
      try {
        $socialite = Socialite::driver($request->provider);

        if($request->provider == 'facebook'){
          $socialite = $socialite->fields([
                    'name',
                    'sur_name',
                    'email',
                    'gender',
                    'verified'
                ]);
        }

        if($request->provider == 'google'){
            $token = $socialite->getAccessTokenResponse($request->code)['access_token'];
        }elseif (in_array($request->provider , ['facebook', 'apple'])) {
            $token = $request->code;
        }
        $providerUser = $socialite->userFromToken($token);

      } catch (\Exception $e) {
        return $this->getInvalidLoginResponse();
      }

      if ($providerUser) {
          /*
           * if user is deleted by admin return with error
           * email deleted_At exist
           */
          $trashUser = (new SocialAccountsService())->getTrashedUser($providerUser);
          if($trashUser){
              return response()->json(['errors'=>['message'=>[trans('message.accountlocked')]]], 422);
          }

          $user = (new SocialAccountsService())->findOrCreate($providerUser, $request->provider);
          $user->update();

          /*
           * send verification email if email is not verified
           */
          if(empty($user->email_verified_at)){
              $user->sendEmailVerificationNotification();
          }
          

          return $this->responseJSON($user->getAccessToken());
      }
      return $this->getInvalidLoginResponse();
    }

    public function resendEmail()
    {
        if (\request()->user()->hasVerifiedEmail()) {
            return response()->json(['errors'=>['message'=>[trans('message.emailAlreadyVerified')]]], 422);
        }

        \request()->user()->sendEmailVerificationNotification();

        return $this->successJsonReponse();
    }

}

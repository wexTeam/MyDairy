<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\UpdateProfileRequest;
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

        return $this->successJsonReponse(auth()->user()->getAccessToken());
    }

    public function getInvalidLoginResponse()
    {
        return $this->errorJsonReponse(trans('message.invalidLoginCredentials'));
    }

    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'sur_name' => 'required',
            'email' => ['required', 'max:255', 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix', 'unique:users'],
            'password' => ['required', 'string', 'min:8',],
            'dob' => ['required','date_format:Y-m-d'],
            'postal_code' => ['required','numeric'],
            'address' => ['nullable','string'],
            'latitude' => ['required','numeric', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required','numeric', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'active' => ['nullable','boolean']
        ]);
        if ($validator->fails()) {
            return $this->errorJsonReponse('Not Successfully create your account',$validator->errors());
        }

        if (User::where('email', $request->email)->exists()) {
            return  $this->errorJsonReponse(trans('message.emailAlreadyTaken'));
        }
        $user = User::create([
            'name' => $request->name,
            'sur_name' => $request->sur_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'dob' => $request->dob,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone_number' => $request->phone_number,
            'is_active' => true,
        ]);
        $user->set_password = true;
        $user->update();

        $user->sendEmailVerificationNotification();

        return $this->successJsonReponse($user->getAccessToken());
    }

    public function socialLogin(Request $request)
    {
        $providerUser = null;
        try {
            $socialite = Socialite::driver($request->provider);

            if ($request->provider == 'facebook') {
                $socialite = $socialite->fields([
                    'name',
                    'sur_name',
                    'email',
                    'gender',
                    'verified'
                ]);
            }

            if ($request->provider == 'google') {
                $token = $socialite->getAccessTokenResponse($request->code)['access_token'];
            } elseif (in_array($request->provider, ['facebook', 'apple'])) {
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
            if ($trashUser) {
                return $this->errorJsonReponse(trans('message.accountlocked'));
            }

            $user = (new SocialAccountsService())->findOrCreate($providerUser, $request->provider);
            $user->update();

            /*
           * send verification email if email is not verified
           */
            if (empty($user->email_verified_at)) {
                $user->sendEmailVerificationNotification();
            }


            return $this->responseJSON($user->getAccessToken());
        }
        return $this->getInvalidLoginResponse();
    }

    public function resendEmail()
    {
        if (\request()->user()->hasVerifiedEmail()) {
            return $this->errorJsonReponse(trans('message.emailAlreadyVerified'));
        }

        \request()->user()->sendEmailVerificationNotification();

        return $this->successJsonReponse();
    }
}

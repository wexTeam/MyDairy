<?php

namespace App\Http\Controllers\api\v1;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DeviceToken;
class ForgotPasswordController extends BaseAPIController
{
    public function forgot(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 422, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                $response = Password::sendResetLink($request->only('email'));
                switch ($response) {
                    case Password::RESET_LINK_SENT:
                        return  \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
                    case Password::INVALID_USER:
                        return \Response::json(array("status" => 422, "message" => trans($response), "data" => array()));
                }
            } catch (\Swift_TransportException $ex) {
                $arr = array("status" => 422, "message" => $ex->getMessage(), "data" => []);
            }
        }
        return \Response::json($arr);
    }

    public function reset(Request $request) {
        $credentials = request()->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string'
        ]);
        $loginUser = '';
        $reset_password_status = Password::reset($credentials, function ($user, $password) use(&$loginUser) {
            $user->password = Hash::make($password);
            $user->save();
            $loginUser = $user;
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["status" => 422, "msg" => trans('passwords.token')], 422);

        }
        
        return $this->responseJSON($loginUser->getAccessToken());
    }
}

<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UserLocationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseAPIController
{
  public function userInfo()
  {
    return $this->responseJSON(['user' => auth()->user()->getUserData()]);
  }

  public function updateProfile(Request $request)
  {
    $input = $request->all();
    $validator = Validator::make($input, [
        'name' => ['required'],
        'sur_name' => ['required'],
        'dob' => ['required','date_format:Y-m-d'],
        'postal_code' => ['required','numeric'],
        'address' => ['nullable','string'],
        'latitude' => ['required','numeric', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        'longitude' => ['required','numeric', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        'active' => ['nullable','boolean']
    ]);
    if ($validator->fails()) {
      return $this->errorJsonReponse('Profile not updated',$validator->errors());
    }

    $user = User::findOrFail(Auth::user()->id);

    $user->name = $request->get('name');

    $user->sur_name = $request->get('sur_name');

    $user->phone_number = $request->get('phone_number');

    $user->address = $request->get('address');

    $user->dob = $request->get('dob');

    $user->is_active = $request->get('is_active') ?? $user->is_active;

    $user->postal_code = $request->get('postal_code');

    $user->latitude = $request->get('latitude');
    $user->longitude = $request->get('longitude');

    $user->update();

    return $this->successJsonReponse($user->fresh());
  }


  public function updatePassword()
  {
    $user = auth()->user();

    $rules = [
      'newPassword' => ['required', 'string', 'min:8'],
    ];
    if ($user->set_password) {
      $rules['oldPassword'] = ['required', 'string', 'min:8'];
    }

    $validator = Validator::make(request()->all(), $rules);
    if ($validator->fails()) {
      return $this->errorJsonReponse('password not update',$validator->errors());
    }

    if (
      $user->set_password &&
      !Hash::check(request()->oldPassword, $user->password)
    ) {
      return $this->errorJsonReponse(['errors' => ['oldPassword' => [trans('message.invalidOldPassword')]]], 422);
    }

    if (!empty(request()->get('newPassword'))) {
      $user->password = Hash::make(request()->get('newPassword'));
      $user->save();
    }

    $user->setPassword(true);
    return $this->successJsonReponse();
  }

  public function updateLocation(UserLocationRequest $request)
  {
    $user = User::findOrFail(Auth::user()->id);
    $user->latitude = $request->get('latitude');
    $user->longitude = $request->get('longitude');
    $user->update();
    return $this->successJsonReponse();
  }

  public function getLocation()
  {
    $user = User::findOrFail(Auth::user()->id);

    return $user->only('latitude', 'longitude');
  }
}

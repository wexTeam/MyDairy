<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sur_name',
        'avatar',
        'phone_number',
        'email',
        'address',
        'country',
        'city',
        'state',
        'postal_code',
        'password',
        'latitude',
        'longitude',
        'email_verified_at',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAccessToken()
    {
        $accessToken = $this->createToken('myDairy')->accessToken;
        $userData = $this->getUserData();

        if($this->name){
            $this->setFirstLogin(false);
        }

        return ['user' => $userData, 'token' => $accessToken];
    }

    public function deviceTokens(){
        return $this->hasMany('App\Models\DeviceToken','user_id','id');
    }

    public function deviceTokensArray(){
        return $this->deviceTokens()->pluck('token')->toArray();
    }


    public function fullName()
    {
        return $this->name;
    }

    public function setEmailVerifiedDate($value=''){
        if (!empty($date)){
            $this->email_verified_at = $value;
        } else {
            $this->email_verified_at = Carbon::now();
        }
        $this->save();
    }

    public function setAdmin(bool $value){
        $this->is_admin = $value;
        $this->save();
    }

    public function getUserData()
    {
        // To Get All attribute of User
        // if use $this so only attributes attach with $this will return

        $user = $this::findOrFail($this->id);

        $data =  $user->only(
            'name',
            'sur_name',
            'phone_number',
            'email',
            'address',
            'postal_code',
            'latitude',
            'longitude',
            'email_verified_at',
            'is_active'
        );

        return $data;
    }

    public function setPassword($value)
    {
        $this->set_password = $value;
        $this->save();
    }

    public function setFirstLogin($value)
    {
        $this->first_login = $value;
        $this->save();
    }

}

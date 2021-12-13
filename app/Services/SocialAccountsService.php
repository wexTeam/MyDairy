<?php
namespace App\Services;
use App\Models\User;
use App\Models\LinkedSocialAccount;
use Laravel\Socialite\Two\User as ProviderUser;
class SocialAccountsService
{
    /**
     * Find or create user instance by provider user instance and provider name.
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     *
     * @return User
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider): User
    {
        $user = User::where('email', $providerUser->getEmail());

        // login with apple sub id
//        if(isset($providerUser->user['sub'])){
//            $user->orWhere('apple_sub_id', $providerUser->user['sub']);
//        }
        
        $user = $user->withTrashed()->first();

      if (! $user) {
          $user = $this->createUser($providerUser, $provider);
      }
      return $user;
    }

    /**
     * @param ProviderUser $providerUser
     * @param string $provider
     * @return mixed
       */
    public function createUser(ProviderUser $providerUser, string $provider='')
      {
        $data['email'] =  $providerUser->getEmail();
        $data['password'] =  \bcrypt(rand(9999,1000).time().rand(9999,1000));

        if(isset($providerUser->avatar)){
          $data['avatar'] = $providerUser->avatar;
        }
        if($provider == 'google'){
          if($providerUser->user['given_name']){
            $data['first_name'] = $providerUser->user['given_name'];
          }

          if($providerUser->user['family_name']){
            $data['last_name'] = $providerUser->user['family_name'];
          }
        }
        elseif($provider == 'facebook'){
          if($providerUser->user['first_name']){
            $data['first_name'] = $providerUser->user['first_name'];
          }

          if ($providerUser->user['last_name']) {
            $data['last_name'] = $providerUser->user['last_name'];
          }
        }elseif ($provider == 'apple'){

            if(request()->get('first_name')){
                $data['first_name'] = request()->get('first_name');
            }else{
                $data['first_name'] = 'User';
            }

            if(request()->get('last_name')){
                $data['last_name'] = request()->get('last_name');
            }else{
                $data['last_name'] = 'YurekAI';
            }

//            $data['apple_sub_id']  =  $providerUser->user['sub'];

            if(empty($data['email'])){
                $data['email'] = $data['apple_sub_id']. '@yurekai.ch';
            }
        }

        if( empty($data['first_name'])){
            $data['first_name'] = $providerUser->getName();
        }

        if( empty($data['last_name'])){
          $data['last_name'] = $providerUser->getName();
        }

        $user = User::create($data);

        $user->setPassword(false);
        // if we have email in response then set email verified. apple social login may not have email in response
        if($providerUser->getEmail()){
          $user->setEmailVerifiedDate();
        }




        return $user;
      }

    public function getTrashedUser(ProviderUser $providerUser){
        return User::where('email', $providerUser->getEmail())->onlyTrashed()->first();
    }
}

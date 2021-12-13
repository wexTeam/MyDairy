<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLanguage;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '';
    protected $lang = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*
         * just for try laer change that
         */
        $this->lang = \request()->get('language') ? \request()->get('language') : config('app.locale');

        $this->middleware('guest')->except('logout');
        $this->redirectTo = route('home').'?language='.$this->lang;
    }

    public function getAuthToken()
    {
      dd(request()->all());
    }
    /**
    * Handle Social login request
    *
    * @return response
    */
   public function socialLogin($social)
   {
       return Socialite::driver($social)->redirect();
   }
   /**
    * Obtain the user information from Social Logged in.
    * @param $social
    * @return Response
    */
   public function handleProviderCallback($social)
   {
      dd(request()->all());
       $userSocial = Socialite::driver($social)->user();
       dd(request()->all(), $userSocial);
       $user = User::where(['email' => $userSocial->getEmail()])->first();
       if($user){
           Auth::login($user);
           return redirect()->action('HomeController@index');
       }else{
           return view('auth.register',['name' => $userSocial->getName(), 'email' => $userSocial->getEmail()]);
       }
   }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login?language='.$this->lang);
    }
}

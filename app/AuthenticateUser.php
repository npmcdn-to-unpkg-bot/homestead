<?php namespace App;

use Illuminate\Contracts\Auth\Guard; 
use Laravel\Socialite\Contracts\Factory as Socialite; 
use App\User; 
use Request;

class AuthenticateUser {     

     private $socialite;
     private $auth;
     private $users;

     public function __construct(Socialite $socialite, Guard $auth, User $users) {   
         
        $this->socialite = $socialite;
        $this->users = $users;
        $this->auth = $auth;
        
    }

    public function execute($request, $listener, $provider) {
        
       if (!$request) {
           $auth = $this->getAuthorizationFirst($provider);
           return $auth;
       }
     
       $r = $this->getSocialUser($provider);
       // TODO set up roles
       // just me for now
       if ($r->email != 'mbbyrnes@gmail.com') {
           return false;
       }
       $user = $this->users->findByUserNameOrCreate($r);

       $this->auth->login($user, true);

       return $listener->userHasLoggedIn($user);
    
    }

    private function getAuthorizationFirst($provider) { 
        
        return $this->socialite->driver($provider)->redirect();
        
    }

    private function getSocialUser($provider) {
        
        return $this->socialite->driver($provider)->user();
        
    }

    
}
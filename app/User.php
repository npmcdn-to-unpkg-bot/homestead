<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

    /**
     * The database to be used
     * Users model gets its own database so that a user may be shared across multiple
     * databases/subdomains.
     */
    protected $connection = 'users';
    
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

    public function findByUserNameOrCreate($userData) {
        
        $user = User::where('email', '=', $userData->email)->first();
        
        if(!$user) {
            
            $user = User::create([
                //'provider_id' => $userData->id,
                'name' => $userData->name,
                //'username' => $userData->nickname,
                'email' => $userData->email,
                //'avatar' => $userData->avatar,
                //'active' => 1,
            ]);
        }

        $this->checkIfUserNeedsUpdating($userData, $user);
        return $user;
    }

    public function checkIfUserNeedsUpdating($userData, $user) {

        $socialData = [
            //'avatar' => $userData->avatar,
            'email' => $userData->email,
            'name' => $userData->name,
            //'username' => $userData->nickname,
        ];
        $dbData = [
            //'avatar' => $user->avatar,
            'email' => $user->email,
            'name' => $user->name,
            //'username' => $user->username,
        ];

        if (!empty(array_diff($socialData, $dbData))) {
            //$user->avatar = $userData->avatar;
            $user->email = $userData->email;
            $user->name = $userData->name;
            //$user->username = $userData->nickname;
            $user->save();
        }
        
    }
    
}

<?php

namespace App;

use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Request;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'member_code',
        'fullname', 
        'username', 
        'password', 
        'email', 
        'avatar', 
        'calling_code', 
        'phone', 
        'manage_id',
        'division_id',
        'id_resource', 
        'intergreated_user_id', 
        'skype', 
        'permission', 
        'remember_token', 
        'created_at', 
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    public function sendPasswordResetNotification($token){
        $this->notify(new PasswordReset($token));
    }
    
	
}

<?php

// defining Namespace
namespace App;

// using Laravel Facades
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'date_of_birth', 'avatar', 'description', 'email', 'password',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     /**
     * Create the relation between User and Flat.
     * User -> Flat
     *   1  ->  *
     *
     * @return App\Flat (array)
     */
    public function flats()
    {
        return $this->hasMany('App\Flat');
    }
}

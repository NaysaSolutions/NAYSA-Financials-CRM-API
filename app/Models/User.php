<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UsersDB extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users'; // actual table name

    protected $fillable = [
        'userId',
        'username',
        'email',
        'password',
        'reset_code', // if you're using this for forgot/reset
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

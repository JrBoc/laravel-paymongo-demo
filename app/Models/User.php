<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'sys_users';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function eWalletPayments()
    {
        return $this->hasMany(EWalletPayment::class);
    }

    public function cardPayments()
    {
        return $this->hasMany(CardPayment::class);
    }
}

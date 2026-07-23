<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Using Authenticatable for future portal login

class Owner extends Authenticatable
{
    protected $fillable = ['name', 'email', 'phone', 'password', 'portal_enabled_at'];

    protected $hidden = ['password'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}

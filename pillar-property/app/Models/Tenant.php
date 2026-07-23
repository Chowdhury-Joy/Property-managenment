<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Tenant extends Authenticatable
{
    protected $fillable = ['name', 'email', 'phone', 'password', 'portal_enabled_at'];

    protected $hidden = ['password'];

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
    
    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active');
    }
}

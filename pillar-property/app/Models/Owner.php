<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Owner extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['name', 'email', 'phone', 'password', 'portal_enabled_at'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'portal_enabled_at' => 'datetime',
        ];
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}

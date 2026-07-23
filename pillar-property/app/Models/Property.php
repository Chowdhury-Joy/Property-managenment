<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = ['owner_id', 'name', 'address', 'city', 'state', 'zip', 'type', 'status'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    // Helper to get full address
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->zip}";
    }
}

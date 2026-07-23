<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['property_id', 'name', 'bedrooms', 'bathrooms', 'sqft', 'status'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active');
    }
    
    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
    
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}

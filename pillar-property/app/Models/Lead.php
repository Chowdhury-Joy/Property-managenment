<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'property_address', 'property_type',
        'current_rent', 'reason_for_switching', 'status', 'staff_notes',
    ];
}

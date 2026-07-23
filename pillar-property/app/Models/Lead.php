<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'property_address', 'property_type',
        'current_rent', 'reason_for_switching', 'status', 'staff_notes',
    ];
}

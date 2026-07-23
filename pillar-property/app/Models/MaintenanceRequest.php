<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'unit_id', 'tenant_id', 'vendor_id', 'category', 'description',
        'photo_path', 'urgency', 'status', 'cost', 'resolved_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}

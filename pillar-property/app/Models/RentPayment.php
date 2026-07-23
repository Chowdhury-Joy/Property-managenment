<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentPayment extends Model
{
    use SoftDeletes;

    protected $fillable = ['lease_id', 'amount', 'due_date', 'paid_date', 'status', 'method_note'];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
}

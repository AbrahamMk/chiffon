<?php

namespace App\Domains\CustomOrders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomOrderMeasurement extends Model
{
    protected $fillable = [
        'custom_order_id',
        'name',
        'value',
        'unit',
    ];

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }
}

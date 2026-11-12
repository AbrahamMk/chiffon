<?php

namespace App\Domains\CustomOrders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomOrderImage extends Model
{
    protected $fillable = [
        'custom_order_id',
        'path',
        'caption',
    ];

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }
}

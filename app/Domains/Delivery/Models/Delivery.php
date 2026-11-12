<?php

namespace App\Domains\Delivery\Models;

use App\Domains\Orders\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'delivery_number',
        'order_id',
        'delivery_zone_id',
        'assigned_to',
        'status',
        'customer_name',
        'customer_phone',
        'city',
        'subcity',
        'address_line',
        'delivery_fee',
        'scheduled_date',
        'delivered_at',
        'proof_path',
        'failed_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
            'scheduled_date' => 'date',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}

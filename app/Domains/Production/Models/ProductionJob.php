<?php

namespace App\Domains\Production\Models;

use App\Domains\CustomOrders\Models\CustomOrder;
use App\Domains\Orders\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionJob extends Model
{
    protected $fillable = [
        'job_number',
        'custom_order_id',
        'order_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'status',
        'expected_completion_date',
        'quantity',
        'completed_quantity',
        'is_delayed',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_completion_date' => 'date',
            'is_delayed' => 'boolean',
        ];
    }

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function jobStages(): HasMany
    {
        return $this->hasMany(ProductionJobStage::class);
    }
}

<?php

namespace App\Domains\CustomOrders\Models;

use App\Domains\Customers\Models\Customer;
use App\Domains\Production\Models\ProductionJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'customer_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'clothing_type',
        'fabric_preference',
        'color_preference',
        'design_description',
        'quantity',
        'required_date',
        'additional_instructions',
        'status',
        'quoted_amount',
        'deposit_amount',
        'deposit_paid',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'required_date' => 'date',
            'quoted_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'deposit_paid' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(CustomOrderMeasurement::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CustomOrderImage::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function productionJob(): HasOne
    {
        return $this->hasOne(ProductionJob::class);
    }
}

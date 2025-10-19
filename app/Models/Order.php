<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency_id',
        'foreign_currency_amount',
        'exchange_rate_at_purchase',
        'surcharge_percentage_at_purchase',
        'discount_percentage_at_purchase',
        'surcharge_amount_in_zar',
        'discount_amount_in_zar',
        'total_zar_amount_paid',
    ];

    /**
     * Get the currency associated with the order.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}

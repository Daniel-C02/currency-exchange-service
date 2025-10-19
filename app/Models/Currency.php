<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'exchange_rate',
        'surcharge_percentage',
        'discount_percentage',
    ];

    /**
     * Get the orders for the currency.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

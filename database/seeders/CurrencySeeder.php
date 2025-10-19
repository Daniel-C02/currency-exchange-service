<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'name' => 'United States Dollar',
                'exchange_rate' => 0.0808279,
                'surcharge_percentage' => 7.5,
                'discount_percentage' => 0.00,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'exchange_rate' => 0.0527032,
                'surcharge_percentage' => 5.0,
                'discount_percentage' => 0.00,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'exchange_rate' => 0.0718710,
                'surcharge_percentage' => 5.0,
                'discount_percentage' => 2.00,
            ],
            [
                'code' => 'KES',
                'name' => 'Kenyan Shilling',
                'exchange_rate' => 7.81498,
                'surcharge_percentage' => 2.5,
                'discount_percentage' => 0.00,
            ],
        ];

        foreach ($currencies as $currency) {
            // Using updateOrCreate to prevent duplicates if the seeder is run multiple times
            Currency::updateOrCreate(['code' => $currency['code']], $currency);
        }
    }
}

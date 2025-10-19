<?php

namespace App\Options;

class CurrencyOptions
{
    /**
     * Get the available interest options.
     *
     * @return array
     */
    public static function get(): array
    {
        return [
            'USD',
            'GBP',
            'EUR',
            'KES',
        ];
    }
}

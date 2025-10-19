<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:fetch-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches the latest currency exchange rates from the CurrencyLayer API and updates the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching latest currency exchange rates...');

        // -------------------------------------------------------------------------------------------------------------
        // --- 1. Get the API access key from the config file.
        // -------------------------------------------------------------------------------------------------------------

        $accessKey = config('services.currency_layer.access_key');

        if (!$accessKey) {
            $this->error('CurrencyLayer API access key is not configured in config/services.php.');
            Log::error('CurrencyLayer API access key is missing.');
            return Command::FAILURE;
        }

        // -------------------------------------------------------------------------------------------------------------
        // --- 2. Make the HTTP request to the CurrencyLayer API.
        // -------------------------------------------------------------------------------------------------------------

        $response = Http::get('http://apilayer.net/api/live', [
            'access_key' => $accessKey,
            'currencies' => 'USD,GBP,EUR,KES',
            'source'     => 'ZAR',
            'format'     => 1,
        ]);

        // -------------------------------------------------------------------------------------------------------------
        // --- 3. Handle potential API errors.
        // -------------------------------------------------------------------------------------------------------------

        if ($response->failed()) {
            $this->error('Failed to connect to the CurrencyLayer API.');
            Log::error('CurrencyLayer API request failed.', ['status' => $response->status()]);
            return Command::FAILURE;
        }

        $data = $response->json();

        if (!$data || !isset($data['success']) || $data['success'] !== true) {
            $errorMessage = $data['error']['info'] ?? 'An unknown error occurred with the CurrencyLayer API.';
            $this->error($errorMessage);
            Log::error('CurrencyLayer API returned an error.', ['response' => $data]);
            return Command::FAILURE;
        }

        // -------------------------------------------------------------------------------------------------------------
        // --- 4. Process the successful response and update the database.
        // -------------------------------------------------------------------------------------------------------------

        $quotes = $data['quotes'];
        $this->info('Successfully fetched rates. Now updating database...');

        foreach ($quotes as $pair => $rate) {
            // The API returns pairs like "ZARUSD", "ZARGBP". We need to extract the target currency code.
            $currencyCode = substr($pair, 3);

            $currency = Currency::where('code', $currencyCode)->first();

            if ($currency) {
                $currency->update(['exchange_rate' => $rate]);
                $this->line("Updated <info>{$currencyCode}</info> exchange rate to <info>{$rate}</info>");
            } else {
                $this->warn("Currency with code '{$currencyCode}' not found in the database. Skipping.");
            }
        }

        $this->info('All currency rates have been updated successfully.');
        return Command::SUCCESS;
    }
}

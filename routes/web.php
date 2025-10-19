<?php

use App\Http\Controllers\CurrencyApiController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\JsonDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Return the main Currency Exchange page
Route::get('/', [CurrencyController::class, 'index'])
    ->name('exchange');

// Post method to place a currency conversion order
Route::post('/', [CurrencyController::class, 'placeOrder'])
    ->name('exchange.order');

// Route to fetch all currency data
Route::get('/api/currencies', [CurrencyApiController::class, 'index']);

// JSON data routes
Route::prefix('json')->name('json.')->group(function () {
    Route::get('/currencies', [JsonDataController::class, 'getCurrencies'])->name('currencies');
    Route::get('/orders', [JsonDataController::class, 'getOrders'])->name('orders');
});

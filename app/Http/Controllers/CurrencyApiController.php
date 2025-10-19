<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyApiController extends Controller
{
    /**
     * Handle the incoming request to fetch all currencies.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Fetch all currencies from the database
        $currencies = Currency::all();

        // Return the collection as a JSON response
        return response()->json($currencies);
    }
}

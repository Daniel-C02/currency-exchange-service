<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class JsonDataController extends Controller
{
    /**
     * Display a listing of all currencies.
     *
     * @return JsonResponse
     */
    public function getCurrencies(): JsonResponse
    {
        $currencies = Currency::all();
        return response()->json($currencies);
    }

    /**
     * Display a listing of all orders.
     *
     * @return JsonResponse
     */
    public function getOrders(): JsonResponse
    {
        // Eager load the currency relationship
        $orders = Order::with('currency')->get();
        return response()->json($orders);
    }
}

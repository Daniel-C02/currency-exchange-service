<?php

namespace App\Http\Controllers;

use App\Mail\OrderDetailsEmail;
use App\Models\Currency;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    /**
     * Display the currency exchange page.
     */
    public function index(): View
    {
        $currencyOptions = \App\Options\CurrencyOptions::get();

        return view('pages.currency-exchange', compact('currencyOptions'));
    }

    /**
     * Handle the submission of a new currency exchange order.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function placeOrder(Request $request): \Illuminate\Http\JsonResponse
    {
        //--------------------------------------------------------------------------------------------------------------
        // --- 1. VALIDATION
        //--------------------------------------------------------------------------------------------------------------

        $validator = Validator::make($request->all(), [
            'currency_option' => 'required|string|exists:currencies,code',
            // Ensure at least one amount is provided, and it's a positive number
            'foreign_currency_amount' => 'nullable|required_without:local_currency_amount|numeric|gt:0',
            'local_currency_amount' => 'nullable|required_without:foreign_currency_amount|numeric|gt:0',
        ]);

        // Get validated data
        // If validation fails, Laravel's default behavior when expecting JSON is to throw a
        // ValidationException, which is automatically caught and formatted as a 422 JSON response.
        $validatedData = $validator->validated();

        //--------------------------------------------------------------------------------------------------------------
        // --- 2. SETUP & CALCULATION
        //--------------------------------------------------------------------------------------------------------------

        try {
            DB::beginTransaction();

            $selectedCurrency = Currency::where('code', $validatedData['currency_option'])->firstOrFail();

            $foreignAmount = $validatedData['foreign_currency_amount'];
            $localAmount = $validatedData['local_currency_amount'];

            // Server-side recalculation to ensure data integrity
            if ($foreignAmount) {
                // Calculate ZAR based on foreign amount
                $baseZarAmount = $foreignAmount / $selectedCurrency->exchange_rate;
            } else {
                // Calculate ZAR based on local amount
                $totalToPay = $localAmount;
                $baseZarAmount = $totalToPay / (1 + ($selectedCurrency->surcharge_percentage / 100));
                // Back-calculate foreign amount
                $foreignAmount = $baseZarAmount * $selectedCurrency->exchange_rate;
            }

            // Calculate the surcharge amount
            $surchargeAmount = $baseZarAmount * ($selectedCurrency->surcharge_percentage / 100);
            $totalPayableZar = $baseZarAmount + $surchargeAmount;

            //----------------------------------------------------------------------------------------------------------
            // --- 3. CREATE THE ORDER
            //----------------------------------------------------------------------------------------------------------

            $order = Order::create([
                'currency_id' => $selectedCurrency->id,
                'foreign_currency_amount' => $foreignAmount,
                'exchange_rate_at_purchase' => $selectedCurrency->exchange_rate,
                'surcharge_percentage_at_purchase' => $selectedCurrency->surcharge_percentage,
                'discount_percentage_at_purchase' => $selectedCurrency->discount_percentage,
                'surcharge_amount_in_zar' => $surchargeAmount,
                'total_zar_amount_paid' => $totalPayableZar,
                'discount_amount_in_zar' => 0, // Default to 0
            ]);

            //----------------------------------------------------------------------------------------------------------
            // --- 4. HANDLE SPECIAL ACTIONS
            //----------------------------------------------------------------------------------------------------------

            // Apply a 2% discount on the total order amount, this is configurable for the EUR currency
            // and saved separately on an order. This is not included in the final currency calculation.
            if ($selectedCurrency->code === 'EUR') {
                $discountAmount = $totalPayableZar * ($selectedCurrency->discount_percentage / 100);
                $order->discount_amount_in_zar = $discountAmount;
                $order->save();
            }

            // Send email with order details for the GBP currency.
            elseif ($selectedCurrency->code === 'GBP') {
                Mail::to('02.christensendaniel@gmail.com')->send(new OrderDetailsEmail($order));
                Log::info("Order #{$order->id} placed for GBP. Email notification has been sent.");
            }

            // After all actions have successfully passed, commit changes to the database.
            DB::commit();

            //----------------------------------------------------------------------------------------------------------
            // --- 5. Return back to the user with either a success or error response
            //----------------------------------------------------------------------------------------------------------

            // Return success response
            return response()->json([
                'title'   => 'Thank You!',
                'message' => "Order successfully placed! You purchased {$order->foreign_currency_amount} {$selectedCurrency->code}."
            ], 200); // OK status

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed: ' . $e->getMessage());

            // Return a generic 500 error to the client
            return response()->json([
                'message' => 'We encountered an issue while processing your order. Please try again later.'
            ], 500); // Internal Server Error
        }
    }
}

<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            {{ config('app.name') }}
        </x-mail::header>
    </x-slot:header>

    # Your Order Confirmation

    Hi there,

    Thank you for your recent currency exchange order. Here are the details of your transaction:

    <x-slot:subcopy>
        <x-mail::panel>
            You purchased **{{ number_format($order->foreign_currency_amount, 2) }} {{ $order->currency->code }}**.
        </x-mail::panel>

        <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
            <tr>
                <td><strong>Order ID</strong></td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td><strong>Date of Purchase</strong></td>
                <td>{{ $order->created_at->format('F j, Y, g:i a') }}</td>
            </tr>
            <tr>
                <td><strong>Currency Purchased</strong></td>
                <td>{{ $order->currency->code }}</td>
            </tr>
            <tr>
                <td><strong>Amount (Foreign)</strong></td>
                <td>{{ number_format($order->foreign_currency_amount, 2) }} {{ $order->currency->code }}</td>
            </tr>
            <tr>
                <td><strong>Exchange Rate</strong></td>
                <td>1 ZAR = {{ $order->exchange_rate_at_purchase }} {{ $order->currency->code }}</td>
            </tr>
            <tr>
                <td><strong>Surcharge</strong></td>
                <td>{{ $order->surcharge_percentage_at_purchase }}%</td>
            </tr>
            <tr>
                <td><strong>Surcharge Amount</strong></td>
                <td>{{ number_format($order->surcharge_amount_in_zar, 2) }} ZAR</td>
            </tr>
            <tr>
                <td><strong>Total Paid</strong></td>
                <td><strong>{{ number_format($order->total_zar_amount_paid, 2) }} ZAR</strong></td>
            </tr>
        </table>

        <x-mail::subcopy>
            If you have any questions about your order, please don't hesitate to contact us.
            <br><br>
            Thanks,<br>
            {{ config('app.name') }}
        </x-mail::subcopy>
    </x-slot:subcopy>

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            © {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>

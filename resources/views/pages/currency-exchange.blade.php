@extends('layouts.app')

@section('content')

    <div id="s_currency_exchange">
        <!-- Spacing | 2.25 _ 4.5 _ 6 -->
        <div class="pb-9 pb-sm-11 pb-lg-12"></div>

        <!-- Section Start -->
        <div class="container-xxl">

            <!-- Page title -->
            <h3 class="text-primary-5 text-center pb-8">
                Currency Exchange Service
            </h3>

            <form
                {{-- id 'currency_exchange_form': Target for the _currency_excahneg.js script to attach to --}}
                id="currency_exchange_form"
                {{-- class 'submission_form': Target for the global _form_submition.js script to attach to --}}
                class="submission_form d-flex justify-content-center"
                {{-- route to perform backend validation and submition logic --}}
                data-post-to="{{ route('exchange.order') }}"
            >
                @csrf

                <table>
                    <tbody>
                    <!-- Foreign currency selection -->
                    <tr>
                        <td class="pe-5">
                            <div class="fs-16 mb-3">
                                <span class="text-primary-5">Select</span>
                                <span class="text-secondary-5">A Foreign Currency</span>
                            </div>
                        </td>
                        <td class="py-2">
                            <x-forms.select-input
                                name="currency_option"
                                :required="true"
                                :options="$currencyOptions"
                            />
                        </td>
                    </tr>

                    <!-- Amount of foreign currency input -->
                    <tr>
                        <td class="pe-5">
                            <div class="fs-16 mb-3">
                                <span class="text-primary-5">Enter</span>
                                <span class="text-secondary-5">Amount to Receive</span>
                            </div>
                        </td>
                        <td class="py-2">
                            <x-forms.input
                                name="foreign_currency_amount"
                                type="number"
                                placeholder="Enter foreign amount"
                            />
                        </td>
                    </tr>

                    <!-- Amount of ZAR currency input -->
                    <tr>
                        <td class="pe-5">
                            <div class="fs-16 mb-3">
                                <span class="text-primary-5">Enter</span>
                                <span class="text-secondary-5">Amount to Pay (ZAR)</span>
                            </div>
                        </td>
                        <td class="py-2">
                            <x-forms.input
                                name="local_currency_amount"
                                type="number"
                                placeholder="Enter ZAR amount"
                            />
                        </td>
                    </tr>

                    <tr>
                        <td class="pt-4"></td>
                    </tr>

                    <!-- Amount Payable -->
                    <tr>
                        <td class="pe-5">
                            <h5>
                                <span class="text-secondary-5">Total Payable (ZAR)</span>
                            </h5>
                        </td>
                        <td class="py-2">
                            <x-forms.input
                                name="amount_payable"
                                type="number"
                                placeholder="Payable"
                                :disabled="true"
                            />
                        </td>
                    </tr>

                    <!-- Amount Receivable -->
                    <tr>
                        <td class="pe-5">
                            <h5>
                                <span id="receivable_label" class="text-secondary-5">Receivable (N/A)</span>
                            </h5>
                        </td>
                        <td class="py-2">
                            <x-forms.input
                                name="amount_receivable"
                                type="number"
                                placeholder="Receivable"
                                :disabled="true"
                            />
                        </td>
                    </tr>

                    <!-- Surcharge Amount -->
                    <tr>
                        <td class="pe-5">
                            <h5>
                                <span class="text-secondary-5">Surcharge (ZAR)</span>
                            </h5>
                        </td>
                        <td class="py-2">
                            <x-forms.input
                                name="surcharge_amount"
                                type="number"
                                placeholder="Surcharge"
                                :disabled="true"
                            />
                        </td>
                    </tr>

                    <!-- Place order submit button -->
                    <tr>
                        <td></td>
                        <td class="d-flex justify-content-end pt-8">
                            <button id="submit_button" type="submit" class="btn btn-primary" disabled>
                                Place Order
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </form>
        </div>

        <!-- Spacing | 6.5 _ 7.5 _ 10 -->
        <div class="pb-12 mb-4 pb-sm-12 mb-sm-8 pb-lg-15 mb-lg-0"></div>
    </div>

@endsection

{{--@push('scripts')--}}
{{--    --}}{{-- Script to handle server-side feedback via SweetAlert --}}
{{--    <script>--}}
{{--        // Wait for sweetalert to register.--}}
{{--        window.addEventListener('sweetalert-registered', function(event) {--}}
{{--            // Declare sweetalert method.--}}
{{--            const fireSweetalert = event.detail.fireSweetalert;--}}

{{--            // If the session has returned back with a success, show that to the user--}}
{{--            @if(session('success'))--}}
{{--                fireSweetalert('success', 'Success!', `{{ session('success') }}`);--}}
{{--            @endif--}}

{{--            // If the session has returned back with an error, show that to the user--}}
{{--            @if(session('error'))--}}
{{--                // Construct a list of validation errors if they exist--}}
{{--                let errorHtml = `{{ session('error') }}`;--}}
{{--                @if ($errors->any())--}}
{{--                    errorHtml += '<ul class="text-start mt-2">';--}}
{{--                    @foreach ($errors->all() as $error)--}}
{{--                        errorHtml += '<li>{{ $error }}</li>';--}}
{{--                    @endforeach--}}
{{--                        errorHtml += '</ul>';--}}
{{--                @endif--}}
{{--                    // Display the error to the user--}}
{{--                fireSweetalert('error', 'Error!', errorHtml);--}}
{{--            @endif--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}

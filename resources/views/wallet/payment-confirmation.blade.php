@extends('layouts.app')
@section('title', 'Confirm Payment')

@push('styles')
    <style>
        .payment-confirm-container {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 2rem auto;
        }
        .payment-confirm-title {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .confirm-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            margin-top: 1.5rem;
        }
        .btn-mada {
            background-color: #ff7f0e;
            border: none;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .btn-mada:hover {
            background-color: #e66a07;
        }
        .btn-cancel {
            background-color: #6c757d;
            border: none;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="payment-confirm-container">
            <h4>PAYMENT THRU MADA / الدفع من خلال مدى</h4>
            <p class="payment-confirm-title">
                Are you sure you want to continue with your payment?
            </p>

            <div class="confirm-actions">
                <!-- Continue button; adjust the form action and add hidden fields as needed -->
                <form action="{{ route('wallet.paymentProcess') }}" method="POST">
                    @csrf
                    {{-- If you want to pass the top-up amount, make sure to pass it from your controller.
                        For now, we have removed it to avoid the undefined variable error. --}}
                    <!-- <input type="hidden" name="amount" value="{{ $amount }}"> -->
                    <button type="submit" class="btn-mada">Continue With Payment</button>
                </form>
                <!-- Cancel button -->
                <a href="{{ route('wallet.topup') }}" class="btn-cancel">Cancel</a>
            </div>
        </div>
    </div>
@endsection

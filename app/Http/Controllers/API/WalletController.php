<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function recharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'payment_type' => 'required|in:credit_card,bank_transfer',
            'amount' => 'required|numeric|min:1',
            'file' => 'required_if:payment_type,bank_transfer',
            'notes' => 'nullable'
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = Payment::create([
            'user_id' => $request->user_id,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'file' => $request->file,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        if ($request->payment_type == 'credit_card'){
            // Integrate with credit card API here
            $payment->update(['status' => 'approved']);
            $wallet = Wallet::where('user_id', $request->user_id)->first();
            $wallet->balance += $request->amount;
            $wallet->save();
        }

        return response()->json(['message' => 'Recharge request submitted', 'payment' => $payment]);
    }
}

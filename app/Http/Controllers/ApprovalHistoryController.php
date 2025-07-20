<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletApprovalStep;

class ApprovalHistoryController extends Controller
{
    /**
     * Display the common approval history for internal users with filters.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = WalletApprovalStep::with([
            'request.payment.user',
            'request.workflow'
        ])
        ->where('user_id', $user->id)
        ->when($request->date_from, fn($q, $date) => $q->whereHas('request', fn($qr) => $qr->whereDate('created_at', '>=', $date)))
        ->when($request->date_to, fn($q, $date) => $q->whereHas('request', fn($qr) => $qr->whereDate('created_at', '<=', $date)))
        ->when($request->status, fn($q, $status) => $q->where('status', $status))
        ->when($request->type, fn($q, $type) => $q->whereHas('request.payment', fn($qr) => $qr->where('payment_type', $type)))
        ->when($request->email, fn($q, $email) => $q->whereHas('request.payment.user', fn($qr) => $qr->where('email', 'like', "%{$email}%")))
        ->orderByDesc('created_at');

        $steps = $query->paginate(10)->appends($request->all());

        return view('wallet.approvals.common-history', compact('steps'));
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\StatusHistory;
use App\Models\Wallet;
use App\Models\WalletApprovalRequest;
use App\Models\ServiceBooking;
use App\Services\StatusTransitionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatusHistoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display status history for a model.
     *
     * @param Request $request
     * @param string $modelType
     * @param string $modelId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $modelType, string $modelId)
    {
        // Map model type to actual model class
        $modelClassMap = [
            'wallet' => Wallet::class,
            'wallet_approval' => WalletApprovalRequest::class,
            'service_booking' => ServiceBooking::class,
            // Add other model mappings as needed
        ];

        if (!isset($modelClassMap[$modelType])) {
            return response()->json(['error' => 'Invalid model type'], 400);
        }

        $modelClass = $modelClassMap[$modelType];

        // Find the model
        $model = $modelClass::find($modelId);
        if (!$model) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        // Check permissions - users can only see history for items they have access to
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if user is owner of the model
        $isOwner = false;
        if (property_exists($model, 'user_id')) {
            $isOwner = $currentUser->id === $model->user_id;
        }
        
        // For now we're keeping it simple - authorize if user owns the model or has an admin-related role
        // In a real application, this would be handled by Laravel Policies
        $isAuthorized = $isOwner;
        if (!$isAuthorized) {
            // Check if the user has admin role by directly querying the roles table
            $adminRoles = DB::table('roles')
                ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('model_has_roles.model_id', $currentUser->id)
                ->where('model_has_roles.model_type', get_class($currentUser))
                ->whereIn('roles.name', ['admin', 'super-admin'])
                ->count();
                
            $isAuthorized = $adminRoles > 0;
        }
        
        if (!$isAuthorized) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get history entries
        $historyEntries = StatusHistory::where([
            'model_id' => $modelId,
            'model_type' => $modelClass,
        ])->with('user:id,name,email')->orderBy('created_at', 'desc')->get();

        // Format the history data for display
        $formattedHistory = $historyEntries->map(function ($entry) {
            return [
                'previous_status' => $entry->previous_status,
                'new_status' => $entry->new_status,
                'comment' => $entry->comment,
                'user' => $entry->user ? [
                    'name' => $entry->user->name,
                    'email' => $entry->user->email,
                ] : null,
                'changed_at' => $entry->created_at->format('Y-m-d H:i:s'),
                'metadata' => $entry->metadata,
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'history' => $formattedHistory,
            ]);
        }

        return view('status.history', [
            'modelType' => $modelType,
            'modelId' => $modelId,
            'model' => $model,
            'history' => $formattedHistory,
        ]);
    }
} 
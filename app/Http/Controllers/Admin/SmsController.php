<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

final class SmsController extends Controller
{
    public function __construct(
        private readonly SmsService $smsService
    ) {}

    /**
     * Display SMS management dashboard
     */
    public function index(): View
    {
        $statistics = $this->smsService->getStatistics();
        $configTest = $this->smsService->testConnection();
        
        return view('admin.sms.index', compact('statistics', 'configTest'));
    }

    /**
     * Test SMS configuration
     */
    public function testConfig(): JsonResponse
    {
        $result = $this->smsService->testConnection();
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? []
        ]);
    }

    /**
     * Send test SMS
     */
    public function sendTest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|min:10|max:15',
            'message' => 'required|string|max:160'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->smsService->sendSms(
            $request->input('mobile'),
            $request->input('message')
        );

        return response()->json($result);
    }

    /**
     * Get SMS statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->smsService->getStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get SMS configuration status
     */
    public function configStatus(): JsonResponse
    {
        $config = config('sms');
        
        $status = [
            'provider' => 'connectsaudi',
            'configured' => false,
            'missing_fields' => []
        ];

        $requiredFields = ['username', 'password', 'sender_id'];

        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                $status['missing_fields'][] = $field;
            }
        }

        $status['configured'] = empty($status['missing_fields']);

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
} 
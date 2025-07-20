<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class SendSmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $mobile,
        protected string $message,
        protected string $purpose = 'general'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        try {
            $result = $smsService->sendSms($this->mobile, $this->message);
            
            Log::info('SMS sent successfully', [
                'mobile' => $this->mobile,
                'purpose' => $this->purpose
            ]);
        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'mobile' => $this->mobile,
                'purpose' => $this->purpose,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // If we've tried the maximum number of times, log a final failure
            if ($this->attempts() === $this->tries) {
                Log::critical('SMS sending failed permanently', [
                    'mobile' => $this->mobile,
                    'purpose' => $this->purpose,
                    'final_error' => $e->getMessage()
                ]);
            }

            throw $e;
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return ['sms', "purpose:{$this->purpose}", "mobile:{$this->mobile}"];
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SMS job failed finally', [
            'mobile' => $this->mobile,
            'purpose' => $this->purpose,
            'error' => $exception->getMessage()
        ]);
    }
}

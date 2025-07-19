<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailNotification implements ShouldQueue
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
        protected string $email,
        protected string $subject,
        protected string $template,
        protected array $data,
        protected string $purpose = 'general'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send($this->template, $this->data, function($message) {
                $message->to($this->email)
                        ->subject($this->subject)
                        ->bcc(env('MAIL_BCC_ADDRESS', 'usman.nawaz@aljeri.com'));
            });

            Log::info('Email sent successfully', [
                'email' => $this->email,
                'purpose' => $this->purpose,
                'template' => $this->template
            ]);
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'email' => $this->email,
                'purpose' => $this->purpose,
                'template' => $this->template,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // If we've tried the maximum number of times, log a final failure
            if ($this->attempts() === $this->tries) {
                Log::critical('Email sending failed permanently', [
                    'email' => $this->email,
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
        return ['email', "purpose:{$this->purpose}", "recipient:{$this->email}"];
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Email job failed finally', [
            'email' => $this->email,
            'purpose' => $this->purpose,
            'error' => $exception->getMessage()
        ]);
    }
}

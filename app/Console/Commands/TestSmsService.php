<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

final class TestSmsService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {mobile?} {--message=Test SMS from JoilYaseeir} {--provider=} {--config-only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS service configuration and send test SMS';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService): int
    {
        $this->info('Testing SMS Service...');
        $this->newLine();

        // Test configuration first
        $configTest = $smsService->testConnection();
        
        if (!$configTest['success']) {
            $this->error('SMS Configuration Test Failed:');
            $this->error($configTest['message']);
            return self::FAILURE;
        }
        
        $this->info('✓ SMS Configuration is valid');
        
        // If config-only flag is set, just test configuration
        if ($this->option('config-only')) {
            $this->info('Configuration test completed successfully.');
            return self::SUCCESS;
        }

        // Get mobile number
        $mobile = $this->argument('mobile');
        if (!$mobile) {
            $mobile = $this->ask('Enter mobile number to send test SMS');
        }

        if (!$mobile) {
            $this->error('Mobile number is required for SMS testing');
            return self::FAILURE;
        }

        // Get message
        $message = $this->option('message');
        
        $this->info("Sending test SMS to: {$mobile}");
        $this->info("Message: {$message}");
        
        // Send test SMS
        $result = $smsService->sendSms($mobile, $message);
        
        if ($result['success']) {
            $this->info('✓ SMS sent successfully!');
            $this->info('Response: ' . json_encode($result['data'], JSON_PRETTY_PRINT));
        } else {
            $this->error('✗ SMS sending failed:');
            $this->error($result['message']);
            if (!empty($result['data'])) {
                $this->error('Details: ' . json_encode($result['data'], JSON_PRETTY_PRINT));
            }
            return self::FAILURE;
        }

        // Show statistics
        $stats = $smsService->getStatistics();
        $this->newLine();
        $this->info('SMS Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Sent', $stats['sent']],
                ['Total Failed', $stats['failed']],
                ['Total Pending', $stats['pending']],
                ['Total Records', $stats['total']],
                ['Success Rate', $stats['success_rate'] . '%'],
                ['Today Sent', $stats['today_sent']],
                ['Today Failed', $stats['today_failed']],
            ]
        );

        return self::SUCCESS;
    }
} 
<?php

namespace App\Logging;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class DatabaseHandler extends AbstractProcessingHandler
{
    /**
     * Handle the log record
     * 
     * @param array|LogRecord $record
     */
    protected function write($record): void
    {
        // Convert LogRecord to array if needed
        if ($record instanceof LogRecord) {
            $record = $record->toArray();
        }
        
        // Determine if we should log this record to the database
        if (!$this->shouldLogToDatabase($record)) {
            return;
        }
        
        $data = [
            'level' => $record['level_name'],
            'description' => $record['message'],
            'log_name' => $record['channel'],
            'event' => $record['context']['type'] ?? null,
            'properties' => $record['context'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        
        // Add causer (user) data
        if (Auth::check()) {
            $data['causer_id'] = Auth::id();
            $data['causer_type'] = get_class(Auth::user());
        }
        
        // Add subject data if available
        if (isset($record['context']['subject'])) {
            $subject = $record['context']['subject'];
            $data['subject_id'] = $subject->getKey();
            $data['subject_type'] = get_class($subject);
        }
        
        ActivityLog::create($data);
    }
    
    /**
     * Determine if this log record should be stored in the database
     * 
     * @param array|LogRecord $record
     * @return bool
     */
    protected function shouldLogToDatabase($record): bool
    {
        // Convert LogRecord to array if needed
        if ($record instanceof LogRecord) {
            $record = $record->toArray();
        }
        
        // By default, store logs with a specified type
        if (isset($record['context']['type'])) {
            return true;
        }
        
        // Store logs with a specified database flag
        if (isset($record['context']['database']) && $record['context']['database'] === true) {
            return true;
        }
        
        // Optionally, store all logs of certain levels (errors, etc.)
        $levelName = $record['level_name'] ?? '';
        $criticalLevels = ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'];
        
        return in_array($levelName, $criticalLevels);
    }
}

class DatabaseLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('database');
        $logger->pushHandler(new DatabaseHandler());
        
        return $logger;
    }
} 
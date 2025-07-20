@echo off
TITLE JOil Yaseeir Online Queue Worker

:: Set the path to your PHP executable
SET PHP_PATH=C:\xampp81\php_832\php.exe

:: Set the path to your Laravel project
SET PROJECT_PATH=%~dp0

:: Set environment variables
SET QUEUE_CONNECTION=database
SET QUEUE_DRIVER=database

ECHO Starting Queue Worker...
ECHO Press Ctrl+C to stop the worker

:: Start the queue worker with specific options
%PHP_PATH% %PROJECT_PATH%artisan queue:work ^
    --queue=high,default,low ^  --memory=512 ^
    --sleep=3 ^
    --tries=3 ^
    --backoff=10 ^
    --timeout=350

:: If the worker stops, pause to show any error messages
pause 
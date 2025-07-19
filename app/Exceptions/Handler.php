<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Custom error handling for web requests
        if ($request->expectsJson()) {
            return parent::render($request, $e);
        }

        // Handle specific HTTP exceptions
        if ($this->isHttpException($e)) {
            // Get status code safely, default to 500 if not available
            $statusCode = 500;
            
            // Try to get status code from exception code
            $exceptionCode = $e->getCode();
            if ($exceptionCode > 0 && $exceptionCode < 600) {
                $statusCode = $exceptionCode;
            }
            
            // Custom error pages for specific status codes
            switch ($statusCode) {
                case 403:
                    return response()->view('errors.403', [
                        'exception' => $e,
                        'title' => __('Access Denied'),
                        'message' => __('Sorry, you do not have permission to access this page.'),
                        'icon' => 'fas fa-shield-alt',
                        'code' => '403',
                        'accentColor' => '#dc3545',
                        'iconColor' => '#dc3545',
                        'codeColor' => '#0061f2',
                        'animation' => 'pulse'
                    ], 403);
                    
                case 404:
                    return response()->view('errors.404', [
                        'exception' => $e,
                        'title' => __('Page Not Found'),
                        'message' => __('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.'),
                        'icon' => 'fas fa-search',
                        'code' => '404',
                        'accentColor' => '#ffc107',
                        'iconColor' => '#ffc107',
                        'codeColor' => '#0061f2',
                        'animation' => 'bounce'
                    ], 404);
                    
                case 500:
                    return response()->view('errors.500', [
                        'exception' => $e,
                        'title' => __('Internal Server Error'),
                        'message' => __('Something went wrong on our end. Our team has been notified and is working to fix the issue.'),
                        'icon' => 'fas fa-exclamation-triangle',
                        'code' => '500',
                        'accentColor' => '#dc3545',
                        'iconColor' => '#dc3545',
                        'codeColor' => '#dc3545',
                        'animation' => 'shake'
                    ], 500);
                    
                default:
                    // Use generic error layout for other status codes
                    return response()->view('errors.layout', [
                        'exception' => $e,
                        'title' => __('Error ' . $statusCode),
                        'message' => $e->getMessage() ?: __('An error occurred while processing your request.'),
                        'icon' => 'fas fa-exclamation-circle',
                        'code' => (string) $statusCode,
                        'accentColor' => '#6c757d',
                        'iconColor' => '#6c757d',
                        'codeColor' => '#0061f2',
                        'animation' => 'pulse'
                    ], $statusCode);
            }
        }

        // Handle other exceptions
        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        // Production error page for unhandled exceptions
        return response()->view('errors.500', [
            'exception' => $e,
            'title' => __('Something Went Wrong'),
            'message' => __('An unexpected error occurred. Please try again later.'),
            'icon' => 'fas fa-exclamation-triangle',
            'code' => '500',
            'accentColor' => '#dc3545',
            'iconColor' => '#dc3545',
            'codeColor' => '#dc3545',
            'animation' => 'shake'
        ], 500);
    }
}

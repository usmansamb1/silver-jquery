<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ApprovalWorkflowController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('approval-workflows', ApprovalWorkflowController::class);
    Route::post('approval-workflows/{approvalWorkflow}/activate', [ApprovalWorkflowController::class, 'activate'])
        ->name('approval-workflows.activate');
    Route::post('approval-workflows/{approvalWorkflow}/deactivate', [ApprovalWorkflowController::class, 'deactivate'])
        ->name('approval-workflows.deactivate');
}); 
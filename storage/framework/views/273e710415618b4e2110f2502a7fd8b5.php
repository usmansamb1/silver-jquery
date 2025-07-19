

<?php $__env->startSection('title', __('Server Error - JOIL YASEEIR')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .error-page {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .error-container {
        text-align: center;
        max-width: 600px;
        padding: 3rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .error-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #dc3545, #c82333);
    }
    
    .error-icon {
        font-size: 8rem;
        color: #dc3545;
        margin-bottom: 1rem;
        animation: shake 2s infinite;
    }
    
    .error-code {
        font-size: 4rem;
        font-weight: 700;
        color: #dc3545;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .error-title {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    
    .error-message {
        font-size: 1.1rem;
        color: #6c757d;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    
    .error-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-error {
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary-error {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .btn-primary-error:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
        color: white;
    }
    
    .btn-secondary-error {
        background: #f8f9fa;
        color: #6c757d;
        border: 2px solid #e9ecef;
    }
    
    .btn-secondary-error:hover {
        background: #e9ecef;
        color: #495057;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .error-help {
        margin-top: 2rem;
        padding: 1.5rem;
        background: #fff3cd;
        border-radius: 10px;
        border-left: 4px solid #ffc107;
    }
    
    .error-help h5 {
        color: #856404;
        margin-bottom: 1rem;
    }
    
    .error-help ul {
        text-align: left;
        color: #856404;
        margin-bottom: 0;
    }
    
    .error-help li {
        margin-bottom: 0.5rem;
    }
    
    .retry-button {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }
    
    .retry-button:hover {
        background: linear-gradient(135deg, #20c997, #17a2b8);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .error-container {
        animation: fadeInUp 0.6s ease-out;
    }
    
    /* RTL Support */
    [dir="rtl"] .error-help {
        border-left: none;
        border-right: 4px solid #ffc107;
    }
    
    [dir="rtl"] .error-help ul {
        text-align: right;
    }
    
    [dir="rtl"] .error-container::before {
        background: linear-gradient(90deg, #c82333, #dc3545);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .error-container {
            margin: 1rem;
            padding: 2rem 1rem;
        }
        
        .error-code {
            font-size: 3rem;
        }
        
        .error-title {
            font-size: 1.5rem;
        }
        
        .error-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-error {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="error-page">
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <div class="error-code">500</div>
        
        <h1 class="error-title"><?php echo e(__('Internal Server Error')); ?></h1>
        
        <p class="error-message">
            <?php echo e(__('Something went wrong on our end. Our team has been notified and is working to fix the issue.')); ?>

            <br>
            <?php echo e(__('Please try again in a few moments.')); ?>

        </p>
        
        <div class="error-actions">
            <a href="<?php echo e(route('home')); ?>" class="btn-error btn-primary-error">
                <i class="fas fa-home"></i>
                <?php echo e(__('Go to Home')); ?>

            </a>
            
            <a href="javascript:history.back()" class="btn-error btn-secondary-error">
                <i class="fas fa-arrow-left"></i>
                <?php echo e(__('Go Back')); ?>

            </a>
            
            <button onclick="location.reload()" class="btn-error btn-secondary-error">
                <i class="fas fa-redo"></i>
                <?php echo e(__('Try Again')); ?>

            </button>
        </div>
        
        <div class="error-help">
            <h5><i class="fas fa-lightbulb"></i> <?php echo e(__('What you can do:')); ?></h5>
            <ul>
                <li><?php echo e(__('Refresh the page and try again')); ?></li>
                <li><?php echo e(__('Clear your browser cache and cookies')); ?></li>
                <li><?php echo e(__('Try accessing the page from a different browser')); ?></li>
                <li><?php echo e(__('Contact support if the problem persists')); ?></li>
            </ul>
        </div>
        
        <?php if(config('app.debug')): ?>
        <div class="error-details" style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #dc3545;">
            <h5><i class="fas fa-bug"></i> <?php echo e(__('Debug Information')); ?></h5>
            <p><strong><?php echo e(__('URL:')); ?></strong> <?php echo e(request()->fullUrl()); ?></p>
            <p><strong><?php echo e(__('Method:')); ?></strong> <?php echo e(request()->method()); ?></p>
            <p><strong><?php echo e(__('User:')); ?></strong> <?php echo e(auth()->user() ? auth()->user()->email : __('Not authenticated')); ?></p>
            <p><strong><?php echo e(__('Timestamp:')); ?></strong> <?php echo e(now()->format('Y-m-d H:i:s')); ?></p>
            <?php if(isset($exception)): ?>
                <p><strong><?php echo e(__('Exception:')); ?></strong> <?php echo e(get_class($exception)); ?></p>
                <p><strong><?php echo e(__('Message:')); ?></strong> <?php echo e($exception->getMessage()); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click effect to buttons
        const buttons = document.querySelectorAll('.btn-error');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Create ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                history.back();
            }
            if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                e.preventDefault();
                location.reload();
            }
        });
        
        // Auto-retry functionality
        let retryCount = 0;
        const maxRetries = 3;
        
        function autoRetry() {
            if (retryCount < maxRetries) {
                retryCount++;
                console.log(`Auto-retry attempt ${retryCount}/${maxRetries}`);
                
                setTimeout(() => {
                    location.reload();
                }, 5000 * retryCount); // Increasing delay: 5s, 10s, 15s
            }
        }
        
        // Start auto-retry after 10 seconds
        setTimeout(autoRetry, 10000);
        
        // Add retry button functionality
        const retryButton = document.querySelector('button[onclick="location.reload()"]');
        if (retryButton) {
            retryButton.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo e(__("Retrying...")); ?>';
                this.disabled = true;
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });
        }
    });
</script>

<style>
    .btn-error {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    /* Loading spinner for retry button */
    .fa-spin {
        animation: fa-spin 1s infinite linear;
    }
    
    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp81\htdocs\aljeri-joil-yaseer-o3mhigh\resources\views/errors/500.blade.php ENDPATH**/ ?>
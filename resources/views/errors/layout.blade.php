@extends('layouts.app')

@section('title', $title ?? __('Error - JOIL YASEEIR'))

@push('styles')
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
        background: linear-gradient(90deg, {{ $accentColor ?? '#0061f2' }}, {{ $secondaryColor ?? '#a2c943' }});
    }
    
    .error-icon {
        font-size: 8rem;
        color: {{ $iconColor ?? '#0061f2' }};
        margin-bottom: 1rem;
        animation: {{ $animation ?? 'pulse' }} 2s infinite;
    }
    
    .error-code {
        font-size: 4rem;
        font-weight: 700;
        color: {{ $codeColor ?? '#0061f2' }};
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
        background: linear-gradient(135deg, {{ $primaryColor ?? '#0061f2' }}, {{ $primaryDark ?? '#0056d6' }});
        color: white;
    }
    
    .btn-primary-error:hover {
        background: linear-gradient(135deg, {{ $primaryDark ?? '#0056d6' }}, {{ $primaryDarker ?? '#0043a8' }});
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 97, 242, 0.3);
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
    
    .error-details {
        margin-top: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid {{ $accentColor ?? '#0061f2' }};
    }
    
    .error-details h5 {
        color: {{ $accentColor ?? '#0061f2' }};
        margin-bottom: 0.5rem;
    }
    
    .error-details p {
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .error-details ul {
        text-align: left;
        color: #6c757d;
    }
    
    .error-details li {
        margin-bottom: 0.25rem;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
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
    [dir="rtl"] .error-details {
        border-left: none;
        border-right: 4px solid {{ $accentColor ?? '#0061f2' }};
    }
    
    [dir="rtl"] .error-details ul {
        text-align: right;
    }
    
    [dir="rtl"] .error-container::before {
        background: linear-gradient(90deg, {{ $secondaryColor ?? '#a2c943' }}, {{ $accentColor ?? '#0061f2' }});
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
@endpush

@section('content')
<div class="error-page">
    <div class="error-container">
        <div class="error-icon">
            <i class="{{ $icon ?? 'fas fa-exclamation-circle' }}"></i>
        </div>
        
        <div class="error-code">{{ $code ?? 'Error' }}</div>
        
        <h1 class="error-title">{{ $title ?? __('An Error Occurred') }}</h1>
        
        <p class="error-message">
            {{ $message ?? __('Something went wrong. Please try again.') }}
        </p>
        
        <div class="error-actions">
            <a href="{{ route('home') }}" class="btn-error btn-primary-error">
                <i class="fas fa-home"></i>
                {{ __('Go to Home') }}
            </a>
            
            <a href="javascript:history.back()" class="btn-error btn-secondary-error">
                <i class="fas fa-arrow-left"></i>
                {{ __('Go Back') }}
            </a>
            
            @auth
                <a href="{{ route('profile.show') }}" class="btn-error btn-secondary-error">
                    <i class="fas fa-user"></i>
                    {{ __('My Profile') }}
                </a>
            @endauth
        </div>
        
        @if(isset($details))
            <div class="error-details">
                {!! $details !!}
            </div>
        @endif
        
        @if(config('app.debug'))
        <div class="error-details">
            <h5><i class="fas fa-info-circle"></i> {{ __('Debug Information') }}</h5>
            <p><strong>{{ __('URL:') }}</strong> {{ request()->fullUrl() }}</p>
            <p><strong>{{ __('Method:') }}</strong> {{ request()->method() }}</p>
            <p><strong>{{ __('User:') }}</strong> {{ auth()->user() ? auth()->user()->email : __('Not authenticated') }}</p>
            <p><strong>{{ __('User Roles:') }}</strong> 
                @if(auth()->check())
                    {{ auth()->user()->roles->pluck('name')->implode(', ') ?: __('No roles assigned') }}
                @else
                    {{ __('Not authenticated') }}
                @endif
            </p>
            <p><strong>{{ __('Timestamp:') }}</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
            @if(isset($exception))
                <p><strong>{{ __('Exception:') }}</strong> {{ get_class($exception) }}</p>
                <p><strong>{{ __('Message:') }}</strong> {{ $exception->getMessage() }}</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
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
        });
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
</style>
@endpush 
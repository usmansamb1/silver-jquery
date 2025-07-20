<!-- Payment Management Section -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('payment.*') ? 'active' : '' }}" href="#paymentMenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('payment.*') ? 'true' : 'false' }}" aria-controls="paymentMenu">
        <i class="fa fa-credit-card text-primary"></i>
        <span class="nav-link-text">Payments</span>
    </a>
    <div class="collapse {{ request()->routeIs('payment.*') ? 'show' : '' }}" id="paymentMenu">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('payment.index') ? 'active' : '' }}" href="{{ route('payment.index') }}">
                    <i class="fas fa-tachometer-alt text-primary me-2"></i>
                    <span class="nav-link-text">Overview</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('services.booking.saved-cards') ? 'active' : '' }}" href="{{ route('services.booking.saved-cards') }}">
                    <i class="fas fa-credit-card text-primary me-2"></i>
                    <span class="nav-link-text">Your Cards</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('payment.history') ? 'active' : '' }}" href="{{ route('payment.history') }}">
                    <i class="fas fa-history text-primary me-2"></i>
                    <span class="nav-link-text">Payment History</span>
                </a>
            </li>
        </ul>
    </div>
</li> 
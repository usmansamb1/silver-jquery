{{-- rEAL ONE {{ route('auth.index') }}--}}
<style>
/* Base menu styles */
.is-expanded-menu.side-header .menu-item.menuActive > .menu-link {
    background-color: #0073bd !important;
    color: #fff !important;
}
.menu-item.active > .menu-link {
    color: rgb(153, 191, 241) !important;
}
.menu-item.active > .menu-link i, .menu-item.menuActive > .menu-link i {
    color: #c8e296 !important;
}
.header-wrap-lang {
    padding-left: 5px !important;
    padding-right: 5px !important;
}

/* Comprehensive RTL Fixes for Arabic Menu */
@if(app()->getLocale() == 'ar')
/* RTL Direction and Layout */
body {
    direction: rtl !important;
    text-align: right !important;
}

/* Menu container RTL fixes */
.menu-container {
    direction: rtl !important;
    text-align: right !important;
}

.menu-item {
    direction: rtl !important;
    text-align: right !important;
}

/* Menu link RTL layout fixes - CRITICAL FIX */
.menu-link {
    direction: rtl !important;
    text-align: right !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    padding: 0.75rem 1.5rem 0.75rem 1rem !important;
    gap: 8px !important;
    flex-direction: row-reverse !important; /* This is the key fix */
}

.menu-link div {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    flex: 1 !important;
    gap: 8px !important;
    direction: rtl !important;
    text-align: right !important;
    flex-direction: row-reverse !important; /* This is the key fix */
}

/* Icon positioning in RTL - FIXED */
.menu-link i {
    order: 1 !important; /* Icons come FIRST in RTL */
    margin-right: 8px !important;
    margin-left: 0 !important;
    flex-shrink: 0 !important;
    width: 16px !important;
    text-align: center !important;
    display: inline-block !important;
    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome' !important;
    font-weight: 900 !important;
    font-style: normal !important;
    text-rendering: auto !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    width: auto !important;
    height: auto !important;
    background: none !important;
    color: inherit !important;
    font-size: inherit !important;
    vertical-align: middle !important;
}

/* Text content positioning in RTL */
.menu-link div span,
.menu-link div:not(:has(i)) {
    order: 2 !important; /* Text comes SECOND in RTL */
    flex: 1 !important;
    text-align: right !important;
}

/* Submenu RTL fixes */
.sub-menu-container {
    direction: rtl !important;
    text-align: right !important;
    padding-right: 2rem !important;
    padding-left: 1rem !important;
    border-right: 0px none rgba(0, 97, 242, 0.1) !important;
    border-left: none !important;
    margin-right: 0rem !important;
    margin-left: 0 !important;
}

.sub-menu-container .menu-link {
    padding-right: 2.5rem !important;
    padding-left: 1rem !important;
}

/* Profile section RTL */
.order-lg-1,
.text-lg-start {
    text-align: right !important;
}

.img-circle {
    margin-left: auto !important;
    margin-right: 0 !important;
}

/* Language selector RTL */
#languageSelector {
    text-align: right !important;
    direction: rtl !important;
}

.input-group {
    flex-direction: row !important;
    justify-content: flex-end !important;
}

/* Menu hover effects RTL */
.menu-item .menu-link:hover {
    transform: translateX(-5px) !important;
    transition: all 0.3s ease !important;
}

/* Active menu states RTL */
.menu-item.active > .menu-link,
.menu-item.menuActive > .menu-link {
    border-radius: 5px !important;
    margin: 2px 0 !important;
}

/* Ensure proper text rendering in Arabic */
* {
    text-rendering: optimizeLegibility !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* Arabic font fixes */
body,
.menu-link,
h1, h2, h3, h4, h5, h6 {
    font-family: 'Noto Kufi Arabic', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'Arial Unicode MS', 'Microsoft YaHei', 'SimSun', 'SimHei' !important;
}

/* SELECTIVE REMOVAL - Only remove submenu indicators, NOT Font Awesome icons */
/* Remove ONLY sub-menu-indicator elements */
.sub-menu-indicator,
.menu-link .sub-menu-indicator,
.menu-item .sub-menu-indicator,
.menu-container .sub-menu-indicator,
.primary-menu .sub-menu-indicator,
#header .sub-menu-indicator,
.sub-menu-indicator::after,
.sub-menu-indicator::before,
.menu-link .sub-menu-indicator::after,
.menu-link .sub-menu-indicator::before,
.menu-item .sub-menu-indicator::after,
.menu-item .sub-menu-indicator::before,
.menu-link div .sub-menu-indicator,
.menu-link div .sub-menu-indicator::after,
.menu-link div .sub-menu-indicator::before,
.menu-item div .sub-menu-indicator,
.menu-item div .sub-menu-indicator::after,
.menu-item div .sub-menu-indicator::before {
    display: none !important;
    content: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    background: none !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
    font-size: 0 !important;
    line-height: 0 !important;
    transform: none !important;
}

/* Remove any dropdown arrows or carets */
.menu-item.mparent > .menu-link .dropdown-toggle::after,
.menu-item.mparent > .menu-link .dropdown-toggle::before,
.menu-link .dropdown-toggle::after,
.menu-link .dropdown-toggle::before,
.menu-item .dropdown-toggle::after,
.menu-item .dropdown-toggle::before,
.dropdown-toggle::after,
.dropdown-toggle::before {
    display: none !important;
    content: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    background: none !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
}

/* Remove any theme-specific indicators */
.menu-item.mparent > .menu-link .caret,
.menu-item.mparent > .menu-link .arrow,
.menu-item.mparent > .menu-link .indicator,
.menu-link .caret,
.menu-link .arrow,
.menu-link .indicator,
.menu-item .caret,
.menu-item .arrow,
.menu-item .indicator {
    display: none !important;
    content: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    background: none !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
}

/* Remove any Bootstrap dropdown indicators */
.menu-item.mparent > .menu-link .dropdown-toggle,
.menu-link .dropdown-toggle,
.menu-item .dropdown-toggle {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
}

.menu-item.mparent > .menu-link .dropdown-toggle::after,
.menu-link .dropdown-toggle::after,
.menu-item .dropdown-toggle::after {
    display: none !important;
    content: none !important;
}

/* CRITICAL: Ensure Font Awesome icons are ALWAYS visible and properly styled */
.menu-link i[class*="fa-"],
.menu-link .fas,
.menu-link .far,
.menu-link .fab,
.menu-link .fa {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: auto !important;
    height: auto !important;
    background: none !important;
    color: inherit !important;
    font-size: inherit !important;
    line-height: inherit !important;
    vertical-align: middle !important;
    margin-right: 8px !important;
    margin-left: 0 !important;
    order: 1 !important;
    position: static !important;
    left: auto !important;
    top: auto !important;
    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome' !important;
    font-weight: 900 !important;
    font-style: normal !important;
    text-rendering: auto !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* Ensure proper spacing for menu items */
.menu-item {
    margin: 2px 0 !important;
}

/* Fix for any remaining white squares */
.menu-link i::before {
    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome' !important;
    font-weight: 900 !important;
    display: inline-block !important;
}

/* Mobile menu RTL fixes */
@media (max-width: 768px) {
    .primary-menu {
        right: 0 !important;
        left: auto !important;
    }
    
    .menu-container {
        text-align: right !important;
    }
    
    .menu-link {
        padding: 0.75rem 1.5rem 0.75rem 1rem !important;
    }
}

/* Fix for menu item dividers in RTL */
.menu-item-divider {
    border-right: none !important;
    border-left: 1px solid rgba(255, 255, 255, 0.1) !important;
    margin-right: 0 !important;
    margin-left: 0.5rem !important;
}

/* Ensure proper z-index for submenus */
.sub-menu-container {
    z-index: 1000 !important;
    position: relative !important;
}

/* Fix for menu item spacing consistency */
.is-expanded-menu .menu-item {
    padding: 2px 7px !important;
    margin: 0 !important;
    margin-top: 0.25rem !important;
}

/* Dark mode toggle RTL */
.body-scheme-toggle {
    text-align: right !important;
}

/* Language switcher container RTL */
.input-group-text {
    border-radius: 0.375rem 0 0 0.375rem !important;
}

.input-group > .form-control {
    border-radius: 0 0.375rem 0.375rem 0 !important;
}

/* Additional fixes for specific menu items */
.menu-link div:has(i) {
    flex-direction: row-reverse !important;
    justify-content: flex-end !important;
}

/* Fix for menu items without icons */
.menu-link div:not(:has(i)) {
    justify-content: flex-end !important;
    text-align: right !important;
}

/* Override any conflicting styles */
.menu-link * {
    direction: rtl !important;
}

/* Ensure proper flex layout */
.menu-link,
.menu-link div {
    display: flex !important;
    align-items: center !important;
}

/* Fix for any Bootstrap conflicts */
.d-flex {
    direction: rtl !important;
}

/* Ensure text content is properly aligned */
.menu-link div:not(:has(i)) {
    text-align: right !important;
    justify-content: flex-end !important;
}

/* CRITICAL: Override any theme CSS that might add submenu indicators */
.menu-item.mparent > .menu-link > div::after,
.menu-item.mparent > .menu-link > div::before,
.menu-item.mparent > .menu-link > i::after,
.menu-item.mparent > .menu-link > i::before,
.menu-item.mparent > .menu-link > span::after,
.menu-item.mparent > .menu-link > span::before,
.menu-item.mparent > .menu-link > *::after,
.menu-item.mparent > .menu-link > *::before {
    display: none !important;
    content: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    background: none !important;
    position: absolute !important;
    left: -9999px !important;
    top: -9999px !important;
}

/* Fix for any remaining white squares by ensuring proper icon rendering */
.menu-link i {
    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome' !important;
    font-weight: 900 !important;
    font-style: normal !important;
    text-rendering: auto !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    display: inline-block !important;
    width: auto !important;
    height: auto !important;
    background: none !important;
    color: inherit !important;
    font-size: inherit !important;
    line-height: inherit !important;
    vertical-align: middle !important;
    margin-right: 8px !important;
    margin-left: 0 !important;
    order: 1 !important;
}

/* EXCEPT for Font Awesome icons that we want to keep */
.menu-link i[class*="fa-"]::before,
.menu-link i.fas::before,
.menu-link i.far::before,
.menu-link i.fab::before,
.menu-link i.fa::before {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: auto !important;
    height: auto !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    background: none !important;
    position: static !important;
    left: auto !important;
    top: auto !important;
    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome' !important;
    font-weight: 900 !important;
    font-style: normal !important;
    text-rendering: auto !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* Remove any white squares in the menu */
/* [class*="fa-"], [class*=" fa-"], [class*="bi-"], [class*=" bi-"], [class*="uil-"], [class*=" uil-"] {
    display: none !important;
    line-height: inherit;
    font-display: swap;
} */
.is-expanded-menu.side-header .on-click .menu-item .sub-menu-trigger {
    display: none;
     
}
@endif
</style>

<script>
// SELECTIVE: Remove only sub-menu-indicator elements, preserve Font Awesome icons
document.addEventListener('DOMContentLoaded', function() {
    // Function to remove only sub-menu-indicator elements
    function removeSubMenuIndicators() {
        const indicators = document.querySelectorAll('.sub-menu-indicator');
        indicators.forEach(function(indicator) {
            indicator.remove();
        });
    }
    
    // Remove any existing indicators immediately
    removeSubMenuIndicators();
    
    // Set up a MutationObserver to watch for dynamically added indicators
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList && node.classList.contains('sub-menu-indicator')) {
                        node.remove();
                    }
                });
            }
        });
    });
    
    // Start observing the entire document
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Also remove indicators periodically as a backup
    setInterval(removeSubMenuIndicators, 1000);
    
    // Override the theme's arrow function to prevent adding indicators
    if (window._arrows) {
        const originalArrows = window._arrows;
        window._arrows = function() {
            // Don't call the original function
            return;
        };
    }
    
    // Override any other functions that might add indicators
    if (window.addArrow) {
        window.addArrow = function() {
            // Do nothing - prevent adding arrows
            return;
        };
    }
});

// Additional cleanup on window load
window.addEventListener('load', function() {
    // Remove any indicators that might have been added during page load
    const indicators = document.querySelectorAll('.sub-menu-indicator');
    indicators.forEach(function(indicator) {
        indicator.remove();
    });
    
    // Set up periodic cleanup
    setInterval(function() {
        const indicators = document.querySelectorAll('.sub-menu-indicator');
        indicators.forEach(function(indicator) {
            indicator.remove();
        });
    }, 500);
});
</script>
<header id="header" class="border-4 dark" data-mobile-sticky="true">
    <div id="header-wrap" class="header-wrap-lang">
        <div class="container">
            <div class="header-row">

                <div id="logo" class="py-2 py-xl-3 mx-xl-0 w-auto align-self-center LoadingUi">
                    <a href="/home" class="d-block text-center">
                        <div class="logo-container" style="background-color: white; border-radius: 12px; padding: 8px 12px; display: inline-block; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin: 0 auto;">
                            <img src="{{ asset('theme_files/imgs/yaseeir-smal-new-logo6.png') }}" alt="Yaseeir Logo" style="height:80px; width: auto; max-width: 100%; display: block;">
                        </div>
                    </a>
                </div>
                <div class="primary-menu-trigger">
                    <button class="cnvs-hamburger" type="button" title="Open Mobile Menu">
                        <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                    </button>
                </div>
                <nav class="primary-menu  mobile-menu-off-canvas order-last order-lg-2 mt-8 mb-auto">
                    <ul class="menu-container one-page-menu" data-easing="easeInOutExpo" data-speed="1250" data-offset="0">
                        <li class="menu-item" data-animate="fadeInLeftSmall" data-delay="200">
                            <a href="{{ route('profile.show') }}" class="LoadingUi">
                                <div class="order-4 order-lg-1 text-lg-start">
                                    <div class="col">
                                        <img src="{{ $user->avatar_url }}" class="img-circle img-thumbnail mb-1" alt="Profile" style="max-width: 84px; height: 84px; object-fit: cover;">
                                        <h4 class="mb-2">{{ $user->name ?? $user->company_name ?? 'User' }}</h4>
                                        <h5 class="mb-2">#{{ $user->formatted_customer_no }}</h5>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @role('customer')   
                        <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}"><a class="menu-link LoadingUi" href="{{ url('/home') }}"><div><i class="fas fa-home"></i> {{ __('Home') }}</div></a></li>
                        @endrole

                        @role('admin|finance|audit|it|contractor|delivery|activation|validation')   
 
                            @if(auth()->user()->hasRole('delivery'))
                                <li class="menu-item  {{ request()->routeIs('admin.delivery.dashboard') ? 'active' : '' }}"><a class="menu-link LoadingUi " href="{{ url('/admin/deliverydashboard') }}"><div><i class="fas fa-home"></i> {{ __('Delivery Dashboard') }}</div></a></li>
                            @else
                                <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><a class="menu-link LoadingUi" href="{{ url('/admin/dashboard') }}"><div><i class="fas fa-home"></i> {{ __('Dashboard') }}</div></a></li>
                            @endif
                            
                        @endrole

                        @role('customer|admin|finance|audit|it|contractor|activation|validation')
                        <li class="menu-item mparent {{ request()->routeIs('wallet.*') ? 'active' : '' }}" id="walletManage">
                            <a class="menu-link {{ request()->routeIs('wallet.*') ? 'headerMenuBg' : '' }}  " href="#">
                                <div><i class="fas fa-credit-card"></i> {{ __('Wallet Management') }}</div>
                            </a>
                            <ul class="sub-menu-container d-block">
                            @role('customer')    
                                <li class="menu-item {{ request()->routeIs('wallet.history') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('wallet.history') }}">
                                        <div>
                                            <i class="fas fa-history"></i>
                                            {{ __('Wallet History') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('wallet.topup') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('wallet.topup') }}">
                                        <div>
                                            <i class="fas fa-plus-circle"></i>
                                            {{ __('Top Up Wallet') }}
                                        </div>
                                    </a>
                                </li>
                                @endrole
                             
                                @role('admin')
                                <li class="menu-item {{ request()->routeIs('admin.wallet-requests.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('admin.wallet-requests.index') }}">
                                        <div>
                                            <i class="fas fa-clock"></i>
                                            {{ __('All Wallet Requests') }}
                                        </div>
                                    </a>
                                </li>
                                @endrole
                                @role('admin')
                                <li class="menu-item {{ request()->routeIs('wallet.approvals.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('wallet.approvals.index') }}">
                                        <div>
                                            <i class="fas fa-clock"></i>
                                            {{ __('Pending Approvals') }}
                                        </div>
                                    </a>
                                </li>
                                @endrole
                                @role('finance|admin|audit|it|validation|activation')
                                <li class="menu-item {{ request()->routeIs('wallet.approvals.my-approvals') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('wallet.approvals.my-approvals') }}">
                                        <div>
                                            <i class="fas fa-check-circle"></i>
                                            {{ __('My Pending Approvals') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('wallet-approvals.history') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('wallet-approvals.history') }}">
                                        <div>
                                            <i class="fas fa-history"></i>
                                            {{ __('Approval History') }}
                                        </div>
                                    </a>
                                </li>
                                @endrole
                            </ul>
                        </li>
                        @endrole
                        
                        @role('customer')
                        <li class="menu-item mparent {{ request()->routeIs('services.booking.*') ? 'active' : '' }}" id="serviceBookings">
                            <a class="menu-link {{ request()->routeIs('services.booking.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div><i class="fas fa-briefcase"></i> {{ __('Services') }}</div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                {{-- <li class="menu-item {{ request()->routeIs('services.booking.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('services.booking.index') }}">
                                        <div>
                                            <i class="fas fa-list"></i>
                                            My Bookings
                                        </div>
                                    </a>
                                </li> --}}
                                <li class="menu-item {{ request()->routeIs('services.booking.create') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('services.booking.order.form') }}">
                                        <div><i class="fas fa-calendar-plus"></i> {{ __('Book a Service') }}</div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('services.booking.history') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('services.booking.history') }}">
                                        <div><i class="fas fa-history"></i> {{ __('Booking History') }}</div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Vehicle Management Menu -->
                        <li class="menu-item mparent {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" id="vehicleManage">
                            <a class="menu-link {{ request()->routeIs('vehicles.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div><i class="fas fa-car"></i> {{ __('Vehicle Management') }}</div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('vehicles.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('vehicles.index') }}">
                                        <div>
                                            <i class="fas fa-list"></i>
                                            {{ __('My Vehicles') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('vehicles.create') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('vehicles.create') }}">
                                        <div>
                                            <i class="fas fa-plus-circle"></i>
                                            {{ __('Add New Vehicle') }}
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- RFID Management Menu -->
                        <li class="menu-item mparent {{ request()->routeIs('rfid.*') ? 'active' : '' }}" id="rfidManage">
                            <a class="menu-link {{ request()->routeIs('rfid.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div><i class="fas fa-id-card"></i> {{ __('RFID Card Management') }}</div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('rfid.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('rfid.index') }}">
                                        <div>
                                            <i class="fas fa-tachometer-alt"></i>
                                            {{ __('RFID Dashboard') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('rfid.transfer') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('rfid.transfer') }}">
                                        <div>
                                            <i class="fas fa-exchange-alt"></i>
                                            {{ __('Transfer RFID') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('rfid.recharge') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('rfid.recharge') }}">
                                        <div>
                                            <i class="fas fa-credit-card"></i>
                                            {{ __('Recharge RFID') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('rfid.transactions') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('rfid.transactions') }}">
                                        <div>
                                            <i class="fas fa-history"></i>
                                            {{ __('Transaction History') }}
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endrole

                        
                        <li class="menu-item mparent {{ request()->routeIs('map-view') || request()->routeIs('check-map-marks') || request()->routeIs('maps-list') || request()->is('locations*') ? 'active' : '' }}" id="locationManage">
                            <a class="menu-link {{ request()->routeIs('map-view') || request()->routeIs('check-map-marks') || request()->routeIs('maps-list') || request()->is('locations*') ? 'headerMenuBg' : '' }}" href="#">
                                <div>
                                    <i class="fas fa-map-marker-alt"></i> {{ __('Locations') }}
                                </div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('map-view') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('map-view') }}">
                                        <div>
                                            <i class="fas fa-map-marker-alt"></i> {{ __('Map View') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('check-map-marks') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('check-map-marks') }}">
                                        <div>
                                            <i class="fas fa-location-arrow"></i> {{ __('Find Nearest Station') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('maps.sync.list') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('maps.sync.list') }}">
                                        <div>
                                            <i class="fas fa-list"></i> {{ __('Stations List') }}
                                        </div>
                                    </a>
                                </li>
                                @role('admin')
                                <li class="menu-item {{ request()->is('locations/manage') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="#">
                                        <div>
                                            <i class="fas fa-edit"></i> {{ __('Manage Locations') }}
                                        </div>
                                    </a>
                                </li>
                                @endrole
                            </ul>
                        </li>

                        
                        @role('customer|admin|finance|audit|it|contractor|delivery')
                        <li class="menu-item mparent {{ request()->routeIs('user.logs.*') ? 'active' : '' }}" id="userLogs">
                            <a class="menu-link {{ request()->routeIs('user.logs.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div><i class="fas fa-history"></i> {{ __('Activity Logs') }}</div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('user.logs.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('user.logs.index') }}">
                                        <div>
                                            <i class="fas fa-list"></i>
                                            {{ __('My Activities') }}
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endrole

                        @role('customer')
                        <li class="menu-item mparent {{ request()->routeIs('profile.*') || request()->routeIs('services.booking.saved-cards') ? 'active' : '' }}">
                            <a class="menu-link {{ request()->routeIs('profile.*') || request()->routeIs('services.booking.saved-cards') ? 'headerMenuBg' : '' }}" href="#">
                                <div>
                                    <i class="fas fa-user-circle"></i> {{ __('Profile') }}
                                </div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('profile.show') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('profile.show') }}">
                                        <div>
                                            <i class="fas fa-user"></i> {{ __('My Profile') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('services.booking.saved-cards') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ url('services/booking/saved-cards') }}">
                                        <div>
                                            <i class="fas fa-credit-card"></i> {{ __('My Saved Cards') }}
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endrole

                        @role('admin|finance|audit|it|contractor|activation|validation')
                        <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <a class="menu-link LoadingUi" href="{{ route('profile.show') }}">
                                <div>
                                    <i class="fas fa-user-circle"></i> {{ __('Profile') }}
                                </div>
                            </a>
                        </li>
                        @endrole

                   

                
               

                        @role('admin|finance|audit|it|contractor|activation|validation')
                        <li class="menu-item mparent {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" id="userManage">
                            <a class="menu-link {{ request()->routeIs('admin.users.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div><i class="fas fa-users"></i> {{ __('User Management') }}</div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('admin.users.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('admin.users.index') }}">
                                        <div>
                                            <i class="fas fa-list"></i>
                                            {{ __('List Users') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('admin.users.create') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('admin.users.create') }}">
                                        <div>
                                            <i class="fas fa-user-plus"></i>
                                            {{ __('Add New User') }}
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endrole
                        @role('admin')
                        <li class="menu-item mparent {{ request()->routeIs('admin.approval-workflows.*') ? 'active' : '' }}" id="workflowManage">
                            <a class="menu-link {{ request()->routeIs('admin.approval-workflows.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div>
                                        <i class="fas fa-project-diagram"></i>
                                        {{ __('Approval Workflows') }}
                                </div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('admin.approval-workflows.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('admin.approval-workflows.index') }}">
                                        <div>
                                            <i class="fas fa-list"></i>
                                            {{ __('List Workflows') }}
                                        </div>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('admin.approval-workflows.create') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('admin.approval-workflows.create') }}">
                                        <div>
                                            <i class="fas fa-plus"></i>
                                            {{ __('Add Workflow') }}
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="menu-item {{ request()->routeIs('admin.logs.*') ? 'menuActive' : '' }}">
                            <a class="menu-link LoadingUi" href="{{ route('admin.logs.index') }}">
                                <div>
                                    <i class="fas fa-history"></i>
                                    {{ __('System Logs') }}
                                </div>
                            </a>
                        </li>
                        @endrole     
                        @role('admin')
                        <li class="menu-item mparent {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" id="paymentManage">
                            <a class="menu-link {{ request()->routeIs('admin.payments.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div>
                                        <i class="fas fa-money-bill"></i>
                                        {{ __('Manage Payment') }}
                                </div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item {{ request()->routeIs('admin.payments.index') ? 'menuActive' : '' }}">
                                    <a class="menu-link LoadingUi" href="{{ route('admin.payments.index') }}">
                                        <div>
                                            <i class="fas fa-list"></i>
                                            {{ __('List of All Payments') }}
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                   
                        <li class="menu-item {{ request()->routeIs('admin.test.email') ? 'menuActive' : '' }}">
                            <a class="menu-link LoadingUi" href="{{ route('admin.test.email') }}">
                                <div>
                                    <i class="fas fa-envelope"></i>
                                    {{ __('Email Tester') }}
                                </div>
                            </a>
                        </li>
                         
                         
                        @endrole

                        {{-- <li class="menu-item mparent {{ request()->routeIs('notifications.*') ? 'active' : '' }}" id="notificationManage">
                            <a class="menu-link {{ request()->routeIs('notifications.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div>
                                    <i class="fas fa-bell"></i> Notifications
                                </div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item">
                                    <a class="menu-link LoadingUi" href="#">
                                        <div>
                                            <i class="fas fa-envelope"></i> My Notifications
                                        </div>
                                    </a>
                                </li>
                                @role('admin')
                                 <li class="menu-item">
                                    <a class="menu-link LoadingUi" href="#">
                                        <div>
                                            <i class="fas fa-cog"></i> Configure Notifications
                                        </div>
                                    </a>
                                </li>
                                @endrole
                            </ul>
                        </li> --}}

                        <li class="menu-item mparent {{ request()->routeIs('reports.*') ? 'active' : '' }}" id="reportsManage">
                            <a class="menu-link {{ request()->routeIs('reports.*') ? 'headerMenuBg' : '' }}" href="#">
                                <div><i class="fas fa-chart-bar"></i> {{ __('Reports') }}</div>
                            </a>
                            <ul class="sub-menu-container d-block">
                                <li class="menu-item">
                                    <a class="menu-link LoadingUi" href="#">
                                        <div><i class="fas fa-file-alt"></i> {{ __('Usage Reports') }}</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a class="menu-link LoadingUi" href="#">
                                        <div><i class="fas fa-file-excel"></i> {{ __('Excel Report') }}</div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                     
                        <li class="menu-item">
                            <a data-animate="fadeInLeftSmall" data-delay="1500" href="#" class="body-scheme-toggle text-white fw-bolder menu-link" data-bodyclass-toggle="dark" data-add-class="btn-warning" data-add-html="MODE: Light" data-remove-html="MODE: Dark">
                                <div>
                                    <i class="fas fa-moon"></i> {{ __('Dark Mode') }}
                                </div>
                            </a>
                        </li>
                        <li class="menu-item" data-animate="fadeInLeftSmall" data-delay="800">
                            <div class="input-group form-group">
                                <span class="input-group-text bg-transparent text-white border-0 p-0 m-0">
                                    <i class="fas fa-language"></i>
                                    <select name="language" class="required valid border-transparent text-white bg-transparent menu-link" onchange="changeLanguage(this.value);" id="languageSelector">
                                        <option value="en" style="color:black" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                                        <option value="ar" style="color:black" {{ app()->getLocale() == 'ar' ? 'selected' : '' }}>{{ __('Arabic') }}</option>
                                    </select>
                                </span>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="header-wrap-clone mb-0"></div>
</header>

<script>

    // load after jquery ran
    setTimeout(() => {
        //    $("#offshelfManage > a > div#offshelfManage").click();
        initilizerMenu();
    }, 600);

    function initilizerMenu()
    {
        jQuery(document).ready(function () {
            console.log("jquery initilized from menu");
            var subparentis2 = jQuery('.menu-item.menuActive').parent('ul').addClass('d-block');
            var subparentis3 = jQuery('.menu-item.menuActive').parent('li.menu-item.sub-menu').addClass('bg-primary');
            var subparentis4 = jQuery('.menu-item.menuActive').parent('.sub-menu-container').parent('li.menu-item.mparent.sub-menu').find('.menu-link').first().addClass('headerMenuBg');
            // console.log(subparentis4);
            // console.log(subparentis2);
            // console.log(subparentis3);
            //jQuery('#menu-item').parents('ul.sub-menu-container').addClass('d-block');
        });
    }

</script>

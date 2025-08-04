<section id="content-header" class="border-bottom border-3 shadow-sm mb-3">
    <div class="container">
        <div class="row g-1">
            <div class="card-body p-0 mb-0 mt-4">

                <div class="d-flex align-items-center justify-content-between">

                    <div class="row col-sm-12">
                        <div class="col-sm-5">
                            <h6 class="fs-3 fw-semibold mb-0">
                                <div class="title-block">
                                    <h4 class="fs-3 fw-esmibold mb-0">{{ __('FuelApp Online') }}</h4>
                                    <span>
                                    <nav aria-label="breadcrumb" class="d-none d-lg-block">
                                        <ol class="breadcrumb" style="--bs-breadcrumb-divider: '/';">

                                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="uil uil-home"></i> {{ __('Dashboard') }}</a> 
                                                    {{-- {{ Auth::user()->getRoleNames()->first() }}   --}}
                                                 </li>
                                        </ol>
                                        
                                    </nav>
                                </span>
                                </div>
                            </h6>

                        </div>
                        

                        <div class="col-sm-5 col-lg-5 text-right">
                            <style>
                                ul.tert-nav {
                                    float: right;
                                    position: absolute;
                                    margin: 0;
                                    padding: 0;
                                    right: 0;
                                    top: 0;
                                    list-style: none;
                                }
                            @if(app()->getLocale() == 'ar')
                                .title-block {
                            border-right: 7px solid #a2c943 !important;
                            border-left: 0px none !important;
                        }  
                        @endif
                                ul.tert-nav li {
                                    float: right;
                                    width: 100%;
                                    height: 28px;

                                    text-align: center;
                                    margin-left: 2px;
                                    cursor: pointer;
                                    transition: all .2s ease;
                                    -o-transition: all .2s ease;
                                    -moz-transition: all .2s ease;
                                    -webkit-transition: all .2s ease;
                                }

                                ul.tert-nav li:hover {

                                }

                                ul.tert-nav .search {
                                    width: 246px;
                                    text-align: left;
                                    cursor: default;
                                }

                                ul.tert-nav .search:hover {

                                }

                                ul.tert-nav .searchbox {
                                    display: none;
                                    width: 100%;
                                }

                                ul.tert-nav .searchbox .closesearch {
                                    float: left;
                                    margin: 6px 4px 0px 4px;
                                    cursor: pointer;
                                }

                                ul.tert-nav .searchbox .closesearch:hover {
                                    opacity: 0.8;
                                }

                                ul.tert-nav .searchbox input[type=text] {
                                    float: left;
                                    width: 184px;
                                    height: 24px;
                                    padding: 0px 0px 0px 10px;
                                    margin: 2px 0px 0px 0px;
                                    border: none;
                                    background:  no-repeat;
                                    outline: none;
                                }

                                ul.tert-nav .searchbox input[type=submit] {
                                    float: left;
                                    width: 26px;
                                    height: 24px;
                                    margin: 2px 0px 0px 0px;
                                    padding: 0px;
                                    border: none;
                                    background: url(images/search-btn.png) no-repeat;
                                    outline: none;
                                    cursor: pointer;
                                }
                                .searchinputstyle{
                                    background-color: aliceblue;
                                    border: 1px solid #000 !important;
                                }


                                .input-group{
                                    flex-direction: row-reverse;
                                }
                                .input-group-append{
                                    display: inline-block;
                                    position: absolute;
                                    right: 0px;
                                    top: 0px;
                                }

                                .select2-container--default .select2-selection--single {
                                    background-color: #ddd;

                                    border-radius: 8px;
                                    height: 40px;
                                    padding-top: 5px;
                                }

                                .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
                                    background-color: #0073bd !important;
                                }

                                #ofssearchbtn {
                                    background-color: transparent;
                                    color: #6c757d;
                                }

                                .ofssearchbtn-selected{
                                    background-color: var(--bs-btn-hover-bg) !important;
                                    color: #fff !important;
                                }

                                /* Professional Language Selector Styles */
                                .language-selector-container {
                                    position: relative;
                                    margin-right: 15px;
                                    display: inline-block;
                                }

                                .language-dropdown-wrapper {
                                    position: relative;
                                    display: inline-block;
                                }

                                .language-btn {
                                    display: flex;
                                    align-items: center;
                                    gap: 6px;
                                    padding: 6px 12px;
                                    border: 1px solid #dee2e6;
                                    border-radius: 6px;
                                    background: #fff;
                                    color: #495057;
                                    font-size: 13px;
                                    font-weight: 500;
                                    text-decoration: none;
                                    transition: all 0.3s ease;
                                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                                    min-width: 85px;
                                    justify-content: space-between;
                                    line-height: 1.4;
                                    cursor: pointer;
                                    outline: none;
                                }

                                .language-btn:hover {
                                    background: #f8f9fa;
                                    border-color: #0073bd;
                                    color: #0073bd;
                                    box-shadow: 0 2px 8px rgba(0,115,189,0.2);
                                }

                                .language-btn:focus {
                                    outline: none;
                                    box-shadow: 0 0 0 2px rgba(0,115,189,0.25);
                                }

                                .language-btn:active {
                                    background: #e9ecef;
                                    border-color: #0073bd;
                                }

                                .language-flag {
                                    font-size: 14px !important;
                                    line-height: 1 !important;
                                    margin-right: 3px !important;
                                }

                                .language-code {
                                    font-weight: 600 !important;
                                    letter-spacing: 0.3px !important;
                                    font-size: 12px !important;
                                }

                                .language-arrow {
                                    font-size: 8px;
                                    color: #6c757d;
                                    transition: transform 0.3s ease;
                                    margin-left: auto;
                                }

                                .language-dropdown-wrapper.show .language-arrow {
                                    transform: rotate(180deg);
                                }

                                .language-menu {
                                    position: absolute;
                                    top: 100%;
                                    right: 0;
                                    border: 1px solid #dee2e6;
                                    border-radius: 8px;
                                    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                                    padding: 5px 0;
                                    min-width: 180px;
                                    margin-top: 3px;
                                    background: #fff;
                                    overflow: hidden;
                                    z-index: 1050;
                                    display: none;
                                }

                                .language-menu.show {
                                    display: block;
                                    animation: fadeIn 0.3s ease;
                                }

                                @keyframes fadeIn {
                                    from { opacity: 0; transform: translateY(-10px); }
                                    to { opacity: 1; transform: translateY(0); }
                                }

                                .language-divider {
                                    margin: 5px 0;
                                    border: 0;
                                    border-top: 1px solid #e9ecef;
                                }

                                .language-item {
                                    display: flex;
                                    align-items: center;
                                    gap: 10px;
                                    padding: 8px 15px;
                                    color: #495057;
                                    text-decoration: none;
                                    transition: all 0.3s ease;
                                    border: none;
                                    position: relative;
                                    font-size: 14px;
                                    cursor: pointer;
                                    width: 100%;
                                    box-sizing: border-box;
                                }

                                .language-item:hover {
                                    background: #f8f9fa;
                                    color: #0073bd;
                                    text-decoration: none;
                                }

                                .language-item.active {
                                    background: rgba(0,115,189,0.1);
                                    color: #0073bd;
                                    font-weight: 600;
                                }

                                .language-item.active::before {
                                    content: '';
                                    position: absolute;
                                    left: 0;
                                    top: 0;
                                    bottom: 0;
                                    width: 3px;
                                    background: #0073bd;
                                }

                                .language-flag-menu {
                                    font-size: 20px;
                                    line-height: 1;
                                    flex-shrink: 0;
                                }

                                .language-details {
                                    display: flex;
                                    flex-direction: column;
                                    flex: 1;
                                    gap: 2px;
                                }

                                .language-name {
                                    font-weight: 500;
                                    font-size: 14px;
                                    line-height: 1.2;
                                }

                                .language-native {
                                    color: #666;
                                    font-size: 12px;
                                    line-height: 1.2;
                                }

                                .language-check {
                                    color: #0073bd;
                                    font-size: 14px;
                                    margin-left: auto;
                                }

                                /* RTL Support for Language Selector */
                                @if(app()->getLocale() == 'ar')
                                .language-selector-container {
                                    margin-left: 15px;
                                    margin-right: 0;
                                }

                                .language-item:hover {
                                    transform: translateX(-2px);
                                }

                                .language-item.active::before {
                                    right: 0;
                                    left: auto;
                                }

                                .language-check {
                                    margin-right: auto;
                                    margin-left: 0;
                                }

                                .language-dropdown .language-btn {
                                    flex-direction: row-reverse;
                                }
                                @endif

                                /* Integrate with existing header styles */
                                #top-social li.language-selector-container {
                                    float: right;
                                    margin: 0 5px;
                                }

                                #top-social .language-dropdown {
                                    display: inline-block;
                                }

                                /* Mobile Responsive */
                                @media (max-width: 768px) {
                                    .language-dropdown .language-btn {
                                        padding: 4px 8px !important;
                                        min-width: 65px !important;
                                        font-size: 12px !important;
                                    }

                                    .language-flag {
                                        font-size: 12px !important;
                                    }

                                    .language-code {
                                        display: none !important;
                                    }

                                    .language-menu {
                                        min-width: 160px !important;
                                        margin-top: 2px !important;
                                    }

                                    .language-item {
                                        padding: 6px 12px !important;
                                        gap: 8px !important;
                                        font-size: 13px !important;
                                    }

                                    .language-flag-menu {
                                        font-size: 14px !important;
                                    }

                                    .language-name {
                                        font-size: 12px !important;
                                    }

                                    .language-native {
                                        font-size: 10px !important;
                                    }

                                    .language-selector-container {
                                        margin-right: 8px !important;
                                    }
                                }

                                @media (max-width: 480px) {
                                    .language-selector-container {
                                        margin-right: 8px;
                                    }

                                    .language-dropdown .language-btn {
                                        padding: 4px 8px;
                                        min-width: 60px;
                                        border-radius: 6px;
                                    }

                                    .language-menu {
                                        min-width: 160px;
                                    }
                                }

                                .language-selector-container {
                                    position: relative;
                                    margin-right: 0 !important;
                                    margin-left: 0 !important;
                                    display: block;
                                    float: none !important;
                                }
                                #top-social li.language-selector-container {
                                    float: none !important;
                                    margin: 0 !important;
                                }


                            </style>




                            <script>


                                // setTimeout(() => searchinit(), 500);






                            </script>
                        </div>
                        <div class="col-sm-2">
                            <div class="d-flex mt-4" style="">

                                <div id="top-search" class="header-misc-icon">
                                </div>
                                <div class="col-12 col-md-auto">
                                    <ul id="top-social" class="d-flex flex-column align-items-end gap-1">
                                        <li>
                                            {{--   <form action="{{ route('logout') }}" method="POST">
                                                    @csrf
                                                    <span class="ts-icon"><button type="submit" class="fa-solid fa-right-from-bracket LoadingUi"><i class="fa-solid fa-right-from-bracket"></i><span class="ts-text">Exit</span></button></span>
                                                </form>--}}
                                            @role('admin|finance|validation|activation|it|delivery')
                                            <form action="{{ route('admin.logout') }}" method="POST" id="frmlogout" style="margin-bottom: 8px !important;">
                                                @csrf
                                            <a href="#/Logout" class="h-bg-slack LoadingUi" id="submitLogout">
                                                <span class="ts-icon"><i class="fa-solid fa-right-from-bracket"></i></span><span class="ts-text">Exit</span>
                                            </a>
                                            </form>
                                            @endrole
                                            @role('customer')
                                            <form action="{{ route('logout') }}" method="POST" id="frmlogout" style="margin-bottom: 8px !important;">
                                                @csrf
                                            <a href="#/Logout" class="h-bg-slack LoadingUi" id="submitLogout">
                                                <span class="ts-icon"><i class="fa-solid fa-right-from-bracket"></i></span><span class="ts-text">Exit</span>
                                            </a>
                                            </form>
                                            @endrole
                                        </li>
                                        <li class="language-selector-container mt-2">
                                            <div class="language-dropdown-wrapper">
                                                <button class="language-btn" type="button" id="languageDropdown" onclick="toggleLanguageDropdown()" title="{{ __('Language') }}">
                                                    <span class="language-flag">
                                                        @if(app()->getLocale() == 'ar')
                                                            <i class="fas fa-globe text-success"></i>
                                                        @else
                                                            <i class="fas fa-globe text-primary"></i>
                                                        @endif
                                                    </span>
                                                    <span class="language-code">{{ strtoupper(app()->getLocale()) }}</span>
                                                    <i class="fas fa-chevron-down language-arrow" id="languageArrow"></i>
                                                </button>
                                                <div class="language-menu" id="languageMenu" style="display: none;">
                                                    <a class="language-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" 
                                                       href="{{ route('lang.change', 'en') }}" 
                                                       onclick="return changeLanguage(event, 'en')"
                                                       data-locale="en">
                                                        <span class="language-flag-menu">
                                                            <i class="fas fa-flag text-primary"></i>
                                                        </span>
                                                        <span class="language-details">
                                                            <span class="language-name">English</span>
                                                            <small class="language-native text-muted">English</small>
                                                        </span>
                                                        @if(app()->getLocale() == 'en')
                                                            <i class="fas fa-check language-check text-success"></i>
                                                        @endif
                                                    </a>
                                                    <hr class="language-divider">
                                                    <a class="language-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" 
                                                       href="{{ route('lang.change', 'ar') }}" 
                                                       onclick="return changeLanguage(event, 'ar')"
                                                       data-locale="ar">
                                                        <span class="language-flag-menu">
                                                            <i class="fas fa-flag text-success"></i>
                                                        </span>
                                                        <span class="language-details">
                                                            <span class="language-name">العربية</span>
                                                            <small class="language-native text-muted">Arabic</small>
                                                        </span>
                                                        @if(app()->getLocale() == 'ar')
                                                            <i class="fas fa-check language-check text-success"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="/Trademark" class="">
                                            </a>
                                        </li>
                                    </ul>
                                </div>


                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        document.getElementById('submitLogout').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent the default anchor behavior
        document.getElementById('frmlogout').submit(); // Submit the form
        });

        // Simple Language Dropdown Toggle Function
        function toggleLanguageDropdown() {
            const menu = document.getElementById('languageMenu');
            const wrapper = document.querySelector('.language-dropdown-wrapper');
            const arrow = document.getElementById('languageArrow');
            
            console.log('Toggle dropdown clicked'); // Debug
            
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                menu.classList.add('show');
                wrapper.classList.add('show');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
                console.log('Dropdown opened'); // Debug
            } else {
                menu.style.display = 'none';
                menu.classList.remove('show');
                wrapper.classList.remove('show');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
                console.log('Dropdown closed'); // Debug
            }
        }

        // Language Change Function
        function changeLanguage(event, locale) {
            event.preventDefault();
            console.log('Language change clicked:', locale); // Debug
            
            // Show loading on the clicked item
            const clickedItem = event.target.closest('.language-item');
            const originalContent = clickedItem.innerHTML;
            
            clickedItem.innerHTML = `
                <span class="language-flag-menu">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </span>
                <span class="language-details">
                    <span class="language-name">Loading...</span>
                    <small class="language-native text-muted">Switching language...</small>
                </span>
            `;
            clickedItem.style.pointerEvents = 'none';
            
            // Navigate to language change route
            setTimeout(() => {
                window.location.href = clickedItem.getAttribute('href');
            }, 500);
            
            return false;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const wrapper = document.querySelector('.language-dropdown-wrapper');
            const menu = document.getElementById('languageMenu');
            const arrow = document.getElementById('languageArrow');
            
            if (wrapper && !wrapper.contains(event.target)) {
                menu.style.display = 'none';
                menu.classList.remove('show');
                wrapper.classList.remove('show');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        });

        // Handle responsive behavior
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Language selector initialized');
            
            function handleResize() {
                const isMobile = window.innerWidth <= 768;
                const languageCode = document.querySelector('.language-code');
                
                if (languageCode) {
                    languageCode.style.display = isMobile ? 'none' : 'inline-block';
                }
            }

            window.addEventListener('resize', handleResize);
            handleResize(); // Call on load
        });
    </script>
@endpush

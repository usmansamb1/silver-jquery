@extends('layouts.app')


@section('title', __('JOIL YASEEIR ONLINE') . ' :: ' . __('Home'))

 
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@emran-alhaddad/saudi-riyal-font/index.css">
    <style>
        .card-number {
            font-size: 20px !important;
        }
    </style>
@endpush

@section('content')
    {{--            <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
                <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
                <p><strong>Registration Type:</strong> {{ ucfirst($user->registration_type) }}</p>--}}
    
    <div class="container clearfix">

        <div class="row grid-container" data-layout="masonry" style="overflow: visible">
            <div class="col-lg-4 mb-4">
                <div class="flip-card text-center">
                    <div class="flip-card-front jcolor-green"  data-height-xl="200" style="background-image: url('demos/business/images/featured/1.jpg')">
                        <div class="flip-card-inner">
                            <div class="card nobg noborder text-center">
                                <div class="card-body">

                                    <h3 class="card-title"> <i class="bi bi-tags h1 jcolor-white mb-1"> 3 </i> <br> {{ __('Total Tags') }} </h3>
                                    <p class="card-text t400"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card-back bg-danger no-after" data-height-xl="200" >
                        <div class="flip-card-inner">
                            <p class="mb-2 text-white">{{ __('To view your paid services.') }}</p>
                            <button type="button" class="btn btn-outline-light mt-2">{{ __('View Details') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="flip-card text-center top-to-bottom">
                    <div class="flip-card-front jcolor-blue " data-height-xl="200"  style="background-image: url('demos/business/images/featured/2.jpg');">
                        <div class="flip-card-inner">
                            <div class="card nobg noborder text-center">
                                <div class="card-body">
                                    <h3 class="card-title"> <i class="bi bi-tags h1 jcolor-white mb-1"> 1 </i> <br> {{ __('Pending Tags To Install') }} </h3>
                                    <p class="card-text t400"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card-back" data-height-xl="200"  style="background-image: url('demos/business/images/featured/3.jpg');">
                        <div class="flip-card-inner">
                            <p class="mb-2 text-white">{{ __('To view your tags list') }}</p>
                            <button type="button" class="btn btn-outline-light mt-2">{{ __('View Details') }}</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4 mb-4">
                <div class="flip-card text-center">
                    <div class="flip-card-front jcolor-red " data-height-xl="200" style="background-image: url('demos/ecommerce/images/catagories/1.jpg');">
                        <div class="flip-card-inner">
                            <div class="card nobg noborder text-center">
                                <div class="card-body">
                                    <h3 class="card-title">
                                        <i class="bi bi-credit-card h1 jcolor-white mb-1"> 0 </i>


                                        <br> {{ __('Cards') }} </h3>
                                    <p class="card-text t400"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card-back" data-height-xl="200" style="background-image: url('demos/ecommerce/images/catagories/2.jpg');">
                        <div class="flip-card-inner">
                            <p class="mb-2 text-white">{{ __('to view your payment options.') }}</p>
                            <button type="button" class="btn btn-outline-light mt-2">{{ __('View Details') }}</button>
                        </div>
                    </div>
                </div>
            </div>




        </div>

        <div class="row">
            <div class="col-sm-4"><h3 class="mb-2"> {{ __('Your Wallet') }}</h3><div class="balance-card card d-flex w-100">

                    <div class="card-body d-flex flex-column">
                        <div class="row balance-color" style="flex: 1;">

                            <div class="row" style="flex: 1;">
                                <div class="col-12  ">
                                    <div class="subtitle mb-2 text-gray">
                                        {{ __('Current balance') }}
                                    </div>
                                    <span class="card-number d-flex align-items-center" id="txtBalance"><span class="icon-saudi_riyal"></span>
                                        {{ $currentBalance }}</span>
                                </div>
                               {{-- <div class="col-md-6 col-12 border-card">
                                    <div class="subtitle mb-2 text-gray d-flex align-items-center">
                                        Tags Reserved
                                    </div>
                                    <span class="card-number d-flex align-items-center" id="txtReserve"><span class="icon-saudi_riyal"></span>
                                                    600.00</span>
                                </div>--}}
                            </div>


                            <div class="col-12" id="topUpBtn">
                                <!-- <a href="/Finance/balance-top-up?lang=En" class="btn-primary w-100 mt-4">
                                    Top up balance
                                </a> -->
                                <a href="{{ url('/wallet/topup') }}" class="button button-border button-small button-rounded button-fill fill-from-right button-blue mt-4">
                                    <i class="fa-solid fa-money-bill-alt"></i><span>{{ __('Add Balance') }}</span></a>
                                <!-- <a href="balance-topup2.html" class="button button-border button-small button-rounded button-fill fill-from-right button-blue mt-4">
                     <i class="fa-solid fa-money-bill-alt"></i><span>Add Balance 2</span></a>-->
                            </div>

                        </div>
                    </div>
                </div></div>
            <div class="col-sm-4" style="    padding-top: 6%;
    padding-left: 5%;">
                <div class="feature-box fbox-plain">
                    <div class="fbox-icon bounceIn animated" data-animate="bounceIn" data-delay="200">
                        <a href="#"><img src="./theme_files/imgs/svg-icons/contract.svg" alt="Retina Graphics"></a>
                    </div>
                    <h3 class="mt-2"><a href="{{ route('services.booking.order.form') }}">{{ __('New Service/Tag') }}</a> </h3>

                </div>

            </div>
            <div class="col-sm-4">
                <br>
                <article class="portfolio-item col-sm-12 col-12">
                    <div class="grid-inner">
                        <div class="portfolio-image">
                            <div class="fslider" data-arrows="true" data-pagi="false" data-speed="400" data-pause="4000">
                                <div class="flexslider">
                                    <div class="slider-wrap">
                                        <div class="slide"><a href="#"><img src="./theme_files/imgs/place-holder-banner.jpg" alt="no banner 1"></a></div>
                                        <div class="slide"><a href="#"><img src="./theme_files/imgs/place-holder-banner.jpg" alt="no banner 2"></a></div>
                                        <div class="slide"><a href="#"><img src="./theme_files/imgs/place-holder-banner.jpg" alt="3 banner no"></a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-overlay" data-lightbox="gallery">
                                <div class="bg-overlay-content dark flex-column">
                                    <div class="portfolio-desc fixed-bottom" >
                                        <h3><a href="#" class="color-discord">{{ __('Promotions') }}</a></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

            </div>

        </div>

        <div class="row col-12 mt-4">
            &nbsp;

        </div>
        <div class="row col-sm-12">
            
            <div class="col-sm-6"> <h3 style="margin-bottom: 18px;"><i class="fa-solid fa-map"></i>	{{ __('Joil Map') }}</h3></div> 
            <div class="col-sm-6 text-end">
                {{-- <a
                name=""
                id=""
                class="btn btn-primary"
                href="#"
                role="button"> <i class="fas fa-map-marker-alt"></i> Get me nearest Station</a> --}}

             </div>
        </div>
        <div class="row col-sm-12">

        <iframe src="{{ route('check-map-marks') }}" width="100%" height="480"></iframe>
             

            <div class="line"></div>

        </div>


    </div>
@endsection

@push('scripts')
    <script>
        jQuery(document).ready(function(){
            jQuery('.videoplay-on-hover').hover( function(){
                if( jQuery(this).find('video').length > 0 ) {
                    jQuery(this).find('video').get(0).play();
                }
            }, function(){
                if( jQuery(this).find('video').length > 0 ) {
                    jQuery(this).find('video').get(0).pause();
                }
            });

            if (jQuery("body").hasClass("dark")) {
                jQuery('.twitter-timeline').attr('data-theme', 'dark');
            }
        });
        // less than 1 second delay to initilize
        setTimeout(() => callDataTable(), 500);

        function callDataTable(){
            jQuery(function () {
                log("Partial jQ  initilizer .");

                jQuery('.datatableOnDashboardActions').dataTable();

            });
        }




        function log(e) {
            console.log(e);
        }


        // Hide the loading overlay.


    </script>
@endpush

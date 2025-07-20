@extends('layouts.app')


@section('title', 'JOIL YASEEIR ONLINE :: User home ')

 
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

                                    <h3 class="card-title"> <i class="bi bi-tags h1 jcolor-white mb-1"> 3 </i> <br> Total Tags </h3>
                                    <p class="card-text t400"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card-back bg-danger no-after" data-height-xl="200" >
                        <div class="flip-card-inner">
                            <p class="mb-2 text-white">To view your paid services.</p>
                            <button type="button" class="btn btn-outline-light mt-2">View Details</button>
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
                                    <h3 class="card-title"> <i class="bi bi-tags h1 jcolor-white mb-1"> 1 </i> <br> Pending Tags To Install </h3>
                                    <p class="card-text t400"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card-back" data-height-xl="200"  style="background-image: url('demos/business/images/featured/3.jpg');">
                        <div class="flip-card-inner">
                            <p class="mb-2 text-white">To view your tags list</p>
                            <button type="button" class="btn btn-outline-light mt-2">View Details</button>
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


                                        <br> Cards </h3>
                                    <p class="card-text t400"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flip-card-back" data-height-xl="200" style="background-image: url('demos/ecommerce/images/catagories/2.jpg');">
                        <div class="flip-card-inner">
                            <p class="mb-2 text-white">to view your payment options.</p>
                            <button type="button" class="btn btn-outline-light mt-2">View Details</button>
                        </div>
                    </div>
                </div>
            </div>




        </div>

        <div class="row">
            <div class="col-sm-4"><h3 class="mb-2"> Your Wallet</h3><div class="balance-card card d-flex w-100">

                    <div class="card-body d-flex flex-column">
                        <div class="row balance-color" style="flex: 1;">

                            <div class="row" style="flex: 1;">
                                <div class="col-12  ">
                                    <div class="subtitle mb-2 text-gray">
                                        Current balance
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
                                    <i class="fa-solid fa-money-bill-alt"></i><span>Add Balance</span></a>
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
                    <h3 class="mt-2"><a href="new-service.html">New Service/Tag</a> </h3>

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
                                        <h3><a href="#" class="color-discord">Promotions</a></h3>
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
            <h3 style="margin-bottom: 18px;"><i class="bi-hurricane"></i>	Actions</h3>

        </div>
        <div class="row col-sm-12">


            <ul class="nav canvas-alt-tabs tabs-alt tabs-tb tabs nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link pending-approvals " data-bs-toggle="pill" data-bs-target="#Pending-tb" type="button" role="tab" aria-controls="canvas-home-tb" aria-selected="true"> Services History <span class="badge bg-success text-white">4</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link notify-approvals active" data-bs-toggle="pill" data-bs-target="#Notifications-tb" type="button" role="tab" aria-controls="canvas-profile-tb" aria-selected="false">Notifications <span class="badge bg-success text-white">3</span></button>
                </li>


            </ul>
            <div class="tab-content">
                <div class="tab-pane fade" id="Pending-tb" role="tabpanel" aria-labelledby="canvas-home-tb-tab" tabindex="0">
                    <div class="col-12 row">
                        <table   class="table table-striped table-bordered dataTable datatableOnDashboardActions" cellspacing="0" width="100%" role="grid" aria-describedby="datatable1_info" style="width: 100%;">
                            <thead>
                            <tr role="row">
                                <th class="sorting sorting_asc" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" width="50">No.</th>
                                <th class="sorting sorting_asc" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 221px;">Service Types</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Status</th>



                                <th class="sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending">Created date</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable1" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending" width="150">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="">
                                <td class="sorting_1">1</td>
                                <td>Tag</td>
                                <td> Pending </td>

                                <td>24-11-2024</td>
                                <td>




                                    <a href="#" class="btn btn-sm btn-outline-primary  ms-2">Details</a>

                                </td>
                            </tr>
                            <tr class="">
                                <td class="sorting_1">1</td>
                                <td>Top up</td>
                                <td> Approve </td>

                                <td>24-11-2024</td>
                                <td>

                                    <a href="#" class="btn btn-sm btn-outline-primary  ms-2">Details</a>

                                </td>
                            </tr>
                            <tr class="">
                                <td class="sorting_1">1</td>
                                <td>Tag</td>
                                <td> Pending </td>

                                <td>24-11-2024</td>
                                <td>



                                    <a href="#"  class="btn btn-sm btn-outline-primary ms-2">Details</a>

                                </td>
                            </tr>

                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="tab-pane fade  show active" id="Notifications-tb" role="tabpanel" aria-labelledby="canvas-profile-tb-tab" tabindex="0">
                    <p>
                    <div class="col-12 row">
                        <div class="alert alert-dismissible alert-success">
                            <i class="bi-boxes"></i><strong>Well done!</strong> You successfully read this <a href="#" class="alert-link">important alert message</a>.
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-hidden="true"></button>
                        </div>
                        <div class="alert alert-dismissible alert-info">
                            <i class="bi-hand-index"></i><strong>Heads up!</strong> This alert needs your attention, but it's not super important.
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-hidden="true"></button>
                        </div>
                        <div class="alert alert-dismissible alert-danger mb-0">
                            <i class="bi-x-circle-fill"></i><strong>Oh snap!</strong> Change a few things up and try submitting again.
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-hidden="true"></button>
                        </div>

                    </div>
                    </p>
                </div>

            </div>

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

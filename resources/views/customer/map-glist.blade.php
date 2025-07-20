@extends('layouts.app')

@section('title', __('Stored Map Locations - JOIL YASEEIR'))

@push('styles')
{{-- Removed DataTables CDN CSS - Already included in project layout --}}
<style>
    .card-icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }
    .dataTables_filter input {
        margin-left: 0.5em;
        display: inline-block;
        width: auto;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85em;
    }
    .page-header {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    .alert ul {
        margin-bottom: 0;
    }
    .dataTables_wrapper .dataTables_filter {
        float: right; /* Align search to the right */
        text-align: right;
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_length {
        float: left; /* Align length menu to the left */
         margin-bottom: 1rem;
    }
    /* Style the filter row */
    #locationsTable tfoot th {
        padding: 0.5rem; /* Add padding to footer cells */
    }
    #locationsTable tfoot select {
        width: 100%; /* Make select fill the cell */
    }

    .items-center.justify-between svg{ width: 30px !important;}
    
    /* Enhanced Arabic RTL Support */
    @if(app()->getLocale() == 'ar')
    body[dir="rtl"] .page-header {
        text-align: right;
    }
    
    body[dir="rtl"] .dataTables_wrapper .dataTables_filter {
        float: left;
        text-align: left;
    }
    
    body[dir="rtl"] .dataTables_wrapper .dataTables_length {
        float: right;
    }
    
    body[dir="rtl"] .dataTables_wrapper .dataTables_info {
        float: right;
        text-align: right;
    }
    
    body[dir="rtl"] .dataTables_wrapper .dataTables_paginate {
        float: left;
    }
    
    body[dir="rtl"] .table {
        text-align: right;
    }
    
    body[dir="rtl"] .table th,
    body[dir="rtl"] .table td {
        text-align: right;
    }
    
    body[dir="rtl"] .table th:first-child,
    body[dir="rtl"] .table td:first-child {
        text-align: center;
    }
    
    body[dir="rtl"] .table th:last-child,
    body[dir="rtl"] .table td:last-child {
        text-align: center;
    }
    
    body[dir="rtl"] .btn-group {
        direction: ltr;
    }
    
    body[dir="rtl"] .card-body {
        text-align: right;
    }
    
    body[dir="rtl"] .card-title {
        text-align: right;
    }
    
    body[dir="rtl"] .d-flex.justify-content-between {
        flex-direction: row-reverse;
    }
    
    body[dir="rtl"] .d-flex.justify-content-center {
        text-align: center;
    }
    
    body[dir="rtl"] .me-2 {
        margin-right: 0 !important;
        margin-left: 0.5rem !important;
    }
    
    body[dir="rtl"] .me-1 {
        margin-right: 0 !important;
        margin-left: 0.25rem !important;
    }
    
    body[dir="rtl"] .ms-2 {
        margin-left: 0 !important;
        margin-right: 0.5rem !important;
    }
    
    body[dir="rtl"] .ms-1 {
        margin-left: 0 !important;
        margin-right: 0.25rem !important;
    }
    
    body[dir="rtl"] .pagination {
        direction: ltr;
    }
    
    body[dir="rtl"] .btn {
        text-align: center;
    }
    
    body[dir="rtl"] .alert {
        text-align: right;
    }
    
    body[dir="rtl"] .text-center {
        text-align: center !important;
    }
    
    body[dir="rtl"] .text-muted {
        text-align: right;
    }
    
    body[dir="rtl"] .container-fluid {
        text-align: right;
    }
    
    /* Numbers and coordinates should remain LTR */
    body[dir="rtl"] .number,
    body[dir="rtl"] .coordinate,
    body[dir="rtl"] .latitude,
    body[dir="rtl"] .longitude {
        direction: ltr;
        text-align: left;
    }
    
    /* DataTables search input RTL */
    body[dir="rtl"] .dataTables_filter input {
        text-align: right;
        direction: rtl;
    }
    
    /* DataTables column filters RTL */
    body[dir="rtl"] #locationsTable tfoot input,
    body[dir="rtl"] #locationsTable tfoot select {
        text-align: right;
        direction: rtl;
    }
    
    /* Badge alignment */
    body[dir="rtl"] .badge {
        text-align: center;
    }
    
    /* Card statistics alignment */
    body[dir="rtl"] .card-text {
        text-align: right;
    }
    
    /* Button group alignment */
    body[dir="rtl"] .btn-outline-primary {
        text-align: center;
    }
    @endif
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0"><i class="fas fa-map-marked-alt {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>{{ __('Locations List') }}</h1>
        <div>
            <button id="clearFilters" class="btn btn-outline-secondary {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}">
                <i class="fas fa-filter-slash {{ app()->getLocale() == 'ar' ? 'ms-1' : 'me-1' }}"></i> {{ __('Clear Filters') }}
            </button>
            <a href="{{ url()->current() }}" class="btn btn-primary">
                <i class="fas fa-sync-alt {{ app()->getLocale() == 'ar' ? 'ms-1' : 'me-1' }}"></i> {{ __('Refresh Data & Sync') }}
            </a>
        </div>
    </div>

    @if (session('syncStatusMessage') || !empty($syncStatusMessage))
        {{-- <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>{{ session('syncStatusMessage') ?? $syncStatusMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> --}}
    @endif

    @if (session('syncErrors') || !empty($syncErrors))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>{{ __('Processing Issues Encountered') }}:</h6>
            <ul>
                @foreach (session('syncErrors') ?? $syncErrors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ __('Total Locations') }}</h5>
                        <p class="card-text fs-4 fw-bold number">{{ $totalCount ?? 0 }}</p>
                    </div>
                    <i class="fas fa-map-marker-alt card-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ __('Operational') }}</h5>
                        <p class="card-text fs-4 fw-bold number">{{ $operationalCount ?? 0 }}</p>
                    </div>
                    <i class="fas fa-check-circle card-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-dark bg-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ __('Maintenance') }}</h5>
                        <p class="card-text fs-4 fw-bold number">{{ $maintenanceCount ?? 0 }}</p>
                    </div>
                    <i class="fas fa-tools card-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-secondary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ __('Other Status') }}</h5>
                        <p class="card-text fs-4 fw-bold number">{{ $otherStatusCount ?? 0 }}</p>
                    </div>
                    <i class="fas fa-question-circle card-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-list-ul {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>{{ __('Location Details') }}</h5>
        </div>
        <div class="card-body">
            @if($locations->isEmpty())
                <div class="alert alert-light text-center">
                    <i class="fas fa-info-circle {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>{{ __('No locations found in the database.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="locationsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Region') }}</th>
                                <th>{{ __('Latitude') }}</th>
                                <th>{{ __('Longitude') }}</th>
                                <th>{{ __('Identifier Code') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($locations as $index => $location)
                                <tr>
                                    <td class="text-center">{{ ($locations->currentPage() - 1) * $locations->perPage() + $loop->iteration }}</td>
                                    <td>{{ $location->name }}</td>
                                    <td>
                                        @php
                                            $statusClass = 'secondary';
                                            $statusText = $location->status;
                                            if (in_array(strtolower($location->status), ['verified', 'operational'])) {
                                                $statusClass = 'success';
                                                $statusText = __('Verified');
                                            } elseif (in_array(strtolower($location->status), ['maintenance', 'under construction'])) {
                                                $statusClass = 'warning';
                                                $statusText = __('Maintenance');
                                            } elseif (in_array(strtolower($location->status), ['closed', 'suspended'])) {
                                                $statusClass = 'danger';
                                                $statusText = __('Closed');
                                            } elseif (strtolower($location->status) === 'unknown') {
                                                $statusClass = 'secondary';
                                                $statusText = __('Unknown');
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>{{ $location->city ?? __('N/A') }}</td>
                                    <td>{{ $location->region ?? __('N/A') }}</td>
                                    <td class="coordinate latitude">{{ number_format($location->latitude, 6) }}</td>
                                    <td class="coordinate longitude">{{ number_format($location->longitude, 6) }}</td>
                                    <td>{{ $location->kml_code ?? __('N/A') }}</td>
                                    <td class="text-center">
                                        <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="{{ __('View on Google Maps') }}">
                                            <i class="fas fa-map-marker-alt"></i> {{ __('View Map') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>  {{-- Footer for Column Filters --}}
                            <tr>
                                <th></th> {{-- No filter for # --}}
                                <th></th> {{-- No filter for Name (or add text input?) --}}
                                <th>{{ __('Status') }}</th> {{-- Filter placeholder --}}
                                <th>{{ __('City') }}</th>   {{-- Filter placeholder --}}
                                <th>{{ __('Region') }}</th> {{-- Filter placeholder --}}
                                <th></th> {{-- No filter for Lat --}}
                                <th></th> {{-- No filter for Lng --}}
                                <th></th> {{-- No filter for Identifier --}}
                                <th></th> {{-- No filter for Actions --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $locations->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
    <hr>
    <p class="text-muted text-center"><small>{{ __('To update this list, ensure the location source file is correctly placed and refresh this page.') }}</small></p>
</div>
@endsection

@push('scripts')
{{-- Removed DataTables CDN JS - Already included in project layout --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var table = $('#locationsTable').DataTable({
            responsive: true,
            paging: false,
            info: false,
            searching: true,
            lengthChange: false,
            autoWidth: false,
            order: [[0, 'asc']],
            language: {
                @if(app()->getLocale() == 'ar')
                "search": "{{ __('Search') }}:",
                "searchPlaceholder": "{{ __('Search locations...') }}",
                "emptyTable": "{{ __('No locations found in the database.') }}",
                "zeroRecords": "{{ __('No matching locations found') }}",
                "info": "{{ __('Showing _START_ to _END_ of _TOTAL_ locations') }}",
                "infoEmpty": "{{ __('Showing 0 to 0 of 0 locations') }}",
                "infoFiltered": "{{ __('(filtered from _MAX_ total locations)') }}",
                "processing": "{{ __('Processing...') }}",
                "lengthMenu": "{{ __('Show _MENU_ entries') }}",
                "paginate": {
                    "first": "{{ __('First') }}",
                    "last": "{{ __('Last') }}",
                    "next": "{{ __('Next') }}",
                    "previous": "{{ __('Previous') }}"
                }
                @else
                "search": "Search:",
                "searchPlaceholder": "Search locations...",
                "emptyTable": "No locations found in the database.",
                "zeroRecords": "No matching locations found",
                "info": "Showing _START_ to _END_ of _TOTAL_ locations",
                "infoEmpty": "Showing 0 to 0 of 0 locations",
                "infoFiltered": "(filtered from _MAX_ total locations)",
                "processing": "Processing...",
                "lengthMenu": "Show _MENU_ entries",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
                @endif
            },
            initComplete: function() {
                var api = this.api();
                var cols = [1,2,3,4,7];
                api.columns(cols).every(function() {
                    var column = this;
                    var idx = column.index();
                    var header = $(column.header()).text().trim();
                    var footer = $(column.footer());
                    if (footer.length) {
                        footer.empty();
                        if (idx === 1 || idx === 7) {
                            $('<input type="text" placeholder="{{ __('Search') }} ' + header + '" class="form-control form-control-sm"/>')
                                .appendTo(footer)
                                .on('keyup change clear', function() {
                                    if (column.search() !== this.value) {
                                        column.search(this.value).draw();
                                    }
                                });
                        } else if (idx === 2 || idx === 3 || idx === 4) {
                            var select = $('<select class="form-control form-control-sm"><option value="">{{ __('All') }}</option></select>')
                                .appendTo(footer)
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                });
                            
                            column.data().unique().sort().each(function(d, j) {
                                if (d) {
                                    var displayText = d;
                                    // Handle status translation
                                    if (idx === 2) {
                                        @if(app()->getLocale() == 'ar')
                                        if (d.toLowerCase() === 'verified') displayText = '{{ __('Verified') }}';
                                        else if (d.toLowerCase() === 'unknown') displayText = '{{ __('Unknown') }}';
                                        else if (d.toLowerCase() === 'maintenance') displayText = '{{ __('Maintenance') }}';
                                        else if (d.toLowerCase() === 'closed') displayText = '{{ __('Closed') }}';
                                        @endif
                                    }
                                    select.append('<option value="' + d + '">' + displayText + '</option>');
                                }
                            });
                        }
                    }
                });
            }
        });

        // Clear filters functionality
        $('#clearFilters').on('click', function() {
            table.search('').columns().search('').draw();
            $('#locationsTable tfoot input, #locationsTable tfoot select').val('');
        });
    });
</script>
@endpush 
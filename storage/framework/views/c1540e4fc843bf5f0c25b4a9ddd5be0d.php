<?php $__env->startSection('title', __('Stored Map Locations - JOIL YASEEIR')); ?>

<?php $__env->startPush('styles'); ?>

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
    <?php if(app()->getLocale() == 'ar'): ?>
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
    <?php endif; ?>
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0"><i class="fas fa-map-marked-alt <?php echo e(app()->getLocale() == 'ar' ? 'ms-2' : 'me-2'); ?>"></i><?php echo e(__('Locations List')); ?></h1>
        <div>
            <button id="clearFilters" class="btn btn-outline-secondary <?php echo e(app()->getLocale() == 'ar' ? 'ms-2' : 'me-2'); ?>">
                <i class="fas fa-filter-slash <?php echo e(app()->getLocale() == 'ar' ? 'ms-1' : 'me-1'); ?>"></i> <?php echo e(__('Clear Filters')); ?>

            </button>
            <a href="<?php echo e(url()->current()); ?>" class="btn btn-primary">
                <i class="fas fa-sync-alt <?php echo e(app()->getLocale() == 'ar' ? 'ms-1' : 'me-1'); ?>"></i> <?php echo e(__('Refresh Data & Sync')); ?>

            </a>
        </div>
    </div>

    <?php if(session('syncStatusMessage') || !empty($syncStatusMessage)): ?>
        
    <?php endif; ?>

    <?php if(session('syncErrors') || !empty($syncErrors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle <?php echo e(app()->getLocale() == 'ar' ? 'ms-2' : 'me-2'); ?>"></i><?php echo e(__('Processing Issues Encountered')); ?>:</h6>
            <ul>
                <?php $__currentLoopData = session('syncErrors') ?? $syncErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('Total Locations')); ?></h5>
                        <p class="card-text fs-4 fw-bold number"><?php echo e($totalCount ?? 0); ?></p>
                    </div>
                    <i class="fas fa-map-marker-alt card-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('Operational')); ?></h5>
                        <p class="card-text fs-4 fw-bold number"><?php echo e($operationalCount ?? 0); ?></p>
                    </div>
                    <i class="fas fa-check-circle card-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-dark bg-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('Maintenance')); ?></h5>
                        <p class="card-text fs-4 fw-bold number"><?php echo e($maintenanceCount ?? 0); ?></p>
                    </div>
                    <i class="fas fa-tools card-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-secondary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?php echo e(__('Other Status')); ?></h5>
                        <p class="card-text fs-4 fw-bold number"><?php echo e($otherStatusCount ?? 0); ?></p>
                    </div>
                    <i class="fas fa-question-circle card-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-list-ul <?php echo e(app()->getLocale() == 'ar' ? 'ms-2' : 'me-2'); ?>"></i><?php echo e(__('Location Details')); ?></h5>
        </div>
        <div class="card-body">
            <?php if($locations->isEmpty()): ?>
                <div class="alert alert-light text-center">
                    <i class="fas fa-info-circle <?php echo e(app()->getLocale() == 'ar' ? 'ms-2' : 'me-2'); ?>"></i><?php echo e(__('No locations found in the database.')); ?>

                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="locationsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo e(__('Name')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <th><?php echo e(__('City')); ?></th>
                                <th><?php echo e(__('Region')); ?></th>
                                <th><?php echo e(__('Latitude')); ?></th>
                                <th><?php echo e(__('Longitude')); ?></th>
                                <th><?php echo e(__('Identifier Code')); ?></th>
                                <th><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center"><?php echo e(($locations->currentPage() - 1) * $locations->perPage() + $loop->iteration); ?></td>
                                    <td><?php echo e($location->name); ?></td>
                                    <td>
                                        <?php
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
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>">
                                            <?php echo e($statusText); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($location->city ?? __('N/A')); ?></td>
                                    <td><?php echo e($location->region ?? __('N/A')); ?></td>
                                    <td class="coordinate latitude"><?php echo e(number_format($location->latitude, 6)); ?></td>
                                    <td class="coordinate longitude"><?php echo e(number_format($location->longitude, 6)); ?></td>
                                    <td><?php echo e($location->kml_code ?? __('N/A')); ?></td>
                                    <td class="text-center">
                                        <a href="https://www.google.com/maps?q=<?php echo e($location->latitude); ?>,<?php echo e($location->longitude); ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="<?php echo e(__('View on Google Maps')); ?>">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo e(__('View Map')); ?>

                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>  
                            <tr>
                                <th></th> 
                                <th></th> 
                                <th><?php echo e(__('Status')); ?></th> 
                                <th><?php echo e(__('City')); ?></th>   
                                <th><?php echo e(__('Region')); ?></th> 
                                <th></th> 
                                <th></th> 
                                <th></th> 
                                <th></th> 
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-3">
                    <?php echo e($locations->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
    <hr>
    <p class="text-muted text-center"><small><?php echo e(__('To update this list, ensure the location source file is correctly placed and refresh this page.')); ?></small></p>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>


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
                <?php if(app()->getLocale() == 'ar'): ?>
                "search": "<?php echo e(__('Search')); ?>:",
                "searchPlaceholder": "<?php echo e(__('Search locations...')); ?>",
                "emptyTable": "<?php echo e(__('No locations found in the database.')); ?>",
                "zeroRecords": "<?php echo e(__('No matching locations found')); ?>",
                "info": "<?php echo e(__('Showing _START_ to _END_ of _TOTAL_ locations')); ?>",
                "infoEmpty": "<?php echo e(__('Showing 0 to 0 of 0 locations')); ?>",
                "infoFiltered": "<?php echo e(__('(filtered from _MAX_ total locations)')); ?>",
                "processing": "<?php echo e(__('Processing...')); ?>",
                "lengthMenu": "<?php echo e(__('Show _MENU_ entries')); ?>",
                "paginate": {
                    "first": "<?php echo e(__('First')); ?>",
                    "last": "<?php echo e(__('Last')); ?>",
                    "next": "<?php echo e(__('Next')); ?>",
                    "previous": "<?php echo e(__('Previous')); ?>"
                }
                <?php else: ?>
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
                <?php endif; ?>
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
                            $('<input type="text" placeholder="<?php echo e(__('Search')); ?> ' + header + '" class="form-control form-control-sm"/>')
                                .appendTo(footer)
                                .on('keyup change clear', function() {
                                    if (column.search() !== this.value) {
                                        column.search(this.value).draw();
                                    }
                                });
                        } else if (idx === 2 || idx === 3 || idx === 4) {
                            var select = $('<select class="form-control form-control-sm"><option value=""><?php echo e(__('All')); ?></option></select>')
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
                                        <?php if(app()->getLocale() == 'ar'): ?>
                                        if (d.toLowerCase() === 'verified') displayText = '<?php echo e(__('Verified')); ?>';
                                        else if (d.toLowerCase() === 'unknown') displayText = '<?php echo e(__('Unknown')); ?>';
                                        else if (d.toLowerCase() === 'maintenance') displayText = '<?php echo e(__('Maintenance')); ?>';
                                        else if (d.toLowerCase() === 'closed') displayText = '<?php echo e(__('Closed')); ?>';
                                        <?php endif; ?>
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
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp81\htdocs\aljeri-joil-yaseer-o3mhigh\resources\views/customer/map-glist.blade.php ENDPATH**/ ?>
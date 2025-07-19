<script src="<?php echo e(asset('theme_files/js/jquery.js')); ?>"></script>
<script src="<?php echo e(asset('theme_files/js/plugins.min.js')); ?>"></script>
<script src="<?php echo e(asset('theme_files/js/plugins.fitvids.js')); ?>"></script>
<script src="<?php echo e(asset('theme_files/js/plugins.lightbox.js')); ?>"></script>
<script src="<?php echo e(asset('theme_files/js/plugins.flexslider.js')); ?>"></script>
<script src="<?php echo e(asset('theme_files/js/functions.bundle.js')); ?>"></script>

<script src="<?php echo e(asset('theme_files/js/core.js')); ?>"></script>


    <script src="<?php echo e(asset('theme_files/js/components/select-boxes.js')); ?>"></script>
    <script src="<?php echo e(asset('theme_files/js/components/selectsplitter.js')); ?>"></script>
    <script src="<?php echo e(asset('theme_files/js/components/bs-select.js')); ?>"></script>
    <script src="<?php echo e(asset('theme_files/js/components/bs-datatable.js')); ?>"></script>

    <script src="<?php echo e(asset('theme_files/js/components/bs-filestyle.js')); ?>"></script>
    <script src="<?php echo e(asset('theme_files/js/components/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('theme_files/js/components/bs-switches.js')); ?>"></script>
    <script src="<?php echo e(asset('theme_files/js/components/dataTables.checkboxes.min.js')); ?>"></script>
<script src="<?php echo e(asset('theme_files/js/js-loading-overlay.min.js')); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // Any other global jQuery functions or event handlers
    $(document).ready(function(){
        // Example: Prevent multiple clicks on links with .prevent-multiple class
        $('.prevent-multiple').click(function(e){
            $(this).addClass('disabled');
        });
    });
    
    // Language switching function
    function changeLanguage(locale) {
        // Show loading overlay
        if (typeof LoadingOverlay !== 'undefined') {
            LoadingOverlay.show();
        }
        
        // Redirect to language change route
        window.location.href = '<?php echo e(route("lang.change", "")); ?>/' + locale;
    }
</script>
<?php /**PATH C:\xampp81\htdocs\aljeri-joil-yaseer-o3mhigh\resources\views/partials/scripts.blade.php ENDPATH**/ ?>
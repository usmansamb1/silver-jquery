{{-- resources/views/partials/loader.blade.php --}}
{{--
<div id="globalLoader">
    <div class="spinner"></div>
</div>

<style>
    /* Global loader overlay styling */
    #globalLoader {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
        z-index: 9999; /* Very high to cover everything */
        pointer-events: all; /* Prevent clicks on underlying elements */
    }

    /* Spinner styling */
    .spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 80px;
        height: 80px;
        margin: -40px 0 0 -40px;
        border: 12px solid #ffffff;
        border-top: 12px solid #194a9f; /* Blue color for the animated segment */
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>--}}
<script>
    // JsLoadingOverlay.show({
    // "overlayBackgroundColor": "#DBD0D0",
    // "overlayOpacity": 0.6,
    // "spinnerIcon": "line-scale",
    // "spinnerColor": "#E11919",
    // "spinnerSize": "3x",
    // "overlayIDName": "overlay",
    // "spinnerIDName": "spinner",
    // "offsetX": 0,
    // "offsetY": 0,
    // "containerID": null,
    // "lockScroll": true,
    // "overlayZIndex": 99998,
    // "spinnerZIndex": 99999
    // });
    jQuery(document).ready(function () {
    //     $('.LoadingUi').on('click', function (event) {
    //     //$('.modal').modal('hide');
    //     JsLoadingOverlay.show(JsLoadingOverlay);
    //     });

    // // Hide the loading overlay.
    // JsLoadingOverlay.hide();
    });
</script>

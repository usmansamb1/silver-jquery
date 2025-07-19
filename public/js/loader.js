// public/js/loader.js
(function($) {
    // Function to show the global loader
    function showLoader() {
        $("#globalLoader").fadeIn();
    }

    // Function to hide the global loader
    function hideLoader() {
        $("#globalLoader").fadeOut();
    }

    // Attach to jQuery AJAX global events
    $(document).ajaxStart(function(){
        showLoader();
    }).ajaxStop(function(){
        hideLoader();
    });

    // If you're using Axios, attach interceptors to handle loader display automatically.
    if (window.axios) {
        axios.interceptors.request.use(function (config) {
            showLoader();
            return config;
        }, function (error) {
            hideLoader();
            return Promise.reject(error);
        });

        axios.interceptors.response.use(function (response) {
            hideLoader();
            return response;
        }, function (error) {
            hideLoader();
            return Promise.reject(error);
        });
    }

    // Expose functions globally if you need to trigger them manually.
    window.showLoader = showLoader;
    window.hideLoader = hideLoader;
})(jQuery);

<script>
// CSP Override for Hyperpay - Remove all CSP restrictions
(function() {
    console.log('🔧 CSP Override: Attempting to disable Content Security Policy for Hyperpay...');
    
    // Method 1: Remove existing CSP meta tags
    var cspMetas = document.querySelectorAll('meta[http-equiv*="Content-Security-Policy"]');
    cspMetas.forEach(function(meta) {
        console.log('🗑️ Removing CSP meta tag:', meta.getAttribute('content'));
        meta.remove();
    });
    
    // Method 2: Override CSP with permissive policy
    var newCSP = document.createElement('meta');
    newCSP.httpEquiv = 'Content-Security-Policy';
    newCSP.content = "script-src * 'unsafe-inline' 'unsafe-eval' data:; object-src *; style-src * 'unsafe-inline'; img-src * data:; connect-src *; frame-src *; font-src *; media-src *; child-src *; worker-src *; default-src *;";
    document.head.appendChild(newCSP);
    console.log('✅ CSP Override: Added permissive CSP policy');
    
    // Method 3: Try to remove CSP from response headers (limited success in browser)
    if (typeof(window.chrome) !== "undefined" && window.chrome.webstore) {
        console.log('🔧 Chrome detected - CSP might be set by server headers');
    }
    
    // Method 4: Force reload Hyperpay scripts after CSP removal
    setTimeout(function() {
        var existingScripts = document.querySelectorAll('script[src*="techlab-cdn.com"], script[src*="oppwa.com"]');
        existingScripts.forEach(function(script) {
            var newScript = document.createElement('script');
            newScript.src = script.src + '?csp_override=' + Date.now();
            newScript.async = true;
            newScript.onload = function() {
                console.log('✅ Reloaded script:', this.src);
            };
            newScript.onerror = function() {
                console.error('❌ Failed to reload script:', this.src);
            };
            document.head.appendChild(newScript);
        });
    }, 1000);
    
    console.log('🎯 CSP Override completed - Hyperpay scripts should now load');
})();
</script> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translation System Test Interface</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-result {
            margin-bottom: 1rem;
        }
        
        .status-success {
            color: #28a745;
        }
        
        .status-missing {
            color: #dc3545;
        }
        
        .status-failed {
            color: #dc3545;
        }
        
        .status-correctly_missing {
            color: #6c757d;
        }
        
        .performance-excellent {
            color: #28a745;
        }
        
        .performance-good {
            color: #ffc107;
        }
        
        .performance-slow {
            color: #dc3545;
        }
        
        .code-block {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        
        .translation-key {
            font-weight: bold;
            color: #0d6efd;
        }
        
        .translation-value {
            color: #198754;
        }
        
        .loading {
            display: none;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        .rtl {
            direction: rtl;
            text-align: right;
        }
        
        .ltr {
            direction: ltr;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-language"></i>
                    Translation System Test Interface
                </h1>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Test Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="locale" class="form-label">Language:</label>
                                    <select class="form-select" id="locale">
                                        <option value="en">English</option>
                                        <option value="ar">Arabic (العربية)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button class="btn btn-primary" onclick="runTests()">
                                            <i class="fas fa-play"></i>
                                            Run Tests
                                        </button>
                                        <button class="btn btn-secondary ms-2" onclick="clearCache()">
                                            <i class="fas fa-trash"></i>
                                            Clear Cache
                                        </button>
                                        <button class="btn btn-info ms-2" onclick="refreshTranslations()">
                                            <i class="fas fa-refresh"></i>
                                            Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="loading text-center p-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Running translation tests...</p>
                </div>
                
                <div id="results" style="display: none;">
                    <!-- Test results will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script>
        // Set up CSRF token for axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function runTests() {
            const locale = document.getElementById('locale').value;
            
            // Show loading
            document.querySelector('.loading').style.display = 'block';
            document.getElementById('results').style.display = 'none';
            
            axios.post('/translation-test/test', { locale: locale })
                .then(response => {
                    displayResults(response.data);
                })
                .catch(error => {
                    console.error('Error running tests:', error);
                    alert('Error running tests. Please check console for details.');
                })
                .finally(() => {
                    document.querySelector('.loading').style.display = 'none';
                });
        }
        
        function displayResults(data) {
            const resultsContainer = document.getElementById('results');
            const locale = data.locale;
            const results = data.results;
            const overall = data.overall_status;
            
            let html = `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line"></i>
                            Overall Test Results (${locale.toUpperCase()})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-primary">${overall.total_tests}</h3>
                                    <p class="text-muted">Total Tests</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-success">${overall.passed_tests}</h3>
                                    <p class="text-muted">Passed</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-danger">${overall.failed_tests}</h3>
                                    <p class="text-muted">Failed</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h3 class="text-info">${overall.success_rate}%</h3>
                                    <p class="text-muted">Success Rate</p>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3">
                            <div class="progress-bar ${getStatusColor(overall.status)}" role="progressbar" style="width: ${overall.success_rate}%">
                                ${overall.success_rate}%
                            </div>
                        </div>
                        <p class="mt-2 mb-0">Status: <span class="badge ${getStatusBadge(overall.status)}">${overall.status.toUpperCase()}</span></p>
                    </div>
                </div>
            `;
            
            // Basic Translations
            html += generateTestSection('Basic Translations', results.basic_translations, 'check-circle');
            
            // Nested Translations
            html += generateTestSection('Nested Translations', results.nested_translations, 'sitemap');
            
            // Module Translations
            html += generateModuleSection('Module Translations', results.module_translations);
            
            // Parameter Translations
            html += generateTestSection('Parameter Translations', results.parameter_translations, 'code');
            
            // Missing Translations
            html += generateTestSection('Missing Translations (Expected)', results.missing_translations, 'exclamation-triangle');
            
            // Performance
            html += generatePerformanceSection('Performance', results.performance);
            
            // Statistics
            html += generateStatisticsSection('Translation Statistics', results.statistics);
            
            resultsContainer.innerHTML = html;
            resultsContainer.style.display = 'block';
        }
        
        function generateTestSection(title, tests, icon) {
            let html = `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-${icon}"></i>
                            ${title}
                        </h5>
                    </div>
                    <div class="card-body">
            `;
            
            for (const key in tests) {
                const test = tests[key];
                const statusClass = `status-${test.status}`;
                const statusIcon = getStatusIcon(test.status);
                
                html += `
                    <div class="test-result">
                        <div class="row">
                            <div class="col-md-4">
                                <span class="translation-key">${test.key}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="translation-value">${test.translation}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="${statusClass}">
                                    <i class="fas fa-${statusIcon}"></i>
                                    ${test.status}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            html += `
                    </div>
                </div>
            `;
            
            return html;
        }
        
        function generateModuleSection(title, modules) {
            let html = `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cube"></i>
                            ${title}
                        </h5>
                    </div>
                    <div class="card-body">
            `;
            
            for (const key in modules) {
                const module = modules[key];
                const statusClass = `status-${module.status}`;
                const statusIcon = getStatusIcon(module.status);
                
                html += `
                    <div class="test-result">
                        <div class="row">
                            <div class="col-md-3">
                                <span class="translation-key">${module.module}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="badge bg-info">${module.count} keys</span>
                            </div>
                            <div class="col-md-5">
                                <small class="text-muted">Sample: ${module.sample_keys.join(', ')}</small>
                            </div>
                            <div class="col-md-2">
                                <span class="${statusClass}">
                                    <i class="fas fa-${statusIcon}"></i>
                                    ${module.status}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            html += `
                    </div>
                </div>
            `;
            
            return html;
        }
        
        function generatePerformanceSection(title, performance) {
            return `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tachometer-alt"></i>
                            ${title}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h4 class="performance-${performance.load_status}">${performance.load_time_ms}ms</h4>
                                    <p class="text-muted">Load Time</p>
                                    <span class="badge ${getStatusBadge(performance.load_status)}">${performance.load_status}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h4 class="performance-${performance.lookup_status}">${performance.lookup_time_ms}ms</h4>
                                    <p class="text-muted">Lookup Time</p>
                                    <span class="badge ${getStatusBadge(performance.lookup_status)}">${performance.lookup_status}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function generateStatisticsSection(title, statistics) {
            let html = `
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar"></i>
                            ${title}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
            `;
            
            for (const locale in statistics) {
                const stats = statistics[locale];
                html += `
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">${locale.toUpperCase()}</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Keys:</strong> ${stats.total_keys}</p>
                                <p><strong>Nested Keys:</strong> ${stats.nested_keys}</p>
                                <p><strong>Modules Loaded:</strong> ${stats.modules_loaded}</p>
                                <p><strong>Cache Key:</strong> <code>${stats.cache_key}</code></p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            html += `
                        </div>
                    </div>
                </div>
            `;
            
            return html;
        }
        
        function getStatusIcon(status) {
            switch (status) {
                case 'success':
                    return 'check';
                case 'missing':
                case 'failed':
                    return 'times';
                case 'correctly_missing':
                    return 'info';
                default:
                    return 'question';
            }
        }
        
        function getStatusColor(status) {
            switch (status) {
                case 'excellent':
                    return 'bg-success';
                case 'good':
                    return 'bg-warning';
                case 'needs_improvement':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        }
        
        function getStatusBadge(status) {
            switch (status) {
                case 'excellent':
                case 'success':
                    return 'bg-success';
                case 'good':
                    return 'bg-warning';
                case 'needs_improvement':
                case 'slow':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        }
        
        function clearCache() {
            axios.post('/translation-test/clear-cache')
                .then(response => {
                    alert('Translation cache cleared successfully!');
                })
                .catch(error => {
                    console.error('Error clearing cache:', error);
                    alert('Error clearing cache. Please check console for details.');
                });
        }
        
        function refreshTranslations() {
            axios.post('/translation-test/refresh')
                .then(response => {
                    alert('Translations refreshed successfully!');
                })
                .catch(error => {
                    console.error('Error refreshing translations:', error);
                    alert('Error refreshing translations. Please check console for details.');
                });
        }
        
        // Run tests on page load
        document.addEventListener('DOMContentLoaded', function() {
            runTests();
        });
    </script>
</body>
</html>
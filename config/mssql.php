<?php

return [
    // Character encoding configuration
    'encoding' => [
        'connection' => 'UTF-8',
        'database' => 'Arabic_CI_AS', // Recommended collation for Arabic
    ],
    
    // Database-specific query options
    'query_options' => [
        // Set these options when connecting
        'connection_options' => [
            PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::SQLSRV_ATTR_DIRECT_QUERY => true,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
]; 
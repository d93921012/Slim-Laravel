<?php
return [
    // 設定要啟動的 service
    'services' => [
        'cache',  // require "illuminate/cache": "5.0.*",
    ],
    // 顯示錯誤訊息的網頁
    'handlers' => [
        'error' => 'errors.500', // Error 500 - Internal Server Error
        'notFound' => 'errors.404', // Error 404 - Not Found
        'tokenMismatched' => 'errors.token-mismatch',  // Error 500 - Token Mismatched
    ]
];

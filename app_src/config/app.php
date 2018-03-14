<?php
return [
    'services' => [
        'cache',  // require "illuminate/cache": "5.0.*",
    ],
    'handlers' => [
        'error' => 'errors.500',
        'notFound' => 'errors.404',
    ],
];

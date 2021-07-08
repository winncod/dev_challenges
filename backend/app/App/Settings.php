<?php

declare(strict_types=1);

return [
    'settings' => [
        'displayErrorDetails' => filter_var($_SERVER['DISPLAY_ERROR_DETAILS'], FILTER_VALIDATE_BOOLEAN),
        'redis' => [
            'enabled' => $_SERVER['REDIS_ENABLED'],
            'url' => $_SERVER['REDIS_URL'],
        ],
        'app' => [
            'domain' => $_SERVER['APP_DOMAIN']
        ],
    ],
];

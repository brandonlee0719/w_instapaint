<?php

\Core\Api\ApiManager::register([
    'attachment/' => [
        'api_service' => 'attachment.api',
        'maps' => [
            'post' => 'post'
        ],
    ],
]);
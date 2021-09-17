<?php

\Core\Api\ApiManager::register([
    'like' => [
        'api_service' => 'like.api',
        'maps' => [
            'post' => 'post',
            'delete' => 'delete',
            'get' => 'get'
        ]
    ],
]);
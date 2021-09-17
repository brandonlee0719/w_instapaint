<?php

\Core\Api\ApiManager::register([
    'feed/:id' => [
        'api_service' => 'feed.api',
        'maps' => [
            'get' => 'get',
            'put' => 'put',
            'delete' => 'delete'
        ],
        'where' => ['id'=>'\d+']
    ],
    'feed' => [
        'api_service' => 'feed.api',
        'maps' => [
            'get' => 'gets',
            'post' => 'post'
        ]
    ],
    'feed/share' => [
        'api_service' => 'feed.api',
        'maps' => [
            'post' => 'share'
        ]
    ],
]);
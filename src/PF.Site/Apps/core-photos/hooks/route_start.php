<?php

\Core\Api\ApiManager::register([
    'photo/:id' => [
        'api_service' => 'photo.api',
        'maps' => [
            'get' => 'get',
            'put' => 'put',
            'delete' => 'delete'
        ],
        'where' => ['id'=>'\d+']
    ],
    'photo' => [
        'api_service' => 'photo.api',
        'maps' => [
            'get' => 'gets',
            'post' => 'post'
        ]
    ],
]);
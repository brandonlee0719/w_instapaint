<?php

\Core\Api\ApiManager::register([
    'comment/:id' => [
        'api_service' => 'comment.api',
        'maps' => [
            'get' => 'get',
            'put' => 'put',
            'delete' => 'delete'
        ],
        'where' => ['id'=>'\d+']
    ],
    'comment' => [
        'api_service' => 'comment.api',
        'maps' => [
            'get' => 'gets',
            'post' => 'post'
        ]
    ],
]);
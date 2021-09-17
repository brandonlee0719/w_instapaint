<?php

\Core\Api\ApiManager::register([
    //module blog
    'blog/:id' => [
        'api_service' => 'blog.api',
        'maps' => [
            'get' => 'get',
            'put' => 'put',
            'delete' => 'delete'
        ],
        'where' => ['id'=>'\d+']
    ],
    'blog' => [
        'api_service' => 'blog.api',
        'maps' => [
            'get' => 'gets',
            'post' => 'post'
        ]
    ],
]);

<?php

\Core\Api\ApiManager::register([
    'friend' => [
        'api_service' => 'friend.api',
        'maps' => [
            'get' => 'get',
            'delete' => 'delete'
        ],
    ],
    'friend/request' => [
        'api_service' => 'friend.api',
        'maps' => [
            'post' => 'addRequest',
            'delete' => 'cancelRequest',
            'put' => 'processRequest'
        ],
    ],
]);
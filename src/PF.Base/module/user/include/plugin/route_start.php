<?php
defined('PHPFOX') or exit('NO DICE!');

\Core\Api\ApiManager::register([
    'user/:id' => [
        'api_service' => 'user.api',
        'maps' => [
            'get' => 'get',
            'put' => 'put',
            'delete' => 'delete'
        ],
        'where' => ['id'=>'\d+']
    ],
    'user' => [
        'api_service' => 'user.api',
        'maps' => [
            'get' => 'gets',
            'post' => 'post'
        ]
    ],
    'user/custom/:id' => [
        'api_service' => 'user.api',
        'maps' => [
            'get' => 'getCustom',
            'put' => 'putCustom'
        ],
        'where' => ['id'=>'\d+']
    ],
    'user/mine' => [
        'api_service' => 'user.api',
        'maps' => [
            'get' => 'getMine'
        ]
    ],
]);
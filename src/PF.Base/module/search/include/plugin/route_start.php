<?php
defined('PHPFOX') or exit('NO DICE!');

\Core\Api\ApiManager::register([
    'search' => [
        'api_service' => 'search.api',
        'maps' => [
            'get' => 'get'
        ]
    ],
]);
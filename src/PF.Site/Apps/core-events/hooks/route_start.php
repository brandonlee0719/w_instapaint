<?php

\Core\Api\ApiManager::register([
    'event/:id/rsvp' => [
        'api_service' => 'event.api',
        'maps' => [
            'put' => 'updateRsvp'
        ],
        'where' => ['id' => '\d+']
    ],
    'event/:id/guests' => [
        'api_service' => 'event.api',
        'maps' => [
            'get' => 'getGuests'
        ],
        'where' => ['id' => '\d+']
    ],
    'event/:id' => [
        'api_service' => 'event.api',
        'maps' => [
            'get' => 'get',
            'put' => 'put',
            'delete' => 'delete'
        ],
        'where' => ['id' => '\d+']
    ],
    'event' => [
        'api_service' => 'event.api',
        'maps' => [
            'get' => 'gets',
            'post' => 'post'
        ],
    ],
]);
<?php
$aValidation = [
    'keep_active_posts' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Active Posts" must be greater than 0'),
    ],
    'total_posts_per_thread' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Show posts per thread at first time" must be greater than 0'),
    ],
    'total_forum_tags_display' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Total Tag Display" must be greater than 0'),
    ]
];


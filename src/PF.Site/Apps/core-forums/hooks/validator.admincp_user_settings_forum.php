<?php
$aValidation = [
    'points_forum' => [
        'def' => 'int:required',
        'min' => '0',
        'title' => _p('"Points received when adding a thread/post within the forum" must be greater than or equal to 0'),
    ],
    'forum_thread_flood_control' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Time to wait before user can post a new thread" must be greater than or equal to 0'),
    ],
    'forum_post_flood_control' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Time to wait before user can post a new reply to a thread" must be greater than or equal to 0'),
    ],
];
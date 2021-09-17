<?php
defined('PHPFOX') or exit('NO DICE!');
$aValidation = [
    'flood_control_blog' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Time to wait before add another blog" must be greater than or equal to 0'),
    ],
    'points_blog' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater than or equal to 0'),
    ],
    'blog_photo_max_upload_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum photo size of blog" must be greater than or equal to 0'),
    ],
    'blog_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor blog price" must be greater than or equal to 0'),
    ],
    'total_blogs_displays' => [
        'def' => 'array:required',
        'subdef' => 'int:required',
        'min' => '0',
        'title' => _p('Each value of "Define how many blogs a user can view at once when browsing the public blog section?" must be greater than 0'),
    ]
];
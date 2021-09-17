<?php

$aValidation = [
    'points_photo'=>[
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater than or equal to 0'),
    ],
    'max_images_per_upload' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max images per upload" must be greater than 0'),
    ],
    'max_number_of_albums' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max number of albums" must be greater than or equal to 0'),
    ],
    'photo_mature_age_limit' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Photo mature age limit" must be greater than 0'),
    ],
    'how_many_tags_on_own_photo' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"How many times can a user tag their own photo?" must be greater than or equal to 0'),
    ],
    'how_many_tags_on_other_photo' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"How many times can a user tag photos added by other users?" must be greater than or equal to 0'),
    ],
    'photo_max_upload_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for photos upload" must be greater than or equal to 0'),
    ],
    'maximum_image_width_keeps_in_server' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Max image width keeps in server" must be greater than 0'),
    ],
    'photo_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor photo price" must be greater than or equal to 0'),
    ],
    'total_photos_displays' => [
        'def' => 'array:required',
        'subdef' => 'int:required',
        'min' => '0',
        'title' => _p('Each value of "Define how many images a user can view at once when browsing the public photo section" must be greater than 0'),
    ]
];

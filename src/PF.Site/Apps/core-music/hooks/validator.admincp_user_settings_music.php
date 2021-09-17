<?php

$aValidation = [
    'max_songs_per_upload' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Maximum number of songs per upload" must be greater than 0'),
    ],
    'points_music_song' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater than or equal to 0'),
    ],
    'music_max_file_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Maximum file size of songs uploaded" must be greater than or equal to 0'),
    ],
    'music_album_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor album price" must be greater than or equal to 0'),
    ],
    'music_song_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor song price" must be greater than or equal to 0'),
    ]
];
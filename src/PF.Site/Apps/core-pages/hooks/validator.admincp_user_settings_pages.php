<?php
$aValidation = [
    'max_upload_size_pages' => [
        'title' => _p('validation_max_upload_size_pages_phrase'),
        'def' => 'int:required',
        'min' => 0,
    ],
    'points_pages' => [
        'title' => _p('validation_points_pages_phrase'),
        'min' => 0,
        'def' => 'int:required',
    ],
    'flood_control' => [
        'title' => _p('validation_flood_control_phrase'),
        'min' => 0,
        'def' => 'int:required',
    ],
];

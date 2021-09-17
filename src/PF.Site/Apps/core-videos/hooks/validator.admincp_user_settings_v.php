<?php
$aValidation['pf_video_file_size'] = [
    'def' => 'int:required',
    'min' => 1,
    'title' => '"Maximum file size of video uploaded" must be greater than 0'
];
$aValidation['pf_video_max_file_size_photo_upload'] = [
    'def' => 'int:required',
    'min' => 1,
    'title' => '"Maximum file size of photo uploaded" must be greater than 0'
];
$aValidation['v_sponsor_price'] = [
    'def' => 'currency',
    'title' => '"How much is the sponsor space worth for videos? This works in a CPM basis." must be greater than or equal to 0'
];
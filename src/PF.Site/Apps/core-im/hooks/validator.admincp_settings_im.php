<?php
$aValidation = [
    'pf_total_conversations' => [
        'title' => _p('validator_im_pf_total_conversations'),
        'def' => 'int:required',
        'min' => 0
    ],
    'pf_time_to_delete_message' => [
        'title' => _p('validator_im_pf_time_to_delete_message'),
        'def' => 'int:required',
        'min' => 0
    ]
];

<?php

$aValidation['comment_page_limit'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => _p('"Comment page limit" must be greater than 0'),
];
$aValidation['thread_comment_total_display'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => _p('"Total Nested Comments" must be greater than 0'),
];
$aValidation['comments_to_check'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => _p('"Comments To Check" must be greater than 0'),
];
$aValidation['total_comments_in_activity_feed'] = [
    'def' => 'int',
    'min' => '0',
    'title' => _p('"Total Comments in Activity Feed" be greater than or equal to 0'),
];
$aValidation['total_minutes_to_wait_for_comments'] = [
    'def' => 'int',
    'min' => '0',
    'title' => _p('"Comment Minutes to Wait Until Next Check" be greater than or equal to 0'),
];

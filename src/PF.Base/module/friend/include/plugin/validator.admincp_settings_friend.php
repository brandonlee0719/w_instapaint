<?php

$aValidation['friend_display_limit'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => 'Friend display limit must be greater than 0',
];
$aValidation['friend_suggestion_search_total'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => '"Friends Suggestion Friends Check Count" must be greater than 0',
];
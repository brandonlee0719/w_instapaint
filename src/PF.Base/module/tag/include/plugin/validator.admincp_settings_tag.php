<?php
defined('PHPFOX') or exit('NO DICE!');

$aValidation['tag_trend_total_display'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => 'Total tag to display must be greater than 0',
];

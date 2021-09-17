<?php
$aValidation['total_mail_messages_to_check'] = [
    'def' => 'int:required',
    'min' => 0,
    'title' => '"PM Messages to Check" is required and must be a positive integer'
];
$aValidation['total_minutes_to_wait_for_pm'] = [
    'def' => 'int:required',
    'min' => 0,
    'title' => '"PM Minutes to Wait Until Next Check" is required and must be a positive integer'
];

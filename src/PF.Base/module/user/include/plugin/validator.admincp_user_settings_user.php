<?php
defined('PHPFOX') or exit('NO DICE!');

$aValidation['total_times_can_change_user_name'] = [
    'def' => 'int',
    'min' => '0',
    'title' => '"How many times can this user group edit their user name" must be greater than or equal to 0',
];
$aValidation['total_times_can_change_own_full_name'] = [
    'def' => 'int',
    'min' => '0',
    'title' => '"How many times can members of this user group change their full name?" must be greater than or equal to 0',
];

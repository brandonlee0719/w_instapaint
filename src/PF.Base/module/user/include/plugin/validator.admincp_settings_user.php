<?php
defined('PHPFOX') or exit('NO DICE!');

$aValidation['date_of_birth_start'] = [
    'def' => 'int:required',
    'min' => '1900',
    'max' => '2017',
    'title' => '"Date of Birth (Start)" the range is accepted: 1900 -> 2017',
];
$aValidation['date_of_birth_end'] = [
    'def' => 'int:required',
    'min' => '1900',
    'max' => '2017',
    'requirements'=>[
        'min'=> '$date_of_birth_start',
    ],
    'title' => '"Date of Birth (End)" the range is accepted: 1900 -> 2017, and large than "Date of Birth (Start)"',
];
$aValidation['maximum_length_for_full_name'] = [
    'def' => 'int:required',
    'min' => '5',
    'requirements'=>[
        'min'=> '$min_length_for_username',
    ],
    'title' => '"Maximum Length for Full Name" must be greater than 4 and large than "Minimum Length for Username"',
];
$aValidation['min_length_for_username'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => '"Minimum Length for Username" must be greater than 0.',
];
$aValidation['max_length_for_username'] = [
    'def' => 'int:required',
    'min' => '1',
    'requirements'=>[
        'min'=> '$min_length_for_username',
    ],
    'title' => '"Maximum Length for Username" must be greater than "Minimum Length for Username"',
];

$aValidation['check_status_updates'] = [
    'def' => 'int',
    'min' => '0',
    'title' => _p('"Spam Check Status Updates" be greater than or equal to 0'),
];

<?php
defined('PHPFOX') or exit('NO DICE!');

$aValidation['ad_multi_ad_count'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => _p('"Ads in Multi-Ad Location" must be greater than 0'),
];

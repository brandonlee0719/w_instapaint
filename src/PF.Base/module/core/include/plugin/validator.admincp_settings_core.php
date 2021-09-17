<?php

$aValidation['auto_deny_items'] = [
    'def' => 'int:required',
    'min' => '2',
    'title' => _p('"SPAM Count" must be greater than 1'),
];


$aValidation['cookie_path'] = [
    'def'=> 'required',
    'title'=> 'Cookie path is required field',
];

$aValidation['auto_ban_spammer'] = [
    'def'=> 'int',
    'min' => '0',
    'title'=> '"Auto Ban Spammers" must be greater than or equal to 0',
];

$aValidation['auto_clear_cache'] = [
    'def'=> 'int',
    'min' => '0',
    'title'=> '"Auto clear system cache" must be greater than or equal to 0',
];

$aValidation['force_https_secure_pages'] = [
    'def'=> 'required',
    'requirements' => [
        'callback' => 'validation_check_valid_ssl'
    ],
    'title'=> 'Your server does not support https',
];

if (!function_exists('validation_check_valid_ssl')) {
    function validation_check_valid_ssl($value)
    {
        if ($value == false) {
            return true;
        }
        $sUrl = Phpfox::getLib('url')->makeUrl('');
        $sUrl = str_replace('http://', 'https://', $sUrl);

        $ch = curl_init($sUrl);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_code == 200) ? true : false;
    }
}
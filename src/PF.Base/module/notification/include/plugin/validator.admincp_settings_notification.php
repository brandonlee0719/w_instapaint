<?php
$aValidation['notify_ajax_refresh'] = [
    'def' => 'int:required',
    'min' => '1',
    'title' => _p('"Site Wide Notification AJAX Refresh" must be greater than 0'),
];
$aValidation['total_notification_title_length'] = [
    'def' => 'int:required',
    'min' => '11',
    'title' => _p('"Notification Title Length" must be greater than 10'),
];

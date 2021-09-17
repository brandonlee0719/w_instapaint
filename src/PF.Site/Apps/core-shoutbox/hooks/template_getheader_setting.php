<?php

$sShoutboxPath = (Phpfox::getVersion() == '4.5.0') ? 'phpFox_Shoutbox' : 'core-shoutbox';
$aJsVars['shoutbox_polling'] = Phpfox::getLib('url')->makeUrl('shoutbox.polling');
$aJsVars['shoutbox_user_profile_link'] = Phpfox_Url::instance()->makeUrl(Phpfox::getUserBy('user_name'));
$aJsVars['shoutbox_user_full_name'] = Phpfox::getUserBy('full_name');
$iSleepingTime = setting('shoutbox_polling_max_request_time');

if ($iSleepingTime > 15 || $iSleepingTime < 2) {
    $iSleepingTime = 5;
}
$aJsVars['shoutbox_sleeping_time'] = $iSleepingTime * 1000;
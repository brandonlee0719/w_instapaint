<?php
$sData .= '<script>var pf_im_site_title = "'. Phpfox::getParam('core.site_title') . ' - '. _p('messengers') .'"; var ban_filters = []; var ban_users = [];';
// bring ban filters word to fe
$aFilters = Phpfox::getService('ban')->getFilters('word');

if (is_array($aFilters)) {
    foreach ($aFilters as $aFilter) {
        $sData .= "ban_filters['$aFilter[find_value]'] = '" . html_entity_decode($aFilter['replacement']) . "';";
        if ($aFilter['return_user_group'] !== null) {
            $sData .= "ban_users.push('$aFilter[find_value]');";
        }
    }
}
$sData .= 'var global_update_time ="'. setting('core.global_update_time') .'";';

// generate token
if (!defined('PHPFOX_IM_TOKEN') || !PHPFOX_IM_TOKEN) {
    if (setting('pf_im_node_server_key')) {
        date_default_timezone_set("UTC");
        $imToken = md5(strtotime('today midnight') . setting('pf_im_node_server_key'));
    } else {
        $imToken = '';
    }
} else {
    $imToken = PHPFOX_IM_TOKEN;
    $sData .= 'var pf_im_using_host = true;';
}
$sData .= 'var pf_im_token ="'. $imToken .'";';
$sData .= 'var pf_im_node_server ="'. setting('pf_im_node_server') .'";';
// end generate token

// check blocked users
$aBlockedUsers = Phpfox::getService('user.block')->get(null, true);
if (!empty($aBlockedUsers)) {
    $sData .= 'var pf_im_blocked_users = [' . implode(',', $aBlockedUsers) . '];';
}

// get delete message time
if (setting('pf_time_to_delete_message')) {
    $sData .= 'var pf_time_to_delete_message = '. setting('pf_time_to_delete_message') * 86400000 .';';
}

// get custom sound
if (storage()->get('core-im/sound')) {
    $aCusSound = (array) storage()->get('core-im/sound')->value;
    if ($aCusSound['option'] === 'custom' && $aCusSound['custom_file']) {
        $sData .= 'var pf_im_custom_sound = "' . $aCusSound['custom_file'] . '";';
    }
}

// check module Attachment enable
if (Phpfox::isModule('attachment')) {
    $sData .= 'var pf_im_attachment_enable = true;';
    // get attachment file types
    $sData .= 'var pf_im_attachment_types = "'. implode(', ', \Phpfox::getService('attachment.type')->getTypes()) .'";';
}

// check App Twemoji
if (Phpfox::isApps('PHPfox_Twemoji_Awesome')) {
    $sData .= 'var pf_im_twemoji_enable = true;';
}

$sData .= '</script>';
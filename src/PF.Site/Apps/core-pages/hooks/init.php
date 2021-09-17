<?php
defined('PHPFOX') or exit('NO DICE!');
if (!defined('PHPFOX_PAGE_ITEM_TYPE_0')) {
    define('PHPFOX_PAGE_ITEM_TYPE_0', 'pages');
}

$iProfilePageId = Phpfox::getUserBy('profile_page_id');
$bIsLoginAsPage = $iProfilePageId > 0 && Phpfox::getLib('pages.facade')->getPageItemType($iProfilePageId) == 'pages';

if ($bIsLoginAsPage) {
    $aCurrentUser = Phpfox::getUserBy();
    $aPrevUser = Phpfox::getService('pages')->getLastLogin();
    $aCurrentUser['user_group_id'] = $aPrevUser['user_group_id'];
    Phpfox::getService('user.auth')->setUser($aCurrentUser);
}

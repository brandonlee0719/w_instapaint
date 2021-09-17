<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox_Request::instance()->get('module') == 'groups') {
    $aPage = Phpfox::getService('groups')->getPage($aFeed['parent_user_id']);
    if (isset($aPage['page_id']) && Phpfox::getService('groups')->isAdmin($aPage)) {
        define('PHPFOX_FEED_CAN_DELETE', true);
    }
}
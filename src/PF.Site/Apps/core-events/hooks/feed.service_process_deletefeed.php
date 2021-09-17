<?php
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox_Request::instance()->get('module') == 'event') {
    $aEvent = Phpfox::getService('event')->getForEdit($aFeed['parent_user_id'], true);
    if (isset($aEvent['event_id']) && $aEvent['user_id'] == Phpfox::getUserId()) {
        define('PHPFOX_FEED_CAN_DELETE', true);
    }
}

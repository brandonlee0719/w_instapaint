<?php
defined('PHPFOX') or exit('NO DICE!');

if (count($aFriends) &&
    ((!PHPFOX_IS_AJAX && defined('PHPFOX_PAGES_EDIT_ID') && PHPFOX_PAGES_EDIT_ID > 0) ||
        (PHPFOX_IS_AJAX && Phpfox_Request::instance()->get('friend_item_id')) && $this->getParam('friend_module_id') == 'groups')
) {
    defined('PHPFOX_PAGES_EDIT_ID') or define('PHPFOX_PAGES_EDIT_ID',
        Phpfox_Request::instance()->get('friend_item_id'));
    $aInvites = Phpfox::getService('groups')->getCurrentInvites(PHPFOX_PAGES_EDIT_ID);
    list(, $aMembers) = Phpfox::getService('groups')->getMembers(PHPFOX_PAGES_EDIT_ID);

    foreach ($aFriends as $iKey => $aFriend) {
        if (is_array($aInvites) && isset($aInvites[$aFriend['user_id']])) {
            $aFriends[$iKey]['is_active'] = _p('invited');
            continue;
        }
        if (is_array($aMembers) && in_array($aFriend['user_id'], array_column($aMembers, 'user_id'))) {
            $aFriends[$iKey]['is_active'] = _p('joined');
        }
    }
}
<?php
defined('PHPFOX') or exit('NO DICE!');

class Feed_Component_Block_Edit_User_Status extends Phpfox_Component
{
    public function process()
    {
        $iFeedId = $this->request()->get('id');
        $aFeedCallback = [];
        if ($module = $this->request()->get('module')) {
            $aFeedCallback = [
                'module' => $module,
                'table_prefix' => $module . '_',
                'item_id' => $this->request()->get('item_id') ? $this->request()->get('item_id') : $this->request()->get('id')
            ];
        }

        $aFeed = Phpfox::getService('feed')->getUserStatusFeed($aFeedCallback, $iFeedId, false);
        if (!$aFeed) {
            return false;
        }

        if (!empty($aFeedCallback)) {
            $this->template()->assign('aFeedCallback', [
                'callback_item_id' => $aFeed['parent_user_id'],
                'module' => $module,
                'item_id' => $aFeedCallback['item_id']
            ]);
        }

        $aTagged = [];
        if (!empty($aFeed['friends_tagged'])) {
            foreach ($aFeed['friends_tagged'] as $aUser) {
                $aTagged[] = $aUser['user_id'];
            }
            $aFeed['tagged_friends'] = implode(',', $aTagged);
        }

        $bIsUserStatus = false;
        switch ($aFeed['type_id']) {
            case 'user_status':
                if (!((Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_edit_other_user_status'))) {
                    return false;
                }
                $bIsUserStatus = true;
                break;
            case 'feed_comment':
                if ($aFeed['user_id'] != Phpfox::getUserId()) {
                    return false;
                }
                break;
            case 'pages_comment':
            case 'groups_comment':
            case 'event_comment':
                break;
            default:
                return false;
        }

        $bLoadCheckIn = false;
        // temporary hide check-in when edit feed
//        if (($aFeed['type_id'] == 'user_status') && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE'))) && !defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_IS_EVENT_VIEW') && Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') ) {
//            $bLoadCheckIn = true;
//        }
        $bLoadTagFriends = false;
        if (Phpfox::getParam('feed.enable_tag_friends') && $this->getParam('allowTagFriends', true)) {
            $bLoadTagFriends = true;
        }

        $this->template()->assign([
            'iFeedId' => $iFeedId,
            'bLoadCheckIn' => $bLoadCheckIn,
            'bLoadTagFriends' => $bLoadTagFriends,
            'aForms' => $aFeed,
            'bIsUserStatus' => $bIsUserStatus
        ]);

        return null;
    }
}

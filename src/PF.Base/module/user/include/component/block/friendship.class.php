<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Friendship
 */
class  User_Component_Block_Friendship extends Phpfox_Component
{
    public function process()
    {
        $viewer_id = Phpfox::getUserId();
        $user_id = $this->getParam('friend_user_id');
        // default value
        $is_friend = false;
        $bShowExtra = false;
        $iMutualCount = 0;
        $aMutualFriends = [];

        if (!$viewer_id) {
            return false;
        }

        if ($viewer_id == $user_id) {
            return false;
        }

        if (Phpfox::isModule('friend')) {
            $is_friend = Phpfox::getService('friend')->isFriend($viewer_id, $user_id);
            if (!$is_friend) {
                $is_friend = (Phpfox::getService('friend.request')->isRequested($viewer_id, $user_id) ? 2 : false);
            }

            if ($bShowExtra = $this->getParam('extra_info', false)) {
                list($iMutualCount, $aMutualFriends) = Phpfox::getService('friend')->getMutualFriends($user_id, 1);
            }
        }

        $iMutualRemain = $iMutualCount - count($aMutualFriends);

        $this->template()->assign([
            'user_id' => $user_id,
            'is_friend' => $is_friend,
            'type' => $this->getParam('type', 'string'),
            'show_extra' => $bShowExtra,
            'no_button' => $this->getParam('no_button', false),
            'no_mutual_list' => $this->getParam('mutual_list', false),
            'mutual_count' => $iMutualCount,
            'mutual_list' => $aMutualFriends,
            'mutual_remain' => $iMutualRemain,
            'is_module_friend' => Phpfox::isModule('friend'),
            'requested' => Phpfox::isUser() ? Phpfox::getService('friend.request')->isRequested($user_id,
                Phpfox::getUserId()) : false
        ]);

        return null;
    }
}

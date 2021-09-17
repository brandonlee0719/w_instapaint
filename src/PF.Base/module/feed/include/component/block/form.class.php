<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Feed_Component_Block_Form
 */
class Feed_Component_Block_Form extends Phpfox_Component {

	public function process() {
		$bLoadCheckIn = false;
		if (!defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_IS_EVENT_VIEW') && Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') )
		{
			$bLoadCheckIn = true;
		}

		$bLoadTagFriends = false;
		if (Phpfox::getParam('feed.enable_tag_friends') && $this->getParam('allowTagFriends', true))
        {
            $bLoadTagFriends = true;
        }
		$aUser = $this->getParam('aUser');
        $mOnOtherUserProfile = (defined('PHPFOX_IS_USER_PROFILE') && !empty($aUser) && $aUser['user_id'] != Phpfox::getUserId()) ? $aUser['user_id'] : false;
        $iUserProfileId = (defined('PHPFOX_IS_USER_PROFILE') && !empty($aUser) && $aUser['user_id']) ? $aUser['user_id'] : 0;

		$this->template()->assign([
			'aFeedStatusLinks' => Phpfox::getService('feed')->getShareLinks(),
			'bLoadCheckIn' => $bLoadCheckIn,
            'bLoadTagFriends' => $bLoadTagFriends,
            'mOnOtherUserProfile' => $mOnOtherUserProfile,
            'iUserProfileId' => $iUserProfileId
		]);
	}
}
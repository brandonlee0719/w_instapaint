<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Ajax_Ajax
 */
class Profile_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function logo()
	{
		$this->setTitle(_p('cover_photo'));
		$aParams = array(
			'page_id' => $this->get('page_id'),
			'groups_id' => $this->get('groups_id')
		);
		
		Phpfox::getBlock('profile.cover', $aParams);
	}

	public function updateProfilePhoto()
    {
        Phpfox::getBlock('user.profile-photo');
    }
}

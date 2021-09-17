<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Admincp_DenyUser
 */
class User_Component_Block_Admincp_DenyUser extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iUser = $this->request()->get('iUser');
		$aUser = Phpfox::getService('user')->get($iUser, true);
		$aUser['link'] = $this->url()->makeUrl($aUser['user_name']);
		
		$this->template()->assign(array(
				'aUser' => $aUser,
				'iUserIdDelete' => $aUser['user_id']
			)
		);

		return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_filter_clean')) ? eval($sPlugin) : false);
	}
}

<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Component_Block_Form
 */
class Ban_Component_Block_Form extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		/* This is a good way of leaving out the admins and staff, we could remove the Banned user groups
		 * but that would require more processing */
		$aUserGroups = Phpfox::getService('user.group')->get('user_group_id != 1 AND user_group_id != 4');

		$this->template()->assign(array(
				'aUserGroups' => $aUserGroups,
				'bShow' => $this->getParam('bShow', false) ? true : false,
				'bHideAffected' => $this->getParam('bHideAffected', false) ? true : false
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('blog.component_block_new_clean')) ? eval($sPlugin) : false);
	}
}

<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Images
 */
class User_Component_Block_Images extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$aUserImages = Phpfox::getService('user')->getUserImages();
		
		if (!is_array($aUserImages) || (is_array($aUserImages) && !count($aUserImages)))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'aUserImages' => $aUserImages
			)
		);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_images_clean')) ? eval($sPlugin) : false);
	}
}

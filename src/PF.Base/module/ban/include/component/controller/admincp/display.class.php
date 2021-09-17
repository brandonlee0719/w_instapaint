<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Component_Controller_Admincp_Display
 */
class Ban_Component_Controller_Admincp_Display extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        $this->setParam('aBanFilter', [
                'title' => _p('display_names'),
                'type'  => 'display_name',
                'url'   => 'admincp.ban.display',
                'form'  => _p('display_name')
            ]);
        
        return Phpfox_Module::instance()->setController('ban.admincp.default');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ban.component_controller_admincp_display_clean')) ? eval($sPlugin) : false);
	}
}

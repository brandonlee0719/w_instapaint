<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Component_Controller_Admincp_Ip
 */
class Ban_Component_Controller_Admincp_Ip extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->setParam('aBanFilter', array(
				'title' => _p('ip_addresses'),
				'type' => 'ip',
				'url' => 'admincp.ban.ip',
				'form' => _p('ip_address')
			)
		);

        $this->setParam('aValidation',[
            'find_value'=> [
                'def'   => 'ip:required',
                'title' => 'Invalid IP Address',
            ]
        ]);
		
		return Phpfox_Module::instance()->setController('ban.admincp.default');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ban.component_controller_admincp_ip_clean')) ? eval($sPlugin) : false);
	}
}

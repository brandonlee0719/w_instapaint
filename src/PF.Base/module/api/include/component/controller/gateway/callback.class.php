<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api_Component_Controller_Gateway_Callback
 */
class Api_Component_Controller_Gateway_Callback extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        try{
            if (!($oGateway = Phpfox::getService('api.gateway')->callback($this->request()->get('req4'))))
            {

            }
        }catch(Exception $ex){
            if(defined('PHPFOX_DEBUG') && PHPFOX_DEBUG){
                var_dump($ex);
            }
        }
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('api.component_controller_gateway_callback_clean')) ? eval($sPlugin) : false);
	}
}

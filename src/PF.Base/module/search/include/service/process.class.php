<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Search_Service_Process
 */
class Search_Service_Process extends Phpfox_Service 
{
    /**
     * @param $sMethod
     * @param $aArguments
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('search.service_process__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

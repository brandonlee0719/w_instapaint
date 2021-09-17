<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 * @version 		$Id: callback.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Core_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct() {}
    
    /**
     * @return array
     */
    public function getBlocksIndexMember()
    {
        return [
            'table' => 'user_dashboard',
            'field' => 'user_id'
        ];
    }
    
    /**
     * @return array
     */
    public function hideBlockNew()
    {
        return [
            'table' => 'user_dashboard'
        ];
    }
    
    /**
     * @return array
     */
    public function getBlockDetailsNew()
	{
        return [
            'title' => _p('what_s_new')
        ];
    }
    
    /**
     * @param string $sProduct
     * @param string $sModule
     * @param bool   $bCore
     *
     * @return bool
     */
    public function exportModule($sProduct, $sModule, $bCore)
	{
		$iCnt = 0;
		(Phpfox::getService('admincp.menu')->export($sProduct, $sModule) ? $iCnt++ : null);
		(Phpfox::getService('admincp.setting')->exportGroup($sProduct, $sModule) ? $iCnt++ : null);
		(Phpfox::getService('admincp.setting')->export($sProduct, $sModule, $bCore) ? $iCnt++ : null);
		(Phpfox::getService('admincp.module.block')->export($sProduct, $sModule) ? $iCnt++ : null);
		(Phpfox::getService('admincp.plugin')->exportHooks($sProduct, $sModule) ? $iCnt++ : null);
		(Phpfox::getService('admincp.plugin')->export($sProduct, $sModule) ? $iCnt++ : null);
		(Phpfox::getService('admincp.component')->export($sProduct, $sModule) ? $iCnt++ : null);
		(Phpfox::getService('admincp.cron')->export($sProduct, $sModule) ? $iCnt++ : null);
		(Phpfox::getService('core.stat')->export($sProduct, $sModule) ? $iCnt++ : null);
		
		return ($iCnt ? true : false);
	}
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('core.service_callback__call'))
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
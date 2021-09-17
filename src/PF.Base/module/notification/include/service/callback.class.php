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
 * @version 		$Id: service.class.php 67 2009-01-20 11:32:45Z Raymond_Benc $
 */
class Notification_Service_Callback extends Phpfox_Service 
{
	public function getBlockDetailsFeed()
	{
		return array(
			'title' => _p('notifications')
		);
	}
	
	public function onDeleteUser($iUser)
	{
		$this->database()->delete(Phpfox::getT('notification'), 'user_id = ' . (int) $iUser . ' OR owner_user_id = ' . (int) $iUser . '');
	}
	
	public function getGlobalNotifications()
	{
		$iTotal = Phpfox::getService('notification')->getUnseenTotal();
		if ($iTotal > 0)
		{				
			Phpfox_Ajax::instance()->call('$(\'span#js_total_new_notifications\').html(\'' . (int) $iTotal . '\').css({display: \'block\'}).show();');
		}
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('notification.service_callback__call'))
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
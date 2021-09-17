<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Service
 * @version 		$Id: callback.class.php 5016 2012-11-12 15:18:29Z Miguel_Espinoza $
 */
class Like_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		
	}
	
	/**
	 * Action to take when user cancelled their account. Remove likes ("Joins" to pages too)
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{		
		// get all the items from this user		
		$aLikes = $this->database()->select('like_id, type_id, item_id')
			->from(Phpfox::getT('like'))
			->where('user_id = ' . $iUser)
			->execute('getSlaveRows');
				
		foreach ($aLikes as $aLike)
		{
			$sModule = $aLike['type_id'];
			$sExtra = '';
			if (strpos($sModule, '_') !== false)
			{
				$aParams = explode('_', $sModule);
				$sModule = $aParams[0];
				$sExtra = ucwords($aParams[1]);
			}
			if (Phpfox::hasCallback($sModule, 'deleteLike' . $sExtra))
			{
				Phpfox::callback($sModule.'.deleteLike' . $sExtra, $aLike['item_id']);
			}
		}
		
		$this->database()->delete(Phpfox::getT('like'), 'user_id = ' . $iUser);
		$this->database()->delete(Phpfox::getT('like_cache'), 'user_id = ' . $iUser);
		
		return true;
	}
	
	public function getNotificationSettings()
	{
		return array('like.new_like' => array(
				'phrase' => _p('notification_for_likes'),
				'default' => 1
			)
		);
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
		if ($sPlugin = Phpfox_Plugin::get('like.service_callback__call'))
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
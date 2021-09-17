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
 * @package  		Module_Friend
 * @version 		$Id: process.class.php 5605 2013-04-02 09:32:58Z Miguel_Espinoza $
 */
class Friend_Service_List_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('friend_list');
	}
	
	/**
	 * Adds a new folder to the friends folder list
	 *
	 * @param String $sName name of the new folder
	 * @return bool value for success
	 */
	public function add($sName)
	{		
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		return $this->database()->insert($this->_sTable, array(
			'user_id' => Phpfox::getUserId(),
			'name' => Phpfox::getLib('parse.input')->clean($sName, 255),
			'time_stamp' => PHPFOX_TIME			
		));		
	}
	
	public function update($iListId, $sName)
	{
		$oFriendList = Phpfox::getService('friend.list');
		
		if (Phpfox::getLib('parse.format')->isEmpty($sName))
		{
			return Phpfox_Error::set(_p('provide_a_name_for_your_list_dot'));
		}
		
		if ($oFriendList->isFolder($sName, $iListId))
		{
			return Phpfox_Error::set(_p('you_already_have_a_list_with_the_same_name_dot'));
		}
		
		$this->database()->update($this->_sTable, array(
				'name' => Phpfox::getLib('parse.input')->clean($sName, 255),
			), 'list_id = ' . (int) $iListId . ' AND user_id = ' . Phpfox::getUserId()
		);		
		
		(($sPlugin = Phpfox_Plugin::get('friend.service_list_process_update')) ? eval($sPlugin) : false);
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		
		return true;
	}	
	
	public function delete($iId)
	{
		$aList = $this->database()->select('*')
			->from(Phpfox::getT('friend_list'))
			->where('list_id = ' . (int) $iId)
			->execute('getSlaveRow');
		
		if ($aList['user_id'] != Phpfox::getUserId())
		{
			return false;
		}
		
		$aRows = $this->database()->select('f.friend_id')
			->from(Phpfox::getT('friend'), 'f')
			->where('f.list_id = ' . (int) $iId . ' AND f.user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRows');
			
		foreach ($aRows as $aRow)
		{
			$this->database()->update(Phpfox::getT('friend'), array(
				'list_id' => 0
			), 'friend_id = ' . $aRow['friend_id']);
		}
		
		$this->database()->delete($this->_sTable, 'list_id = ' . (int) $iId);
		
		(($sPlugin = Phpfox_Plugin::get('friend.service_list_process_delete')) ? eval($sPlugin) : false);
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		
		return true;
	}

	public function move($mFolder, $aIds)
	{		
		// how many friends are in destination list:
		$oService = Phpfox::getService('friend.list');

		foreach ($aIds as $iId)
		{			
			// move friend
			$this->database()->update(Phpfox::getT('friend'), array(
				'list_id' => (int) $mFolder
			), 'friend_id = ' . (int) $iId . ' AND user_id = ' . Phpfox::getUserId());			
		}
		
		(($sPlugin = Phpfox_Plugin::get('friend.service_list_process_move')) ? eval($sPlugin) : false);
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		return true;
	}
	
	public function addFriendsTolist($iListId, $aFriends)
	{
		if (!Phpfox::getService('friend.list')->getList($iListId, Phpfox::getUserId())) return false;
		if (!is_array($aFriends))
		{
			$aFriends = array($aFriends);
		}
		
		foreach ((array) $aFriends as $iFriendId)
		{
			$this->database()->delete(Phpfox::getT('friend_list_data'), 'list_id = ' . (int) $iListId . ' AND friend_user_id = ' . (int) $iFriendId);
			$this->database()->insert(Phpfox::getT('friend_list_data'), array(
					'list_id' => (int) $iListId,
					'friend_user_id' => (int) $iFriendId
				)
			);
		}
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		return true;
	}
	
	public function removeFriendsFromlist($iListId, $iFriendId)
	{
		$aList = $this->database()->select('*')
			->from(Phpfox::getT('friend_list'))
			->where('list_id = ' . (int) $iListId)
			->execute('getSlaveRow');
			
		if (!isset($aList['list_id']))
		{
			return Phpfox_Error::set(_p('cannot_find_the_list_you_are_trying_to_manage_dot'));
		}
		
		if ($aList['user_id'] != Phpfox::getUserId())
		{
			return Phpfox_Error::set(_p('you_cannot_delete_this_list_dot'));
		}
			
		$this->database()->delete(Phpfox::getT('friend_list_data'), 'list_id = ' . (int) $iListId . ' AND friend_user_id = ' . (int) $iFriendId);
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		return true;
	}
	
	public function addListToProfile($iListId)
	{
		$aList = $this->database()->select('*')
			->from(Phpfox::getT('friend_list'))
			->where('list_id = ' . (int) $iListId)
			->execute('getSlaveRow');
			
		if (!isset($aList['list_id']))
		{
			return Phpfox_Error::set(_p('cannot_find_the_list_you_are_trying_to_manage_dot'));
		}
		
		if ($aList['user_id'] != Phpfox::getUserId())
		{
			return Phpfox_Error::set(_p('you_cannot_add_this_list_to_your_profile_dot'));
		}		
		
		$this->database()->update(Phpfox::getT('friend_list'), array('is_profile' => '1'), 'list_id = ' . (int) $iListId);
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		return true;
	}
	
	public function removeListFromProfile($iListId)
	{
		$aList = $this->database()->select('*')
			->from(Phpfox::getT('friend_list'))
			->where('list_id = ' . (int) $iListId)
			->execute('getSlaveRow');
			
		if (!isset($aList['list_id']))
		{
			return Phpfox_Error::set(_p('cannot_find_the_list_you_are_trying_to_manage_dot'));
		}
		
		if ($aList['user_id'] != Phpfox::getUserId())
		{
			return Phpfox_Error::set(_p('you_cannot_remove_this_list_from_your_profile_dot'));
		}		
		
		$this->database()->update(Phpfox::getT('friend_list'), array('is_profile' => '0'), 'list_id = ' . (int) $iListId);
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		return true;		
	}
	
	public function updateListOrder($iListId, $aFriends)
	{
		$aList = $this->database()->select('*')
			->from(Phpfox::getT('friend_list'))
			->where('list_id = ' . (int) $iListId)
			->execute('getSlaveRow');
			
		if (!isset($aList['list_id']))
		{
			return Phpfox_Error::set(_p('cannot_find_the_list_you_are_trying_to_manage_dot'));
		}
		
		if ($aList['user_id'] != Phpfox::getUserId())
		{
			return Phpfox_Error::set(_p('you_cannot_manage_this_list_dot'));
		}
		
		if (!is_array($aFriends))
		{
			$aFriends = array($aFriends);
		}
		
		$aNewFriends = array_reverse($aFriends);

		$iCnt = 0;
		foreach ($aNewFriends as $iFriendId)
		{
			$iCnt++;
			
			$this->database()->update(Phpfox::getT('friend_list_data'), array('ordering' => (int) $iCnt), 'list_id = ' . (int) $iListId . ' AND friend_user_id = ' . (int) $iFriendId);
		}
		if (Phpfox::getParam('friend.cache_friend_list'))
		{
			$sCacheId = $this->cache()->set(array('friend_list', Phpfox::getUserId()));
			$this->cache()->remove($sCacheId);
		}
		return true;
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
		if ($sPlugin = Phpfox_Plugin::get('friend.service_list_process__call'))
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
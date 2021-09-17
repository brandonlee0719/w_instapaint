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
 * @version 		$Id: callback.class.php 7274 2014-04-21 13:25:12Z Fern $
 */
class Friend_Service_Callback extends Phpfox_Service 
{
    /**
     * @param      $iItemId
     * @param bool $bDoNotSendEmail
     * @deprecated
     * @return bool
     */
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		// No count to update here. Table friend do not use total_like
		return true;
	}

    /**
     * @param      $iItemId
     * @param bool $bDoNotSendEmail
     * @deprecated
     * @return bool
     */
	public function deleteLike($iItemId, $bDoNotSendEmail = false)
	{
		// No count to update here. Table friend do not use total_like
		return true;
	}
	
	public function getActivityFeed($aFeed, $aCallBack = null, $bIsChildItem = false)
	{
		$aCore = Phpfox_Request::instance()->get('core');
		
		$bForceUser = false;
		if (defined('PHPFOX_CURRENT_USER_PROFILE') || isset($aCore['profile_user_id']))
		{
			$aUser = (array) (isset($aCore['profile_user_id']) ? Phpfox::getService('user')->get($aCore['profile_user_id']) : Phpfox::getService('user')->getUserObject(PHPFOX_CURRENT_USER_PROFILE));
			if (isset($aUser['user_id']))
			{
				if ($aUser['user_id'] == $aFeed['item_id'])
				{
					$aFeed['item_id'] = $aFeed['user_id'];
					$bForceUser = true;				
				}
			}
		}		
		
		if(isset($aUser['user_id']) && $aFeed['parent_user_id'] == $aUser['user_id'])
		{
			$iDestinationUserId = $aFeed['user_id'];
			$bForceUser = true;
		}
		else
		{
			$iDestinationUserId = $aFeed['parent_user_id'];
		}

		$aRow = $this->database()->select('u.*, uf.city_location, uf.country_child_id, uf.total_friend')
			->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
			->where('u.user_id = ' . (int) $iDestinationUserId)
			->execute('getSlaveRow');

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aRow['user_id']))
        {
            return false;
        }
		
		$iTotalLikes = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('like'))
			->where('item_id = ' . $aFeed['item_id'] . " AND type_id = 'friend'")
			->execute('getSlaveField');
		$iIsLiked = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('like'))
			->where('item_id = ' . $aFeed['item_id'] . " AND type_id = 'friend'" . ' AND user_id = ' . Phpfox::getUserId())
			->execute('getSlaveField');
		
		if (!isset($aRow['user_id']))
		{
			return false;
		}
		
		$aParams = array(
			'user' => $aRow,
			'suffix' => '_120_square',
			'max_width' => '120',
			'max_height' => '120'
		);
		
		$sImage = Phpfox::getLib('image.helper')->display($aParams);
        if ($aRow['gender'])
        {
            $aRow['gender_name'] = Phpfox::getService('user')->gender($aRow['gender']);
        }
        $sContent = Phpfox_Template::instance()->assign(['aUserFriendFeed' => $aRow])->getTemplate('friend.block.feed', true);
		
		$aReturn = [
			'feed_title' => $aRow['full_name'],
			'feed_title_sub' => $aRow['user_name'],
			'feed_info' => _p('is_now_friends_with_user', [
                'user_link'  => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
                'full_name'  => $aRow['full_name'],
                'user_name'  => $aRow['user_name'],
            ]),
			'feed_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/friend_added.png', 'return_url' => true)),
			'feed_total_like' => $iTotalLikes,
			'feed_is_liked' => ((int)$iIsLiked > 0 ? true : false),
			'time_stamp' => $aFeed['time_stamp'],			
			'enable_like' => false,		
			'feed_image' => $sImage,
            'feed_custom_html' => $sContent
        ];
		
		if ($bForceUser)
		{
			$aReturn['force_user'] = $aUser;
			$aReturn['gender'] = $aUser['gender'];
		}
        if ($bIsChildItem){
            $aReturn = array_merge($aReturn, $aFeed);
        }
		(($sPlugin = Phpfox_Plugin::get('friend.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false); 
		return $aReturn;
	}

	public function getNewsFeed($aRow, $iUserid = null)
	{
		if ($sPlugin = Phpfox_Plugin::get('friend.service_callback_getnewsfeed_start')){eval($sPlugin);}
		static $aCache = array();

		if ($iUserid === null && isset($aCache[$aRow['viewer_user_id']][$aRow['owner_user_id']]))
		{
			return false;
		}

		$oUrl = Phpfox_Url::instance();

		$sOwnerImage = '';
		$sViewerImage = '';

		if ($iUserid === null)
		{
			if ($aRow['viewer_user_id'] == Phpfox::getUserId())
			{
				$aRow['text'] = _p('viewer_image_you_and_owner_image_a_href_user_link_full_name_a_are_now_friends', array(
						'viewer_image' => $sViewerImage,
						'owner_image' => $sOwnerImage,
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
						'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name'])
					)
				);
			}
			elseif ($aRow['owner_user_id'] == Phpfox::getUserId())
			{
				$aRow['text'] = _p('owner_image_you_and_viewer_image_a_href_friend_link_friend_a_are_now_friends', array(
						'viewer_image' => $sViewerImage,
						'owner_image' => $sOwnerImage,		
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
						'friend_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id'])),
						'friend' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name'])
					)
				);
			}
			else
			{
				$aRow['text'] = _p('owner_image_a_href_user_link_full_name_a_and_viewer_image', array(
						'viewer_image' => $sViewerImage,
						'owner_image' => $sOwnerImage,
						'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),					
						'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
						'friend_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id'])),
						'friend' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name'])
					)
				);
			}
		}
		else
		{
			$aRow['text'] = _p('owner_image_a_href_user_link_full_name_a_and_viewer_image_friends', array(
					'viewer_image' => $sViewerImage,
					'owner_image' => $sOwnerImage,				
					'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
					'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
					'friend_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id'])),				
					'friend' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name'])					
				)
			);
		}

		$aCache[$aRow['owner_user_id']][$aRow['viewer_user_id']] = true;
		
		$aRow['icon'] = 'misc/friend_added.png';

		return $aRow;
	}

	/**
	* This is the callback to display when a friend request has been accepted
	* @param array $aNotification
			owner_user_id = user who sent the friend request
			user_id = user who accepted the friend request
     *
     * @return array
	*/
	public function getNotificationAccepted($aNotification)
	{
		return array(
			'link' => Phpfox_Url::instance()->makeUrl($aNotification['user_name']),
			'message' => _p('full_name_added_you_as_a_friend', array('full_name' => Phpfox::getLib('parse.output')->shorten($aNotification['full_name'], 0))),
			'icon' => Phpfox_Template::instance()->getStyle('image', 'misc/user.png')
		);							
	}
	
	public function getProfileLink()
	{
		return 'profile.friend';
	}

	public function getNotificationSettings()
	{
		return array('friend.new_friend_accepted' => array(
				'phrase' => _p('new_friend'),
				'default' => 1
			),
			'friend.new_friend_request' => array(
				'phrase' => _p('friend_request'),
				'default' => 1
			)
		);
	}

	public function getNotificationFeedBirthday($aRow)
	{
		return array(
			'message' => _p('user_link_wished_you_a_happy_birthday', array('user' => $aRow)),
			'link' => Phpfox_Url::instance()->makeUrl('friend.mybirthday', array('id' => $aRow['item_id']))
		);
	}

	public function getNotificationBirthday($aRow)
	{
		return array(
			'message' => _p('user_link_wished_you_a_happy_birthday', array('user' => $aRow)),
			'link' => Phpfox_Url::instance()->makeUrl('friend.mybirthday', array('id' => $aRow['item_id']))
		);
	}
	
	public function getProfileSettings()
	{
		return array(
			'friend.view_friend' => array(
				'phrase' => _p('view_your_friends')
			)
		);
	}

	public function getUserCountFieldRequest()
	{
		return 'friend_request';
	}

	public function getNotificationFeedRequest($aRow)
	{
		return array(
			'message' => _p('user_link_asked_to_be_your_friend', array('user' => $aRow)),
			'link' => Phpfox_Url::instance()->makeUrl('friend.accept', array('id' => $aRow['item_id']))
		);
	}

	/**
	 * Action to take when user cancelled their account
	 *	Deletes: friends, friends lists and friends requests
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		$aFriends = $this->database()
			->select('friend_id')
			->from(Phpfox::getT('friend'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		foreach ($aFriends as $aFriend)
		{
            Phpfox::getService('friend.process')->delete($aFriend['friend_id']);
		}
		$aFriendLists = $this->database()
			->select('list_id')
			->where('user_id = ' . (int)$iUser)
			->from(Phpfox::getT('friend_list'))
			->execute('getSlaveRows');
		foreach ($aFriendLists as $aList)
		{
            Phpfox::getService('friend.list.process')->delete($aList['list_id']);
		}
		$this->database()->delete(Phpfox::getT('friend_request'), 'user_id = ' . (int)$iUser . ' OR friend_user_id = ' . (int)$iUser);
	}
	
	public function updateCounterList()
	{
		$aList = array();		
		
		$aList[] =	array(
			'name' => (Phpfox::isModule('photo') ? _p('update_friend_count') : 'Update friend count'),
			'id' => 'video-friend-count'
		);	

		return $aList;
	}		
	
	public function updateCounter($iId, $iPage, $iPageLimit)
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('user'))
			->execute('getSlaveField');		
			
		if (($sPlugin = Phpfox_Plugin::get('friend.service_callback__updatecounter')))
		{
			eval($sPlugin);
		}				
			
		$aRows = $this->database()->select('u.user_id')
			->from(Phpfox::getT('user'), 'u')
			->limit($iPage, $iPageLimit, $iCnt)
			->group('u.user_id')
			->execute('getSlaveRows');
					
		foreach ($aRows as $aRow)
		{
			$iTotalFriends = $this->database()->select('COUNT(f.user_id)')
				->from(Phpfox::getT('friend'), 'f')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id AND u.status_id = 0 AND u.view_id = 0')
				->where('f.user_id = ' . $aRow['user_id'] . ' AND f.is_page = 0') 
				->execute('getSlaveField');		

			$this->database()->update(Phpfox::getT('user_field'), array('total_friend' => $iTotalFriends), 'user_id = ' . $aRow['user_id']);			
		}							
			
		return $iCnt;
	}
	
	public function getAjaxProfileController()
	{
		return 'friend.profile';
	}
	
	public function getProfileMenu($aUser)
	{
		if (!Phpfox::getParam('profile.show_empty_tabs'))
		{		
			if (!isset($aUser['total_friend']))
			{
				return false;
			}

			if (isset($aUser['total_friend']) && (int) $aUser['total_friend'] === 0)
			{
				return false;
			}
		}
		
		if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'friend.view_friend'))
		{
			return false;	
		}		
		
		$aMenus[] = array(
			'phrase' => _p('friends'),
			'url' => 'profile.friend',
			'total' => (int) (isset($aUser['total_friend']) ? $aUser['total_friend'] : 0),
			'icon' => 'misc/group.png'
		);		
		
		return $aMenus;
	}	

	public function getGlobalNotifications()
	{
		$iTotal = Phpfox::getService('friend.request')->getUnseenTotal();
		if ($iTotal > 0)
		{
			Phpfox_Ajax::instance()->call('$(\'span#js_total_new_friend_requests\').html(\'' . (int) $iTotal . '\').css({display: \'block\'}).show();');
		}
	}

    /**
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':friend')
            ->where('user_id ='.(int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('friends'),
            'total_value' => $iTotal,
            'type' => 'user'
        ];
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
		if ($sPlugin = Phpfox_Plugin::get('friend.service_callback___call'))
		{
			eval($sPlugin);
            return null;
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
    public function canShareItemOnFeed(){
        return true;
    }
}
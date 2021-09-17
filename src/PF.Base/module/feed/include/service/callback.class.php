<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 */
class Feed_Service_Callback extends Phpfox_Service
{
    /**
     * Feed_Service_Callback constructor.
     */
	public function  __construct()
	{
		$this->_sTable = Phpfox::getT('feed');
	}
    
    /**
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
	public function getSiteStatsForAdmin($iStartTime, $iEndTime)
	{
		$aCond = array();
		if ($iStartTime > 0)
		{
			$aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
		}	
		if ($iEndTime > 0)
		{
			$aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
		}			
		
		$iCnt = (int) $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('feed_comment'))
			->where($aCond)
			->execute('getSlaveField');
		
		return array(
			'phrase' => 'feed.comments_on_profiles',
			'total' => $iCnt
		);
	}
    
    /**
     * @param int $iModule
     *
     * @return void
     */
	public function massAdmincpModuleDelete($iModule)
	{
		$this->database()->delete($this->_sTable, "type_id = '" . $this->database()->escape($iModule) . "'");
	}
    
    /**
     * @param array $aRow
     *
     * @return bool
     */
	public function getCommentNewsFeed($aRow)
	{
		return false;
	}
    
    /**
     * @param int $iId
     * @param string $sName
     *
     * @return string
     */
	public function getItemName($iId, $sName)
	{
		return '<a href="' . Phpfox_Url::instance()->makeUrl('comment.view', array('id' => $iId)) . '">' . _p('on_name_s_feed', array('name' => $sName)) . '</a>';
	}
    
    /**
     * @param int $iId
     *
     * @return void
     */
	public function deleteComment($iId) { }
    
    /**
     * @param int $iUser
     *
     * @return void
     */
	public function onDeleteUser($iUser)
	{
	    $this->database()->delete($this->_sTable, 'user_id = ' . (int)$iUser);
	    $this->database()->delete($this->_sTable, 'parent_user_id = ' . (int)$iUser);
	    $this->database()->delete(Phpfox::getT('feed_comment'), 'parent_user_id = ' . (int)$iUser); 
	}
    
    /**
     * @return array
     */
	public function getProfileSettings()
	{
        $aOut = [
            'feed.view_wall' => [
                'phrase'  => _p('view_your_wall'),
                'default' => '0'
            ]
        ];

        // Check if all user groups have "profile.can_post_comment_on_profile" disabled
		$aGroups = Phpfox::getService('user.group')->get();
		
		$bShowShareOnWall = false;
		$oUser = Phpfox::getService('user.group.setting');
        foreach ($aGroups as $aGroup) {
            if ($oUser->getGroupParam($aGroup['user_group_id'], 'profile.can_post_comment_on_profile')) {
                $bShowShareOnWall = true;
                break;
            }
        }
        
        if ($bShowShareOnWall) {
            $aOut['feed.share_on_wall'] = [
                'phrase'  => _p('share_on_your_wall'),
                'default' => '1',
                'anyone'  => false
            ];
        }
        
        return $aOut;
	}
    
    /**
     * @param int $iId
     *
     * @return bool|string
     */
	public function getReportRedirect($iId)
	{
		$aFeed = $this->database()->select('f.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('feed_comment'), 'f')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = f.parent_user_id')
			->where('f.feed_comment_id = ' . (int) $iId)
			->execute('getSlaveRow');
        
        if (!isset($aFeed['feed_comment_id'])) {
            return false;
        }
		
		return Phpfox_Url::instance()->makeUrl($aFeed['user_name'], array('comment-id' => $aFeed['feed_comment_id']));
	}
    
    /**
     * @param int $iId
     *
     * @return bool|string
     */
	public function getReportRedirectComment($iId)
	{
		$aFeed = $this->database()->select('f.feed_id, ' . Phpfox::getUserField())
			->from(Phpfox::getT('feed_comment'), 'c')
			->join(Phpfox::getT('feed'), 'f', 'type_id = \'feed_comment\' && f.item_id = c.feed_comment_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = f.user_id')
			->where('c.feed_comment_id = ' . (int) $iId)
			->execute('getSlaveRow');
        
        if (empty($aFeed)) {
            return false;
        }
		
		return Phpfox_Url::instance()->makeUrl($aFeed['user_name'], array('feed' => $aFeed['feed_id'], '#feed'));
	}
    
    /**
     * @param int $iId
     *
     * @return bool|string
     */
	public function getRedirectComment($iId)
	{		
		return $this->getReportRedirect($iId);	
	}
    
    /**
     * @return array
     */
	public function pendingApproval()
	{
        return [
            'phrase' => _p('profile_comments'),
            'value'  => 0,
            'link'   => Phpfox_Url::instance()->makeUrl('admincp.feed', ['view' => 'approval'])
        ];
    }

    
    /**
     * @param array $aItem
     *
     * @return array|bool
     */
    public function getActivityFeedEgift($aItem)
	{
        if (!Phpfox::isModule('egift')) {
            return false;
        }
        
		/* Check if this egift is free or paid */
		$this->database()->select('e.file_path, g.price, g.user_from, g.user_to, e.title, e.server_id, g.status, fc.content, fc.feed_comment_id, fc.total_comment, f.time_stamp, fc.total_like, ' . Phpfox::getUserField('u', 'parent_'))
				->from(Phpfox::getT('egift_invoice'), 'g')
				->join(Phpfox::getT('feed'), 'f', 'f.feed_id = g.feed_id')
				->join(Phpfox::getT('egift'), 'e', 'e.egift_id = g.egift_id')
				->leftJoin(Phpfox::getT('feed_comment'), 'fc', 'fc.feed_comment_id = ' . $aItem['item_id'])
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = fc.parent_user_id')
				->where('g.feed_id = ' . (int)$aItem['feed_id']);
        
        if (Phpfox::isModule('like')) {
            $this->database()
                ->select(', l.like_id as is_liked')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_egift\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId());
        }
		
		$aInvoice = $this->database()->execute('getSlaveRow');

        if (empty($aInvoice)) {
            return false;
        }
        
        if ($aInvoice['price'] > 0 && $aInvoice['status'] != 'completed') {
            return false;
        }
        $aInvoice['send_from'] = Phpfox::getService('user')->getUser($aInvoice['user_from']);
        $aInvoice['send_to'] = Phpfox::getService('user')->getUser($aInvoice['user_to']);

        $sContent = Phpfox_Template::instance()->assign(['aInvoice' => $aInvoice])->getTemplate('egift.block.feed-rows',
            true);
		$aReturn = array(
			'no_share' => true,
			'feed_status' => $aInvoice['content'],
			'feed_link' => '',
			'total_comment' => $aInvoice['total_comment'],
			'feed_total_like' => $aInvoice['total_like'],
			'feed_is_liked' => (isset($aInvoice['is_liked']) ? $aInvoice['is_liked'] : false),
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/comment.png', 'return_url' => true)),
			'time_stamp' => $aInvoice['time_stamp'],
			'enable_like' => true,
			'comment_type_id' => 'feed',
			'like_type_id' => 'feed_egift',
            'feed_custom_html' => $sContent
		);

        if (!empty($aInvoice['parent_user_name'])) {
		    $aParentUser = Phpfox::getService('user')->getUserFields(true, $aInvoice, 'parent_');
            if (!empty($aParentUser) && !empty($aParentUser['parent_user_name']) && !empty($aInvoice['feed_comment_id']))
            {
                $aReturn['feed_link'] = Phpfox_Url::instance()->makeUrl($aParentUser['parent_user_name'], array('comment-id' => $aInvoice['feed_comment_id']));
            }
        }
		
		if (!empty($aInvoice['parent_user_name']) && !defined('PHPFOX_IS_USER_PROFILE') && empty($_POST))
		{
			$aReturn['parent_user'] = $aParentUser;
		}

		if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aInvoice['parent_user_name']) && $aInvoice['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId())
		{
			if (empty($_POST))
			{
				$aReturn['parent_user'] = $aParentUser;
			}
		}
		return $aReturn;
	}
    
    /**
     * @param array $aItem
     *
     * @return array
     */
	public function getActivityFeedComment($aItem)
	{
		if(Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId());
		}
		
		$aRow = $this->database()->select('fc.*, ' . Phpfox::getUserField('u', 'parent_'))
			->from(Phpfox::getT('feed_comment'), 'fc')			
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');
		$sLink = Phpfox_Url::instance()->makeUrl($aRow['parent_user_name'], array('comment-id' => $aRow['feed_comment_id']));
		
		$aReturn = array(
			'no_share' => true,
			'feed_status' => $aRow['content'],
			'feed_link' => $sLink,
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/comment.png', 'return_url' => true)),
			'time_stamp' => $aRow['time_stamp'],			
			'enable_like' => true,			
			'comment_type_id' => 'feed',
			'like_type_id' => 'feed_comment',
            'location_latlng' => isset($aRow['location_latlng']) ? json_decode($aRow['location_latlng'], true) : '',
            'location_name' => isset($aRow['location_name']) ? $aRow['location_name'] : ''
		);

		if (!empty($aRow['parent_user_name']) && !defined('PHPFOX_IS_USER_PROFILE') && empty($_POST))
		{
			$aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
		}

		if (defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId())
		{
			$aReturn['feed_info'] = _p('posted_on_parent_full_names_wall', array('parent_user_name' => Phpfox_Url::instance()->makeUrl($aRow['parent_user_name']), 'parent_full_name' => $aRow['parent_full_name']));
			$aReturn['feed_status'] = $aRow['content'];
			$aReturn['parent_user_id'] = $aRow['user_id'];
		}

		return $aReturn;		
	}
    
    /**
     * @param int  $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return void
     */
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$this->database()->updateCount('like', 'type_id = \'feed_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'feed_comment', 'feed_comment_id = ' . (int) $iItemId);	
	}
    
    /**
     * @param int $iItemId
     *
     * @return void
     */
	public function addLikeEgift($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'feed_egift\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'feed_comment', 'feed_comment_id = ' . (int) $iItemId);
	}
    
    /**
     * @param int $iItemId
     *
     * @return void
     */
	public function deleteLikeComment($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'feed_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'feed_comment', 'feed_comment_id = ' . (int) $iItemId);	
	}
    
    /**
     * @return null
     */
	public function getAjaxCommentVar()
	{
		return null;
	}
    
    /**
     * @param array       $aVals
     * @param null|int    $iUserId
     * @param null|string $sUserName
     */
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{		
		$aRow = $this->database()->select('fc.feed_comment_id, fc.content, u.full_name, u.user_id, u.gender, u.user_name, u2.user_name AS parent_user_name, u2.full_name AS parent_full_name')
			->from(Phpfox::getT('feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('user'), 'u2', 'u2.user_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('feed_comment', 'total_comment', 'feed_comment_id', $aRow['feed_comment_id']);		
		}
		
		// Send the user an email
		$sLink = Phpfox_Url::instance()->makeUrl($aRow['parent_user_name'], array('comment-id' => $aRow['feed_comment_id']));
		$iSenderId = $aRow['user_id'];
		// get tagged users (tagged by @)
        $aTaggedUsers = [];

        if (preg_match_all('/\[user=(\d+)\].+?\[\/user\]/i', $aRow['content'], $aUsers)) {
            foreach ($aUsers[1] as $iUserId) {
                if ($iUserId && $iUserId != $iSenderId) {
                    $aTaggedUsers[] = $iUserId;
                }
            }
        }
        // get tagged users (tagged by "With")
        $aTaggedByWith = db()->select('user_id')->from(':feed_tag_data')->where([
            'item_id' => $aRow['feed_comment_id'],
            'type_id' => 'feed_comment'
        ])->executeRows();
        $aTaggedUsers = array_merge($aTaggedUsers, array_column($aTaggedByWith, 'user_id'));

        $sSubject = (Phpfox::getUserId() == $iSenderId ?
            _p('full_name_commented_on_one_of_gender_wall_comments',
                array('full_name' => Phpfox::getUserBy('full_name'),
                    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)
            ))
            :
            _p('full_name_commented_on_one_of_row_full_name_s_wall_comments',
                array('full_name' => Phpfox::getUserBy('full_name'),
                    'row_full_name' => $aRow['full_name'])
            ));

        $sMessage = (Phpfox::getUserId() == $iSenderId ?
            _p('full_name_commented_on_one_of_gender_wall_comments_message',
                array('full_name' => Phpfox::getUserBy('full_name'),
                    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'link' => $sLink))
            :
            _p('full_name_commented_on_one_of_row_full_name_s_wall_comments_message',
                array('full_name' => Phpfox::getUserBy('full_name'),
                    'row_full_name' => $aRow['full_name'], 'link' => $sLink)
            ));
        // notify tagged users
        if (!empty($aTaggedUsers)) {

            // send email and notification to each user that were tagged
            foreach ($aTaggedUsers as $iUserId) {
                Phpfox_Mail::instance()->to($iUserId)
                    ->subject($sSubject)
                    ->message($sMessage)
                    ->notification('comment.add_new_comment')
                    ->send();
                Phpfox::getService('notification.process')->add('comment_feed', $aVals['item_id'], $iUserId,
                    $iSenderId);
            }
        }
        Phpfox::getService('comment.process')->notify(array(
				'user_id' => $aRow['user_id'],
				'item_id' => $aRow['feed_comment_id'],
				'owner_subject' => _p('full_name_commented_on_one_of_your_wall_comments', array('full_name' => Phpfox::getUserBy('full_name'))),
				'owner_message' => _p('full_name_commented_on_one_of_your_wall_comments_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink)),
				'owner_notification' => 'comment.add_new_comment',
				'notify_id' => 'comment_feed',
				'mass_id' => 'feed',
				'mass_subject' => $sSubject,
				'mass_message' => $sMessage,
                'exclude_users' => $aTaggedUsers
			)
		);		
	}
    
    /**
     * @param int $iId
     *
     * @return array|int|string
     */
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
			->from(Phpfox::getT('feed_comment'))
			->where('feed_comment_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}
    
    /**
     * @param array $aNotification
     *
     * @return array
     */
	public function getCommentNotificationFeed($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, fc.content, fc.parent_user_id, u.gender, u.user_name, u.full_name, u2.user_name AS parent_user_name, u2.full_name AS parent_full_name')	
			->from(Phpfox::getT('feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('user'), 'u2', 'u2.user_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);

		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_commented_on_one_of_gender_wall_comments', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$aMentions = Phpfox::getService('user.process')->getIdFromMentions($aRow['content']);
			$bUseDefault = true;
			foreach ($aMentions as $iKey => $iUser)
			{
				if ($iUser == $aRow['parent_user_id'])
				{
					$bUseDefault = false;
				}
			}
			if ($bUseDefault)
			{
				$sPhrase = _p('users_commented_on_one_of_your_wall_comments', array('users' => $sUsers));
			}
			else
			{
				$sPhrase = _p('parent_user_name_commented_on_one_of_your_status_updates', array('parent_user_name' => $aRow['parent_full_name']));
			}
		}
		else 
		{
			$sPhrase = _p('users_commented_on_one_of_span_class_drop_data_user_row_full_name_s_span_wall_comments', array('users' => $sUsers, 'row_full_name' => $aRow['full_name']));
		}
			
		return array(
			'link' => Phpfox_Url::instance()->makeUrl($aRow['parent_user_name'], array('comment-id' => $aRow['feed_comment_id'])),
			'message' => $sPhrase,
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);
	}
    
    /**
     * @param array $aNotification
     *
     * @return array
     */
	public function getNotificationComment_Link($aNotification)
	{
		$aRow = $this->database()->select('fc.link_id, u.user_id, u.gender, u.user_name, u.full_name')
			->from(Phpfox::getT('link'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.parent_user_id')
			->where('fc.link_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');	
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);

		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_commented_on_gender_wall', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_commented_on_your_wall', array('users' => $sUsers));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_one_span_class_drop_data_user_row_full_name_span_wall', array('users' => $sUsers, 'row_full_name' => $aRow['full_name']));
		}			
		
		return array(
			'link' => Phpfox_Url::instance()->makeUrl($aRow['user_name'], array('plink-id' => $aRow['link_id'])),
			'message' => $sPhrase,
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);		
	}
    
    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
	public function getNotificationComment_Profile($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name')
			->from(Phpfox::getT('feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'] . ' AND fc.parent_user_id = '.(int)$aNotification['item_user_id'])
			->execute('getSlaveRow');
		
		$sType = 'comment-id';
		if (empty($aRow))
		{
			$aRow = $this->database()->select('u.user_id, u.gender, u.user_name, u.full_name')
				->from(Phpfox::getT('user_status'), 'fc')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
				->where('fc.status_id = ' . (int) $aNotification['item_id'])
				->execute('getSlaveRow');
			
			$aRow['feed_comment_id'] = (int) $aNotification['item_id'];
			$sType='status-id';
			$bWasChanged = true;
		}
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		if (empty($aRow) || !isset($aRow['user_id']))
        {
            return false;
        }

		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			if (isset($bWasChanged))
			{
				$sPhrase = _p('user_name_tagged_you_in_a_status_update', array('user_name' => $aNotification['full_name']));
			}
			else
			{
				$sPhrase = _p('users_commented_on_gender_wall', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)));
			}
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_commented_on_your_wall', array('users' => $sUsers));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_one_span_class_drop_data_user_row_full_name_span_wall', array('users' => $sUsers, 'row_full_name' => $aRow['full_name']));
		}			
		
		return array(
			'link' => Phpfox_Url::instance()->makeUrl($aRow['user_name'], array($sType => $aRow['feed_comment_id'])),
			'message' => $sPhrase,
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);
	}

	/**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationTagged_Profile($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name')
            ->from(Phpfox::getT('feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sType = 'comment-id';
        if (empty($aRow)) {
            $aRow = $this->database()->select('u.user_id, u.gender, u.user_name, u.full_name')
                ->from(Phpfox::getT('user_status'), 'fc')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
                ->where('fc.status_id = ' . (int)$aNotification['item_id'])
                ->execute('getSlaveRow');

            $aRow['feed_comment_id'] = (int)$aNotification['item_id'];
            $sType = 'status-id';
        }
        if (empty($aRow) || !isset($aRow['user_id'])) {
            return false;
        }

        $sPhrase = _p('user_name_tagged_you_in_a_status_update', array('user_name' => $aNotification['full_name']));


        return array(
            'link' => Phpfox_Url::instance()->makeUrl($aRow['user_name'], array($sType => $aRow['feed_comment_id'])),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
	public function getNotificationTagged_Link($aNotification)
	{
		$aRow = $this->database()->select('u.user_name, u.full_name')
			->from(Phpfox::getT('link'), 'l')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.link_id = ' . (int) $aNotification['item_id'])
			->executeRow();

		return array(
			'link' => Phpfox_Url::instance()->makeUrl($aRow['user_name'], array('link-id' => $aNotification['item_id'])),
			'message' => _p('full_name_tagged_you_in_a_link', ['full_name' => $aRow['full_name']]),
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);
	}
    
    /**
     * @param int  $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool|null
     */
	public function addLikeComment($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, u2.user_name, u2.full_name')
			->from(Phpfox::getT('feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('user'), 'u2', 'u2.user_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['feed_comment_id']))
		{
			return false;
		}		
		
		$this->database()->updateCount('like', 'type_id = \'feed_comment\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'feed_comment', 'feed_comment_id = ' . (int) $iItemId);		
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox_Url::instance()->makeUrl($aRow['user_name'], array('comment-id' => $aRow['feed_comment_id']));
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('user.full_name_liked_a_comment_you_posted_on_row_full_name_s_wall', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'])))
				->message(array('user.full_name_liked_your_comment_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'content' => Phpfox::getLib('parse.output')->shorten($aRow['content'], 50, '...'), 'row_full_name' => $aRow['full_name'])))
				->notification('like.new_like')
				->send();				
					
			Phpfox::getService('notification.process')->add('feed_comment_like', $aRow['feed_comment_id'], $aRow['user_id']);
		}
		return null;
	}
    
    /**
     * @param int $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool
     */
	public function addLikeMini($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('c.comment_id, c.user_id, ct.text_parsed AS text')
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->where('c.comment_id = ' . (int) $iItemId)
			->execute('getSlaveRow');
			
		if (!isset($aRow['comment_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'feed_mini\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'comment', 'comment_id = ' . (int) $iItemId);

		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox_Url::instance()->makeUrl('comment.view', $iItemId);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('user.full_name_liked_one_of_your_comments', array('full_name' => Phpfox::getUserBy('full_name'))))
				->message(array('user.full_name_liked_your_comment_message_mini', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'content' => Phpfox::getLib('parse.output')->shorten($aRow['text'], 50, '...'))))
				->notification('like.new_like')
				->send();
					
			Phpfox::getService('notification.process')->add('feed_mini_like', $aRow['comment_id'], $aRow['user_id']);
		}
		return null;
	}
    
    /**
     * @param int  $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return null
     */
	public function deleteLikeMini($iItemId, $bDoNotSendEmail = false)
	{
		$this->database()->updateCount('like', 'type_id = \'feed_mini\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'comment', 'comment_id = ' . (int) $iItemId);	
	}
    
    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
	public function getNotificationMini_Like($aNotification)
	{
		$aRow = $this->database()->select('c.comment_id, c.user_id, ct.text_parsed AS text')
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->where('c.comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');

        if (!$aRow) {
            return false;
        } else {
            $sPhrase = _p('users_liked_your_comment_text_that_you_posted', array('users' => Phpfox::getService('notification')->getUsers($aNotification) , 'text' => Phpfox::getLib('parse.output')->shorten($aRow['text'], Phpfox::getParam('notification.total_notification_title_length'), '...')));
            return array(
                'link' => Phpfox_Url::instance()->makeUrl('comment.view', $aRow['comment_id']),
                'message' => $sPhrase,
                'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
            );
        }
    }
    
    /**
     * @param array $aNotification
     *
     * @return array
     */
	public function getNotificationComment_Like($aNotification)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, u.gender, u.user_name, u.full_name, u2.user_name AS parent_user_name, u2.full_name AS parent_full_name, u2.gender AS parent_gender')	
			->from(Phpfox::getT('feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->join(Phpfox::getT('user'), 'u2', 'u2.user_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sContent = Phpfox::getLib('parse.output')->shorten($aRow['content'], Phpfox::getParam('notification.total_notification_title_length'), '...');

		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_comment_content', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'content' => $sContent));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_comment_content_that_you_posted_on_span_class_drop_data_user_parent_full_name_s_span_wall', array('users' => $sUsers, 'content' => $sContent, 'parent_full_name' => $aRow['parent_full_name']));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_full_name_s_span_comment_content', array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'content' => $sContent));
		}
			
		return array(
			'link' => Phpfox_Url::instance()->makeUrl($aRow['parent_user_name'], array('comment-id' => $aRow['feed_comment_id'])),
			'message' => $sPhrase,
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);	
	}
    
    /**
     * @param array $aComment
     *
     * @return string
     */
	public function getParentItemCommentUrl($aComment)
	{
		$aFeedComment = $this->database()->select('u.user_name')
			->from(Phpfox::getT('feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.parent_user_id')
			->where('fc.feed_comment_id = ' . (int) $aComment['item_id'])
			->execute('getSlaveRow');
			
		return Phpfox_Url::instance()->makeUrl($aFeedComment['user_name'], array('comment-id' => $aComment['item_id']));
	}
    
    /**
     * @param string $sProductId
     * @param null   $sModule
     *
     * @return bool
     */
	public function exportModule($sProductId, $sModule = null)
	{
		$aSql = array();
		$aSql[] = "product_id = '" . $sProductId . "'";
		if ($sModule !== null)
		{
			$aSql[] = "AND module_id = '" . $sModule . "'";
		} 
		
		$aRows = $this->database()->select('*')
			->from(Phpfox::getT('feed_share'))
			->where($aSql)
			->execute('getSlaveRows');
			
		if (!count($aRows))
		{
			return false;
		}
			
		$oXmlBuilder = Phpfox::getLib('xml.builder');
		$oXmlBuilder->addGroup('feed_share');

		foreach ($aRows as $aRow)
		{
			$oXmlBuilder->addTag('share', '', array(					
					'module_id' => $aRow['module_id'],
					'title' => $aRow['title'],
					'description' => $aRow['description'],
					'block_name' => $aRow['block_name'],
					'no_input' => $aRow['no_input'],
					'is_frame' => $aRow['is_frame'],
					'ajax_request' => $aRow['ajax_request'],
					'no_profile' => $aRow['no_profile'],
					'icon' => $aRow['icon'],
					'ordering' => $aRow['ordering']
				)
			);
		}
		$oXmlBuilder->closeGroup();

		return true;	
	}
    
    /**
     * @param string $sProduct
     * @param string $sModule
     * @param array  $aModule
     *
     * @return void
     */
	public function installModule($sProduct, $sModule, $aModule)
	{		
		if (isset($aModule['feed_share']))
		{
			// get all the existing feed_share
			$aShares = $this->database()->select('*')
				->from(Phpfox::getT('feed_share'))
				->where(['module_id' => Phpfox::getLib('parse.input')->clean($sModule), 'product_id' => Phpfox::getLib('parse.input')->clean($sProduct)])
				->execute('getSlaveRows');
			$aRows = (isset($aModule['feed_share']['share'][1]) ? $aModule['feed_share']['share'] : array($aModule['feed_share']['share']));
			foreach ($aRows as $aRow)
			{
				foreach($aShares as $aShare)
				{
					if ($aShare['title'] == $aRow['title'])
					{
						break 2;
					}
				}
				$this->database()->insert(Phpfox::getT('feed_share'), array(
						'product_id' => $sProduct,
						'module_id' => ($sModule === null ? $aRow['module_id'] : $sModule),						
						'title' => $aRow['title'],
						'description' => $aRow['description'],
						'block_name' => $aRow['block_name'],
						'no_input' => (int) $aRow['no_input'],
						'is_frame' => (int) $aRow['is_frame'],
						'ajax_request' => (empty($aRow['ajax_request']) ? null : $aRow['ajax_request']),
						'no_profile' => (int) $aRow['no_profile'],
						'icon' => (empty($aRow['icon']) ? null : $aRow['icon']),
						'ordering' => (int) $aRow['ordering']
					)
				);
			}
		}
	}
    
    /**
     * @return array
     */
	public function updateCounterList()
	{
        $aList = [];
        
        $aList[] = [
            'name' => _p('find_missing_share_buttons'),
            'id'   => 'missing-share'
        ];
        
        $aList[] = [
            'name' => _p('update_feed_time_stamps'),
            'id'   => 'update-feed'
        ];
        
        $aList[] = [
            'name' => _p('update_feed_time_stamps_for_pages'),
            'id'   => 'update-pages-feed'
        ];
        
        $aList[] = [
            'name' => _p('update_feed_time_stamps_for_events'),
            'id'   => 'update-event-feed'
        ];
        
        return $aList;
    }
    
    /**
     * @param $iId
     * @param $iPage
     * @param $iPageLimit
     *
     * @return array|int|string
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
	{	
		if (!empty($iId))
		{
			$sPrefix = '';
			if ($iId == 'update-pages-feed')
			{
				$sPrefix = 'pages_';
			}
			elseif ($iId == 'update-event-feed')
			{
				$sPrefix = 'event_';
			}			
			//  == 'update-pages-feed'

			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT($sPrefix . 'feed'))
				->where('time_update = 0')
				->execute('getSlaveField');
			
			$aRows = $this->database()->select('feed_id, time_stamp')
				->from(Phpfox::getT($sPrefix . 'feed'))
				->where('time_update = 0')				
				->limit($iPage, $iPageLimit, $iCnt)
				->execute('getSlaveRows');	

			foreach ($aRows as $aRow)
			{
				$this->database()->update(Phpfox::getT('feed'), array('time_update' => $aRow['time_stamp']), 'feed_id = ' . (int) $aRow['feed_id']);
			}
			
			return $iCnt;
		}
		else
		{
			$aModules = Phpfox::getService('core')->getModulePager('feed_share', 0, 200);
			
			foreach ($aModules as $sModule => $aData)
			{
				$iCheck = $this->database()->select('COUNT(*)')
					->from(Phpfox::getT('feed_share'))
					->where('module_id = \'' . $this->database()->escape($aData['share']['module_id']) . '\' AND title = \'' . $this->database()->escape($aData['share']['title']) . '\'')
					->execute('getSlaveField');
				
				if (!$iCheck)
				{
					$aRow = $aData['share'];
					$this->database()->insert(Phpfox::getT('feed_share'), array(
							'product_id' => 'phpfox',
							'module_id' => $aData['share']['module_id'],						
							'title' => $aRow['title'],
							'description' => $aRow['description'],
							'block_name' => $aRow['block_name'],
							'no_input' => (int) $aRow['no_input'],
							'is_frame' => (int) $aRow['is_frame'],
							'ajax_request' => (empty($aRow['ajax_request']) ? null : $aRow['ajax_request']),
							'no_profile' => (int) $aRow['no_profile'],
							'icon' => (empty($aRow['icon']) ? null : $aRow['icon']),
							'ordering' => (int) $aRow['ordering']
						)
					);				
				}
			}
		}
		
		return 0;
	}

    /**
     * Used from the Ad module when sponsoring a post in the feed.
     * Complies with the requirement in the ad.sponsor controller for $aItem
     *
     * @param $aParams array
     *
     * @return array
     */
    public function getSponsorPostInfo($aParams)
    {
        $aInfo = [
            'title' => _p('sponsoring') . ' ' . $aParams['sModule'] . ' #' . $aParams['iItemId'],
            'link' => Phpfox::callback($aParams['sModule'] . '.getLink', ['item_id' => $aParams['iItemId']]),
            'paypal_msg' => _p('purchasing_a_sponsored_feed') . ' ',
            'item_id' => $aParams['iItemId'],
            'user_id' => Phpfox::getUserId()
        ];

        if (Phpfox::isModule($aParams['sModule']) && Phpfox::hasCallback($aParams['sModule'], 'getToSponsorInfo')) {
            $aCalled = Phpfox::callback($aParams['sModule'] . '.getToSponsorInfo', $aParams['iItemId']);
            $aInfo = array_merge($aInfo, $aCalled);
        }

        return $aInfo;
    }
    
    /**
     * @param int $iId feed_id
     *
     * @return array|bool in the format:
     * array(
     *    'sModule' => 'module_id',            <-- required
     *  'iItemId'  => 'item_id',            <-- required
     * )
     */
	public function getToSponsorInfo($iId)
	{
        $aFeed = $this->database()
            ->select('f.type_id AS sModule, f.item_id AS iItemId')
            ->from($this->_sTable, 'f')
            ->where('f.feed_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        
        if (empty($aFeed)) {
            return false;
        }
        
        return $this->getSponsorPostInfo($aFeed);
	}

    /**
     * @param array $aParams
     *
     * @return bool|mixed
     */
    public function getLink($aParams)
    {
        $aFeed = $this->database()->select('f.type_id as section, f.item_id')
            ->from($this->_sTable, 'f')
            ->where('f.feed_id = ' . (int)$aParams['item_id'])
            ->execute('getSlaveRow');

        if (empty($aFeed)) {
            return false;
        }

        if (Phpfox::hasCallback($aFeed['section'], 'getLink')) {
            return Phpfox::callback($aFeed['section'] . '.getLink', $aFeed);
        }

        return false;
    }
    
    /**
     * @param array $aParams
     *
     * @return bool|mixed
     */
	public function enableSponsor($aParams)
	{
		$aFeed = $this->database()->select('f.type_id as section, f.item_id')
			->from($this->_sTable, 'f')
			->where('f.feed_id = ' . (int)$aParams['item_id'])
		    ->execute('getSlaveRow');
		    
	    if (empty($aFeed))
	    {
			return false;
	    }

	    if (Phpfox::hasCallback($aFeed['section'], 'enableSponsor'))
		{
			return Phpfox::callback($aFeed['section'] . '.enableSponsor', $aFeed);
		}
		
		return false;
	}
}
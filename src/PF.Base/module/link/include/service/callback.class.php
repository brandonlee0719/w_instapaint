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
 * @version 		$Id: callback.class.php 7309 2014-05-08 16:05:43Z Fern $
 */
class Link_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('link');
	}

    public function getCommentNotificationTag($aNotification)
    {
        $aRow = $this->database()->select('c.comment_id, l.link_id, l.title, u.user_name, u.full_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('link'), 'l', 'l.link_id = c.item_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        $aUser = Phpfox::getService('user')->getUser($aNotification['user_id'], 'u.full_name');

        if (!$aUser) {
            return false;
        }

        $sPhrase = $aUser['full_name'] . ' ' . _p('mentioned_you_in_a_post');

        return array(
            'link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']) . 'link-id_' . $aRow['link_id'] . '/comment_' . $aRow['comment_id'] . '/',
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

	public function getActivityFeedCustomChecks($aRow)
	{
		if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'link.view_browse_links'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['custom_data_cache']['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['custom_data_cache']['item_id'], 'link.view_browse_links'))
		)
		{
			return false;
		}

		return $aRow;
	}
    public function canShareItemOnFeed(){
        return true;
    }
	public function getActivityFeed($aItem, $aCallBack = null, $bIsChildItem = false)
	{
		if (Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'link\' AND l.item_id = link.link_id AND l.user_id = ' . Phpfox::getUserId());
		}

		$sSelect = '';
		if (db()->tableExists(Phpfox::getT('pages'))) {
		    $sSelect .= 'p.*, pu.vanity_url,';
		    $this->database()->leftJoin(Phpfox::getT('pages'), 'p', 'p.page_id = link.item_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = link.item_id');
        }

        $sSelect .= Phpfox::getUserField('u', 'parent_') . ', link.*';
        $aRow = $this->database()->select($sSelect)
		    ->from($this->_sTable, 'link') 
		    ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = link.parent_user_id')
		    ->where('link.link_id = ' . (int) $aItem['item_id'])
		    ->execute('getSlaveRow'); 
		if (!isset($aRow['link_id']))
		{
			return false;
		}
		
		if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($aRow['item_id'], 'link.view_browse_links'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'link.view_browse_links')))
		)
		{
			return false;
		}		

		if (empty($aRow['link']))
		{
			return false;
		}
		
		if($aRow['module_id'] == 'pages')
		{
			$aPage = Phpfox::getService('pages')->getForView($aRow['parent_user_id']);

            if (empty($aPage)) {
                return false;
            }

			$aNewUser = Phpfox::getService('user')->getUser($aPage['page_user_id']);
			
			// Override the values
			$aRow['parent_profile_page_id'] = $aNewUser['profile_page_id'];
			$aRow['user_parent_server_id'] = $aNewUser['server_id'];
			$aRow['parent_user_name'] = (!empty($aNewUser['user_name']) ? $aNewUser['user_name'] : '');
			$aRow['parent_full_name'] = $aNewUser['full_name'];
			$aRow['parent_gender'] = $aNewUser['gender'];
			$aRow['parent_user_image'] = $aNewUser['user_image'];
			$aRow['parent_is_invisible'] = $aNewUser['is_invisible'];
			$aRow['parent_user_group_id'] = $aNewUser['user_group_id'];
			$aRow['parent_language_id'] = $aNewUser['language_id'];
			$aRow['parent_last_activity'] = $aNewUser['last_activity'];		
			unset($aNewUser);
		}

        if($aRow['module_id'] == 'groups')
        {
            $aPage = Phpfox::getService('groups')->getForView($aRow['parent_user_id']);

            if (empty($aPage)) {
                return false;
            }

            $aNewUser = Phpfox::getService('user')->getUser($aPage['page_user_id']);

            // Override the values
            $aRow['parent_profile_page_id'] = $aNewUser['profile_page_id'];
            $aRow['user_parent_server_id'] = $aNewUser['server_id'];
            $aRow['parent_user_name'] = (!empty($aNewUser['user_name']) ? $aNewUser['user_name'] : '');
            $aRow['parent_full_name'] = $aNewUser['full_name'];
            $aRow['parent_gender'] = $aNewUser['gender'];
            $aRow['parent_user_image'] = $aNewUser['user_image'];
            $aRow['parent_is_invisible'] = $aNewUser['is_invisible'];
            $aRow['parent_user_group_id'] = $aNewUser['user_group_id'];
            $aRow['parent_language_id'] = $aNewUser['language_id'];
            $aRow['parent_last_activity'] = $aNewUser['last_activity'];
            unset($aNewUser);
        }
		
		if (substr($aRow['link'], 0, 7) != 'http://' && substr($aRow['link'], 0, 8) != 'https://')
		{
			$aRow['link'] = 'http://' . $aRow['link'];
		}
		
		$aParts = parse_url($aRow['link']);		
				
		$aReturn = array(
		    'feed_title' => $aRow['title'],
		    'feed_status' => $aRow['status_info'], 
		    'feed_link_comment' => Phpfox_Url::instance()->makeUrl($aItem['user_name'], array('link-id' => $aRow['link_id'])),
		    'feed_link' => Phpfox_Url::instance()->makeUrl($aItem['user_name'], array('link-id' => $aRow['link_id'])),
			'feed_link_actual' => $aRow['link'],
		    'feed_content' => $aRow['description'], 
		    'total_comment' => $aRow['total_comment'], 
		    'feed_total_like' => $aRow['total_like'], 
		    'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
		    'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'feed/link.png', 'return_url' => true)), 
		    'time_stamp' => $aItem['time_stamp'],
		    'enable_like' => true,
		    'comment_type_id' => 'link', 
		    'like_type_id' => 'link', 
		    'feed_title_extra' => $aParts['host'], 
		    'feed_title_extra_link' => $aParts['scheme'] . '://' . $aParts['host'], 
			'custom_data_cache' => $aRow
		);

        $aReturn['friends_tagged'] = Phpfox::getService('feed')->getTaggedUsers($aItem['item_id'], 'link');

        if (!empty($aRow['location_name']))
        {
            $aReturn['location_name'] = $aRow['location_name'];
        }
        if (!empty($aRow['location_latlng']))
        {
            $aReturn['location_latlng'] = json_decode($aRow['location_latlng'], true);
        }
		
		if (Phpfox::getParam('core.warn_on_external_links'))
		{
			if (!preg_match('/' . preg_quote(Phpfox::getParam('core.host')) . '/i', $aRow['link']))
			{
				$aReturn['feed_link_actual'] = Phpfox_Url::instance()->makeUrl('core.redirect', array('url' => Phpfox_Url::instance()->encode($aRow['link'])));
				$aReturn['feed_title_extra_link'] = Phpfox_Url::instance()->makeUrl('core.redirect', array('url' => Phpfox_Url::instance()->encode($aReturn['feed_title_extra_link'])));
			}						
		}
		
		if (!empty($aRow['image']))
		{
            $sImage = Phpfox::getLib('url')->secureUrl($aRow['image']);
			$aReturn['feed_image'] = '<img src="' . $sImage . '" alt="" />';
		}
		
		if ($aRow['module_id'] == 'pages' || $aRow['module_id'] == 'event')
		{
			$aReturn['parent_user_id'] = 0;
			$aReturn['parent_user_name'] = '';
		}			
		
		if (empty($aRow['module_id']) && !empty($aRow['parent_user_name']) && !defined('PHPFOX_IS_USER_PROFILE') && empty($_POST))
		{
			$aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
		}
		if($aRow['module_id'] == 'pages' && empty($aRow['parent_user_name']) && Phpfox_Request::instance()->get('link-id') > 0)
		{
			$sLink = Phpfox_Url::instance()->makeUrl('pages', $aRow['parent_user_id']);
			$aReturn['feed_mini'] = true;
			$aReturn['no_share'] = true;
			$aReturn['feed_mini_content'] = _p('full_name_posted_a_href_link_a_link_a_on_a_href_parent_user_name',
				array(
					'full_name' => Phpfox::getService('user')->getFirstName($aItem['full_name']),
					'link' => $sLink,
					'parent_user_name' => $sLink, 
					'parent_full_name' => $aRow['parent_full_name']
				)
			);
			
			unset($aReturn['feed_status'], $aReturn['feed_image'], $aReturn['feed_title'], $aReturn['feed_content']);
		}
		else
		{
			if ($aRow['has_embed'])
			{
				$aReturn['feed_image_onclick'] = '$Core.box(\'link.play\', 700, \'id=' . $aRow['link_id'] . '&amp;feed_id=' . $aItem['feed_id'] . '&amp;popup=true\', \'GET\'); return false;';
			}
		}
        if ($bIsChildItem){
            $aReturn = array_merge($aReturn, $aItem);
        }

        (($sPlugin = Phpfox_Plugin::get('link.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);
        if ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && $aRow['page_id'] && $aItem['profile_page_id'] == 0) {
            $aReturn['parent_user_name'] = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                $aRow['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if (!defined('PHPFOX_IS_PAGES_VIEW') && empty($_POST)) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
            }
            unset($aReturn['parent_user_id']);
        }
		return $aReturn;
	}
	
	public function addLike($iItemId, $bDoNotSendEmail = false)
	{
		$aRow = $this->database()->select('link_id, title, user_id')
			->from(Phpfox::getT('link'))
			->where('link_id = ' . (int) $iItemId)
			->execute('getSlaveRow');		
			
		if (!isset($aRow['link_id']))
		{
			return false;
		}
		
		$this->database()->updateCount('like', 'type_id = \'link\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'link', 'link_id = ' . (int) $iItemId);	
		
		if (!$bDoNotSendEmail)
		{
			$sLink = Phpfox::permalink('link', $aRow['link_id'], $aRow['title']);
			
			Phpfox::getLib('mail')->to($aRow['user_id'])
				->subject(array('link.full_name_liked_your_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])))
				->message(array('link.full_name_liked_your_link_title_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])))
				->notification('like.new_like')
				->send();
				
			Phpfox::getService('notification.process')->add('link_like', $aRow['link_id'], $aRow['user_id']);
		}
        return null;
	}	
	
	public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()->select('l.link_id, l.title, l.user_id, u.gender, u.full_name')	
			->from(Phpfox::getT('link'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.link_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
		
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
			
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_link_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_link_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_link_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox_Url::instance()->permalink('link', $aRow['link_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);	
	}	
	
	public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'link\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'link', 'link_id = ' . (int) $iItemId);	
	}		
	
	public function deleteComment($iId)
	{
		$this->database()->update(Phpfox::getT('link'), array('total_comment' => array('= total_comment -', 1)), 'link_id = ' . (int) $iId);
	}	
	
	public function getAjaxCommentVar()
	{
		return null;
	}	
	
	public function addComment($aVals, $iUserId = null, $sUserName = null)
	{		
		$aRow = $this->database()->select('l.link_id, l.title, l.status_info, u.full_name, u.user_id, u.user_name, u.gender')
			->from(Phpfox::getT('link'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.link_id = ' . (int) $aVals['item_id'])
			->execute('getSlaveRow');
			
		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('link', 'total_comment', 'link_id', $aRow['link_id']);		
		}
		
		// Send the user an email
		$sLink = Phpfox_Url::instance()->permalink('link', $aRow['link_id'], $aRow['title']);
        
        Phpfox::getService('comment.process')->notify(array(
            'user_id' => $aRow['user_id'],
            'item_id' => $aRow['link_id'],
            'owner_subject' => _p('full_name_commented_on_your_link_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $this->preParse()->clean($aRow['title'], 100))),
            'owner_message' => _p('full_name_commented_on_your_link_a_href_link_title_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])),
            'owner_notification' => 'comment.add_new_comment',
            'notify_id' => 'comment_link',
            'mass_id' => 'link',
            'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_gender_link', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : _p('full_name_commented_on_row_full_name_s_link', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
            'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_gender_link_a_href_link_title_a', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'link' => $sLink, 'title' => $aRow['title'])) : _p('full_name_commented_on_row_full_name_s_link_a_href_link_title_a_message', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'], 'link' => $sLink, 'title' => $aRow['title'])))
			)
		);

        // send notification to tagged users
        preg_match_all('/\[user=(\d+)\].+?\[\/user\]/i', $aRow['status_info'], $aUsers);
        if (is_array($aUsers) && count($aUsers) == 2) {
            foreach ($aUsers[1] as $iUserId) {
                if ($iUserId != Phpfox::getUserId()) {
                    Phpfox::getService('notification.process')->add('comment_link', $aRow['link_id'], $iUserId, Phpfox::getUserId());

                    // send email
                    Phpfox::getLib('mail')->to($iUserId)
                        ->subject(_p('full_name_commented_on_row_full_name_s_link', [
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'row_full_name' => $aRow['full_name']
                        ]))
                        ->message(_p('full_name_commented_on_row_full_name_s_link_a_href_link_title_a_message', [
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'row_full_name' => $aRow['full_name'],
                            'link' => $sLink,
                            'title' => $aRow['title']
                        ]))
                        ->send();
                }
            }
        }
	}
	
	public function getCommentItem($iId)
	{
		$aRow = $this->database()->select('link_id AS comment_item_id, privacy_comment, user_id AS comment_user_id, module_id AS parent_module_id')
			->from(Phpfox::getT('link'))
			->where('link_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
			
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}	
	
	public function getCommentNotification($aNotification)
	{
		$aRow = $this->database()->select('l.link_id, l.title, u.user_id, u.gender, u.user_name, u.full_name')	
			->from(Phpfox::getT('link'), 'l')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
			->where('l.link_id = ' . (int) $aNotification['item_id'])
			->execute('getSlaveRow');
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users']))
		{
			$sPhrase = _p('users_commented_on_gender_link_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())
		{
			$sPhrase = _p('users_commented_on_your_link_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_link_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' =>  $sTitle));
		}
			
		return array(
			'link' => Phpfox_Url::instance()->permalink('link', $aRow['link_id'], $aRow['title']),
			'message' => $sPhrase,
			'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
		);
	}
	
	public function canViewPageSection($iPage)
	{
        if (Phpfox::isModule('pages')){
            return false;
        }
		if (!Phpfox::getService('pages')->hasPerm($iPage, 'link.view_browse_links'))
		{
			return false;
		}
		
		return true;
	}
	
	public function checkFeedShareLink()
	{
		(($sPlugin = Phpfox_Plugin::get('link.service_callback_checkfeedsharelink')) ? eval($sPlugin) : ''); 
		
		if (isset($bNoFeedLink))
		{
			return false;
		}
		
		if (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'link.share_links'))
		{
			return false;
		}
	}	
	
	public function getRedirectComment($iId)
	{
		$aLink = $this->database()->select('u.user_name')
            ->from(Phpfox::getT('link'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where('l.link_id = ' . (int)$iId)
            ->execute('getSlaveField');
		
		$sLink = Phpfox_Url::instance()->makeUrl($aLink, array('link-id' => $iId));
		return $sLink;
	}
	public function getSqlTitleField()
	{
		return array(
			'table' => 'link',
			'field' => 'description'
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
		if ($sPlugin = Phpfox_Plugin::get('link.service_callback__call'))
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
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
 * @package  		Module_Comment
 * @version 		$Id: comment.class.php 7059 2014-01-22 14:20:10Z Fern $
 */
class Comment_Service_Comment extends Phpfox_Service 
{	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('comment');
	}
    
    /**
     * @param int $iCommentId
     *
     * @return array|mixed
     */
	public function getQuote($iCommentId)
	{
	    $sCacheId = $this->cache()->set('comment_quote_' . (int) $iCommentId);

        if (!$aRow = $this->cache()->get($sCacheId)) {
            
            (($sPlugin = Phpfox_Plugin::get('comment.service_comment_getquote_start')) ? eval($sPlugin) : false);
    
            $aRow = $this->database()->select('cmt.comment_id, cmt.author, comment_text.text AS text, u.user_id')
                ->from($this->_sTable, 'cmt')
                ->join(Phpfox::getT('comment_text'), 'comment_text', 'comment_text.comment_id = cmt.comment_id')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = cmt.user_id')
                ->where('cmt.comment_id = ' . (int)$iCommentId)
                ->execute('getSlaveRow');
    
            if (!isset($aRow['comment_id'])) {
                return false;
            }
    
            if ($aRow['comment_id'] && !$aRow['user_id']) {
                $aRow['user_id'] = $aRow['author'];
            }
    
            (($sPlugin = Phpfox_Plugin::get('comment.service_comment_getquote_end')) ? eval($sPlugin) : false);
            
            $this->cache()->save($sCacheId, $aRow);
            Phpfox::getLib('cache')->group('comment', $sCacheId);
        }
		return $aRow;
	}
    
    /**
     * @param int $iId
     *
     * @return array
     */
	public function getComment($iId)
	{
        list(, $aRows) = $this->get('cmt.*', ['AND cmt.comment_id = ' . $iId], 'cmt.time_stamp DESC', 0, 1, 1);
        
        return (isset($aRows[0]['comment_id']) ? $aRows[0] : []);
    }
    
    /**
     * @param string $sSelect
     * @param array  $aConds
     * @param string $sSort
     * @param string $iRange
     * @param string $sLimit
     * @param null   $iCnt
     * @param bool   $bIncludeOwnerDetails
     *
     * @return array
     */
    public function get($sSelect, $aConds, $sSort = 'cmt.time_stamp DESC', $iRange = '', $sLimit = '', $iCnt = null, $bIncludeOwnerDetails = false)
	{
		(($sPlugin = Phpfox_Plugin::get('comment.service_comment_get__start')) ? eval($sPlugin) : false);
        
        $aRows = [];
        
        if ($iCnt === null)
		{
			(($sPlugin = Phpfox_Plugin::get('comment.service_comment_get_count_query')) ? eval($sPlugin) : false);
			
			$iCnt = $this->database()->select('COUNT(*)')
				->from($this->_sTable, 'cmt')
				->where($aConds)
				->execute('getSlaveField');		
		}

		if ($iCnt)
		{			
			if (Phpfox::isUser())
			{
				$this->database()->select('cr.comment_id AS has_rating, cr.rating AS actual_rating, ')
					->leftJoin(Phpfox::getT('comment_rating'), 'cr', 'cr.comment_id = cmt.comment_id AND cr.user_id = ' . (int) Phpfox::getUserId());
			}			
			
			if ($bIncludeOwnerDetails === true)
			{
				$this->database()->select(Phpfox::getUserField('owner', 'owner_') . ', ')
                    ->leftJoin(Phpfox::getT('user'), 'owner', 'owner.user_id = cmt.owner_user_id');
			}
			
			if(Phpfox::isModule('like'))
			{
				$this->database()->select('l.like_id AS is_liked, ')
						->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = cmt.comment_id AND l.user_id = ' . Phpfox::getUserId());
			}
			
			(($sPlugin = Phpfox_Plugin::get('comment.service_comment_get_query')) ? eval($sPlugin) : false);
			
			$aRows = $this->database()->select($sSelect . ", " . (Phpfox::getParam('core.allow_html') ? "comment_text.text_parsed" : "comment_text.text") ." AS text, " . Phpfox::getUserField())
				->from($this->_sTable, 'cmt')
				->leftJoin(Phpfox::getT('comment_text'), 'comment_text', 'comment_text.comment_id = cmt.comment_id')				
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = cmt.user_id')
				->where($aConds)
				->order($sSort)
				->limit($iRange, $sLimit, $iCnt)
				->execute('getSlaveRows');			
		}	

		$oUrl = Phpfox_Url::instance();
		$oParseOutput = Phpfox::getLib('parse.output');
		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['link'] = '';
			if ($aRow['user_name'])
			{
				$aRows[$iKey]['link'] = $oUrl->makeUrl($aRow['user_name']);
				$aRows[$iKey]['is_guest'] = false;
			} else
			{
                if (Phpfox::getUserBy('profile_page_id') > 0 && Phpfox::isModule('pages')) {
                    $aRows[$iKey]['full_name'] = $oParseOutput->clean(Phpfox::getUserBy('full_name'));
                } else {
                    $aRows[$iKey]['full_name'] = $oParseOutput->clean($aRow['author']);
                }

				$aRows[$iKey]['is_guest'] = true;
				if ($aRow['author_url'])
				{
					$aRows[$iKey]['link'] = $aRow['author_url'];	
				}
			}
			$aRows[$iKey]['unix_time_stamp'] = $aRow['time_stamp'];
			$aRows[$iKey]['time_stamp'] = Phpfox::getTime(Phpfox::getParam('comment.comment_time_stamp'), $aRow['time_stamp']);
			$aRows[$iKey]['posted_on'] = _p('user_link_at_item_time_stamp', array(
					'item_time_stamp' => Phpfox::getTime(Phpfox::getParam('comment.comment_time_stamp'), $aRow['time_stamp']),
					'user' => $aRow
				)
			);
			$aRows[$iKey]['update_time'] = Phpfox::getTime(Phpfox::getParam('comment.comment_time_stamp'), $aRow['update_time']);
			$aRows[$iKey]['post_convert_time'] = Phpfox::getLib('date')->convertTime($aRow['time_stamp'], 'comment.comment_time_stamp');
			if (Phpfox::hasCallback($aRow['type_id'], 'getCommentItemName')) {
                $aRows[$iKey]['item_name'] = Phpfox::callback($aRow['type_id'] . '.getCommentItemName');
            } else {
                $aRows[$iKey]['item_name'] = '';
            }
		}

		(($sPlugin = Phpfox_Plugin::get('comment.service_comment_get__end')) ? eval($sPlugin) : false);
        
		return array($iCnt, $aRows);
	}

    /**
     * Get a Comment for edit
     *
     * @param int $iCommentId
     *
     * @return array
     */
    public function getCommentForEdit($iCommentId)
    {
        (($sPlugin = Phpfox_Plugin::get('comment.service_comment_getcommentforedit')) ? eval($sPlugin) : false);

        $aComment = $this->database()->select('cmt.*, comment_text.text AS text')
            ->from($this->_sTable, 'cmt')
            ->join(Phpfox::getT('comment_text'), 'comment_text', 'comment_text.comment_id = cmt.comment_id')
            ->where('cmt.comment_id = ' . (int)$iCommentId)
            ->execute('getSlaveRow');

        return $aComment;
    }
    
    /**
     * @param int    $iCommentId
     * @param string $sUserPerm
     * @param string $sGlobalPerm
     *
     * @return bool
     */
	public function hasAccess($iCommentId, $sUserPerm, $sGlobalPerm)
	{
		(($sPlugin = Phpfox_Plugin::get('comment.service_comment_hasaccess_start')) ? eval($sPlugin) : false);
		
        $sCacheId = $this->cache()->set('comment_detail_access_' . $iCommentId);

        if (!$aRow = $this->cache()->get($sCacheId)) {
            $aRow = $this->database()->select('u.user_id')
                ->from($this->_sTable, 'cmt')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = cmt.user_id')
                ->where('cmt.comment_id = ' . (int) $iCommentId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aRow);
            Phpfox::getLib('cache')->group('comment', $sCacheId);
        }
        
		(($sPlugin = Phpfox_Plugin::get('comment.service_comment_hasaccess_end')) ? eval($sPlugin) : false);
        
        if (!isset($aRow['user_id'])) {
            return false;
        }
		
		if ((Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('comment.' . $sUserPerm)) || Phpfox::getUserParam('comment.' . $sGlobalPerm)) {
			return $aRow['user_id'];
		}
		
		return false;
	}
    
    /**
     * @return array
     */
	public function getPendingComments()
	{
	    $iUserId = Phpfox::getUserId();
	    $sCacheId = $this->cache()->set('comment_pending_user_' . $iUserId);

        if (!$aComments = $this->cache()->get($sCacheId)) {
            $aComments = $this->database()
                ->select('c.*, ' . (Phpfox::getParam('core.allow_html') ? "ct.text_parsed" : "ct.text") . ' AS text, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'c')
                ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                ->where('c.owner_user_id = ' . $iUserId . ' AND c.view_id = 1')
                ->group('c.comment_id', true)
                ->execute('getSlaveRows');
    
            foreach ($aComments as $iKey => $aComment) {
                if (Phpfox::isModule($aComment['type_id']) == false) {
                    unset($aComments[$iKey]);
                    continue;
                }
                $aComments[$iKey]['item_message'] = _p('user_link_left_a_comment_on_your_item', [
                    'user'      => $aComment,
                    'item_name' => Phpfox::callback($aComment['type_id'] . '.getCommentItemName'),
                    'link'      => Phpfox_Url::instance()->makeUrl('request.view.comment', ['id' => $aComment['comment_id']])
                ]);
            }
            $this->cache()->save($sCacheId, $aComments);
            Phpfox::getLib('cache')->group('comment', $sCacheId);
        }
        
        return $aComments;
	}
    
    /**
     * @param string $sType
     * @param int $iItem
     *
     * @return array
     */
	public function getForRss($sType, $iItem)
	{
        if (!Phpfox::isModule('rss')){
            return [];
        }
        $sCacheId = $this->cache()->set('comment_rss_' . $sType . '_' . $iItem);
        if (!$aRss = $this->cache()->get($sCacheId)) {
            
            (($sPlugin = Phpfox_Plugin::get('comment.service_comment_getforrss__start')) ? eval($sPlugin) : false);
    
            $oUrl = Phpfox_Url::instance();
    
            $aSql = [
                "AND cmt.type_id = '" . Phpfox_Database::instance()->escape($sType) . "'",
                'AND cmt.item_id = ' . $iItem,
                'AND cmt.view_id = 0'
            ];
    
            // Get the comments for this page
            list(, $aRows) = $this->get('cmt.*', $aSql, 'cmt.time_stamp DESC', 0, Phpfox::getParam('rss.total_rss_display'));
    
            $aItems = [];
            foreach ($aRows as $aRow) {
                $aItems[] = [
                    'title'       => _p('by_full_name', [
                        'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name'])
                    ]),
                    'link'        => $oUrl->makeUrl('comment.view', ['id' => $aRow['comment_id']]),
                    'description' => $aRow['text'],
                    'time_stamp'  => $aRow['unix_time_stamp'],
                    'creator'     => Phpfox::getLib('parse.output')->clean($aRow['full_name'])
                ];
            }
    
            $aRss = [
                'href'        => $oUrl->makeUrl('comment.rss', ['type' => $sType, 'item' => $iItem]),
                'title'       => (Phpfox::hasCallback($sType, 'getRssTitle') ? Phpfox::callback($sType . '.getRssTitle', $iItem) : _p('latest_comments')),
                'description' => _p('latest_comments_on_site_title', ['site_title' => Phpfox::getParam('core.site_title')]),
                'items'       => $aItems
            ];
    
            (($sPlugin = Phpfox_Plugin::get('comment.service_comment_getforrss__end')) ? eval($sPlugin) : false);
            
            $this->cache()->save($sCacheId, $aRss);
            Phpfox::getLib('cache')->group('comment', $sCacheId);
        }
		return $aRss;
	}
    
    /**
     * @return int
     */
	public function getSpamTotal()
	{
	    $sCacheId = $this->cache()->set('comment_spam_total');

        if (!$iTotalSpam = $this->cache()->get($sCacheId)) {
            $iTotalSpam = $this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('view_id = 9')
                ->execute('getSlaveField');
            
            $this->cache()->save($sCacheId, $iTotalSpam);
            Phpfox::getLib('cache')->group('comment', $sCacheId);
        }
        return $iTotalSpam;
	}
    
    /**
     * @param string $sType
     * @param int    $iItemId
     * @param int    $iLimit
     * @param null   $mPager
     * @param null   $iCommentId
     * @param string $sPrefix
     *
     * @return array
     */
	public function getCommentsForFeed($sType, $iItemId, $iLimit = 2, $mPager = null, $iCommentId = null, $sPrefix = '')
	{
		if (redis()->enabled()) {
			$comments = [];
			$iLimit--;
			$start = 0;
			$end = $iLimit;

			if ($mPager !== null && request()->get('page')) {
				$page = (int) request()->get('page');
				$page--;
				$start = ($page * setting('comment.comment_page_limit'));
				$end = ($start + setting('comment.comment_page_limit'));
			}

			$get_comment = function($comment, $comment_id) {
				$comment->is_liked = redis()->get('is/feed/liked/' . user()->id . '/feed_mini/' . $comment_id);
				$comment->total_like = redis()->get('total/feed/liked/feed_mini/' . $comment_id);
			};
			$rows = redis()->lrange('comments/' . $sType . '/' . $iItemId, $start, $end);
			foreach ($rows as $comment_id) {
				$comment = redis()->get('comment/' . $comment_id);

				$get_comment($comment, $comment_id);

				$comments[] = array_merge(redis()->user($comment->user_id), (array) $comment);
			}

			$comments = array_reverse($comments);

			return $comments;
		}

		if ($iCommentId === null) {
			if ($mPager !== null) {
				$this->database()->limit(Phpfox_Request::instance()->getInt('page'), $iLimit, $mPager);
			} else {
				$this->database()->limit($iLimit);
			}
		}
		
		if ($iCommentId !== null)
		{
			$this->database()->where('c.comment_id = ' . (int) $iCommentId . '');
		}
		else
		{
		    if ($sType == 'app')
            {
                $this->database()->where('c.parent_id = 0 AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int) $iItemId . ' AND c.view_id = 0 AND c.feed_table = "' . $sPrefix . 'feed' . '"');
            }
            else{
                $this->database()->where('c.parent_id = 0 AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int) $iItemId . ' AND c.view_id = 0');
            }

		}

		if(Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
		}
		if (Phpfox::getParam('comment.newest_comment_on_top')) {
		    Phpfox::getLib('database')->order('c.time_stamp ASC')	;
        } else {
            Phpfox::getLib('database')->order('c.time_stamp DESC')	;
        }
		$aFeedComments = $this->database()->select('c.*, ' . (Phpfox::getParam('core.allow_html') ? "ct.text_parsed" : "ct.text") .' AS text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->execute('getSlaveRows');

		$aComments = array();
		if (count($aFeedComments))
		{
			foreach ($aFeedComments as $iFeedCommentKey => $aFeedComment)
			{
				$aFeedComments[$iFeedCommentKey]['post_convert_time'] = Phpfox::getLib('date')->convertTime($aFeedComment['time_stamp'], 'comment.comment_time_stamp');
				
				if (Phpfox::getParam('comment.comment_is_threaded'))
				{
                    $aFeedComments[$iFeedCommentKey]['children'] = $aFeedComment['child_total']>0? $this->_getChildren($aFeedComment['comment_id'], $sType, $iItemId, $iCommentId):[];
				}
			}					
						
			$aComments = array_reverse($aFeedComments);			
		}	
		
		return $aComments;	
	}
    
    /**
     * @param int $iUserId owner (user_id) of the item to comment on (owner of the blog for example)
     * @param int $iPrivacy
     *
     * @return boolean
     */
	public function canPostComment($iUserId, $iPrivacy)
	{
        $bCanPostComment = true;
        if ($iUserId != Phpfox::getUserId() && !Phpfox::getUserParam('privacy.can_comment_on_all_items')) {
            $bIsFriend = (Phpfox::isModule('friend')) ? Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $iUserId) : false;
            
            switch ((int)$iPrivacy) {
                case 1:
                    if ($bIsFriend <= 0) {
                        $bCanPostComment = false;
                    }
                    break;
                case 2:
                    if ($bIsFriend > 0) {
                        $bCanPostComment = true;
                    } else {
                        if (Phpfox::isModule('friend') && !Phpfox::getService('friend')->isFriendOfFriend($iUserId)) {
                            $bCanPostComment = false;
                        }
                    }
                    break;
                case 3:
                    $bCanPostComment = false;
                    break;
            }
        }
		
		return $bCanPostComment;	
	}

    /**
     * This function use to send mails and notifications to users that commented on an item
     *
     * @param  string $sModule
     * @param  int $iItemId
     * @param  int $iOwnerUserId
     * @param  array $aMessage
     * @param null $iSenderUserId
     * @param array $aExcludeUsers
     *
     * @return mixed|null
     */
    public function massMail($sModule, $iItemId, $iOwnerUserId, $aMessage = array(), $iSenderUserId = null, $aExcludeUsers = [])
    {
        if (!is_array($aExcludeUsers)) {
            $aExcludeUsers = [];
        }
        if ($sPlugin = Phpfox_Plugin::get('comment.service_comment_massmail__0')) {
            eval($sPlugin);
            if (isset($aPluginReturn)) {
                return $aPluginReturn;
            }
        }

        $aRows = $this->database()->select('*')->from($this->_sTable)->where([
            'type_id' => $sModule,
            'item_id' => intval($iItemId),
            'view_id' => 0,
            'AND user_id != ' . $iOwnerUserId
        ])->group('user_id', true)->executeRows();

        if ($sPlugin = Phpfox_Plugin::get('comment.service_comment_massmail__1')) {
            eval($sPlugin);
        }

        foreach ($aRows as $aRow) {
            if (in_array($aRow['user_id'], $aExcludeUsers) || $aRow['user_id'] == $iSenderUserId) {
                continue;
            }

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject($aMessage['subject'])
                ->message($aMessage['message'])
                ->notification('comment.add_new_comment')
                ->send();

            Phpfox::getService('notification.process')->add('comment_' . $sModule, $iItemId, $aRow['user_id'],
                $iSenderUserId);
        }

        return null;
    }

    /**
     * @param string $sMethod
     * @param array  $aArguments
     *
     * @return mixed|null
     */
	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('comment.service_comment___call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
        return null;
	}
    
    /**
     * @param int    $iParentId
     * @param string $sType
     * @param int    $iItemId
     * @param null   $iCommentId
     * @param int    $iCnt
     *
     * @return array
     */
	private function _getChildren($iParentId, $sType, $iItemId, $iCommentId = null, $iCnt = 0)
	{
		$iTotalComments = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.parent_id = ' . (int) $iParentId . ' AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int) $iItemId . ' AND c.view_id = 0')
			->execute('getSlaveField');
		
		if(Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')
					->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());		
		}
		
		if ($iCommentId === null)
		{
			$this->database()->limit(Phpfox::getParam('comment.thread_comment_total_display'));
		}
		
		$aFeedComments = $this->database()->select('c.*, ' . (Phpfox::getParam('core.allow_html') ? "ct.text_parsed" : "ct.text") .' AS text, ' . Phpfox::getUserField())
			->from(Phpfox::getT('comment'), 'c')
			->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
			->where('c.parent_id = ' . (int) $iParentId . ' AND c.type_id = \'' . $this->database()->escape($sType) . '\' AND c.item_id = ' . (int) $iItemId . ' AND c.view_id = 0')
			->order('c.time_stamp ASC')
			->execute('getSlaveRows');
		
		$iCnt++;
		if (count($aFeedComments))
		{	
			foreach ($aFeedComments as $iFeedCommentKey => $aFeedComment)
			{
				$aFeedComments[$iFeedCommentKey]['iteration'] = $iCnt;
				
				$aFeedComments[$iFeedCommentKey]['post_convert_time'] = Phpfox::getLib('date')->convertTime($aFeedComment['time_stamp'], 'comment.comment_time_stamp');
				$aFeedComments[$iFeedCommentKey]['children'] = $this->_getChildren($aFeedComment['comment_id'], $sType, $iItemId, $iCommentId, $iCnt);			
			}						
		}
        
        return ['total'    => (int)($iTotalComments - Phpfox::getParam('comment.thread_comment_total_display')),
                'comments' => $aFeedComments
        ];
    }
    
    /**
     * @deprecated This function will be removed in 4.6.0
     * @param array $aItem
     *
     * @return array
     */
    public function getInfoForAction($aItem)
	{
	    $sCacheId = $this->cache()->set('comment_action_info');

        if (!$aRow = $this->cache()->get($sCacheId)) {
            $aRow = $this->database()->select('c.comment_id, ct.text as title, c.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('comment'), 'c')
                ->join(Phpfox::getT('comment_text'), 'ct', 'c.comment_id = ct.comment_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                ->where('c.comment_id = ' . (int)$aItem['item_id'])
                ->execute('getSlaveRow');
            $aRow['link'] = '';
            
            $this->cache()->save($sCacheId, $aRow);
            Phpfox::getLib('cache')->group('comment', $sCacheId);
        }
		return $aRow;
	}
}
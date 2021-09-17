<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Module_Feed
 */
class Feed_Service_Feed extends Phpfox_Service
{
    /**
     * @var array
     */
	private $_aViewMoreFeeds = [];

    /**
     * @var array
     */
	private $_aCallback = [];

    /**
     * @var string
     */
	private $_sLastDayInfo = '';

    /**
     * @var array
     */
    private $_aFeedTimeline = ['left' => [], 'right' => []];

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('feed');

		(($sPlugin = Phpfox_Plugin::get('feed.service_feed___construct')) ? eval($sPlugin) : false);
	}

    /**
     * @deprecated 4.7.0 Frank: cannot find usage of this function so I mark it as deprecated to remove later
     * @param int $iPageId
     *
     * @return int
     */
	public function getOldPost($iPageId)
	{
		$aOldFeed = $this->database()->select('*')
			->from(Phpfox::getT('pages_feed'))
			->where('parent_user_id	= ' . (int)$iPageId)
			->order('time_stamp ASC')
			->execute('getSlaveRow');

		return (isset($aOldFeed['time_stamp']) ? $aOldFeed['time_stamp'] : PHPFOX_TIME);
	}

    /**
     * @param int $iUserId
     * @param int $iLastTimeStamp
     *
     * @return array|mixed
     */
	public function getTimeLineYears($iUserId, $iLastTimeStamp)
	{
		static $aCachedYears = [];

		if (isset($aCachedYears[ $iUserId ])) {
			return $aCachedYears[ $iUserId ];
		}

		$sCacheId = $this->cache()->set(['timeline', $iUserId]);
		if (!($aNewYears = $this->cache()->get($sCacheId))) {
			$aYears = range(date('Y', PHPFOX_TIME), date('Y', $iLastTimeStamp));
			foreach ($aYears as $iYear) {
				$iStartYear = mktime(0, 0, 0, 1, 1, $iYear);
				$iEndYear = mktime(0, 0, 0, 12, 31, $iYear);

				$iCnt = $this->database()->select('COUNT(*)')
					->from(Phpfox::getT('feed'))
					->forceIndex('time_stamp')
					->where('user_id = ' . (int)$iUserId . ' AND feed_reference = 0 AND time_stamp > \'' . $iStartYear . '\' AND time_stamp <= \'' . $iEndYear . '\'')
					->execute('getSlaveField');

				if ($iCnt) {
					$aNewYears[] = $iYear;
				}
			}

			$this->cache()->save($sCacheId, $aNewYears);
		}

		if (!is_array($aNewYears)) {
			$aNewYears = [];
		}

		$iBirthYear = date('Y', $iLastTimeStamp);

		$sDobCacheId = $this->cache()->set(['udob', $iUserId]);

		if (!($iDOB = $this->cache()->get($sDobCacheId))) {
			$iDOB = $this->database()->select('dob_setting')->from(Phpfox::getT('user_field'))->where('user_id = ' . (int)$iUserId)->execute('getSlaveField');
			$this->cache()->save($sDobCacheId, $iDOB);
            Phpfox::getLib('cache')->group(  'user', $sCacheId);
		}

		if ($iDOB == 0) {
			$sPermission = Phpfox::getParam('user.default_privacy_brithdate');
			$bShowBirthYear = ($sPermission == 'full_birthday' || $sPermission == 'show_age');
		}

		if (!in_array($iBirthYear, $aNewYears) && ($iDOB == 2 || $iDOB == 4 || ($iDOB == 0 && isset($bShowBirthYear) && $bShowBirthYear))) {
			$aNewYears[] = $iBirthYear;
		}

		$aYears = [];
		foreach ($aNewYears as $iYear) {
			$aMonths = [];
			foreach (range(1, 12) as $iMonth) {
				if ($iYear == date('Y', PHPFOX_TIME) && $iMonth > date('n', PHPFOX_TIME)) {

				} elseif ($iYear == date('Y', $iLastTimeStamp) && $iMonth > date('n', $iLastTimeStamp)) {

				} else {
					$aMonths[] = [
						'id'     => $iMonth,
						'phrase' => Phpfox::getTime('F', mktime(0, 0, 0, $iMonth, 1, $iYear), false),
					];
				}
			}

			$aMonths = array_reverse($aMonths);

			$aYears[] = [
				'year'   => $iYear,
				'months' => $aMonths,
			];
		}

		$aCachedYears[ $iUserId ] = $aYears;

		return $aYears;
	}

    /**
     * @param string $sModule
     * @param int $iItemId
     *
     * @return array|bool
     */
	public function getForItem($sModule, $iItemId)
	{
		$aRow = $this->database()->select('*')
			->from(Phpfox::getT('feed'))
			->where('type_id = \'' . $this->database()->escape($sModule) . '\' AND item_id = ' . (int)$iItemId)
			->executeRow();

		if (isset($aRow['feed_id'])) {
			return $aRow;
		}

		return false;
	}

    /**
     * @param array $aCallback
     *
     * @return $this
     */
	public function callback($aCallback)
	{
		$this->_aCallback = $aCallback;
		return $this;
	}

    /**
     * @param string $sTable
     *
     * @return void
     */
	public function setTable($sTable)
	{
		$this->_sTable = $sTable;
	}

    /**
     * @var array
     */
	private $_params = [];

	/**
	 * @return bool
	 */
	public function isSearchHashtag()
	{
		$sSearch = Phpfox_Request::instance()->get('hashtagsearch');
		return ('hashtag' == Phpfox_Request::instance()->get('req1')) || !empty($sSearch);
	}

    /**
     * @return string
     */
	public function getSearchHashtag()
	{
		if (!$this->isSearchHashtag()) return '';
		$sRequest = (isset($_GET[ PHPFOX_GET_METHOD ]) ? $_GET[ PHPFOX_GET_METHOD ] : '');
		$sReq2 = '';
		if (!empty($sRequest)) {
			$aParts = explode('/', trim($sRequest, '/'));
			$iCnt = 0;
			// We have to count the "mobile" part as a req1
			// add one to the count
			$iCntTotal = 2;
			foreach ($aParts as $sPart) {
				$iCnt++;

				if ($iCnt === $iCntTotal) {
					$sReq2 = $sPart;
					break;
				}
			}
		}

		$sTag = (Phpfox_Request::instance()->get('hashtagsearch') ? Phpfox_Request::instance()->get('hashtagsearch') : urldecode($sReq2));
		return $sTag;
	}

    /**
     * @param null|int|array $iUserId
     * @param null|int $iFeedId
     * @param int $iPage
     * @param bool $bForceReturn
     * @param bool $bLimit
     * @param null|int $iLastFeedId
     *
     * @return array
     * @throws Exception
     */
	public function get($iUserId = null, $iFeedId = null, $iPage = 0, $bForceReturn = false, $bLimit = true, $iLastFeedId = null)
	{
		static $iLoopCount = 0;
		$params = [];
		if (is_array($iUserId)) {
			$params = $iUserId;
			$iUserId = null;
			if (isset($params['id'])) {
				$iFeedId = $params['id'];
			}

			if (isset($params['page'])) {
				$iPage = (int)$params['page'];
			}

			if (isset($params['user_id'])) {
				$iUserId = $params['user_id'];
			}
		}
		$this->_params = $params;
        $query_redis = function($r_key) use ($iPage) {
            $start = 0;
            $end = 9;
            if ($iPage) {
                $start = ($end * $iPage);
                $end = ($end * ($iPage + 1));
            }

            $aRows = [];
            $rows = redis()->lrange($r_key, $start, $end);
            foreach ($rows as $feed_id) {
                $feed = redis()->get_as_array('feed/' . $feed_id);
                if ($feed) {
                    $aRows[] = $feed;
                } else {
                    $this_feed = db()->select('f.*, ' . Phpfox::getUserField())
                        ->from(':feed', 'f')
                        ->join(':user', 'u', 'u.user_id = f.user_id')
                        ->where(['f.feed_id' => $feed_id])
                        ->executeRow();

                    $feed = $this->_processFeed($this_feed, 0, $this_feed['user_id'], true);
                    if ($feed) {
                        $aRows[] = $feed;
                    }
                }
            }
            return $aRows;
        };

		$oReq = Phpfox_Request::instance();
		$bIsCheckForUpdate = defined('PHPFOX_CHECK_FOR_UPDATE_FEED') ? 1 : 0;
		$iLastFeedUpdate = defined('PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE') ? PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE : 0;
		$iLastStoreUpdate = Phpfox::getCookie('feed-last-check-id');
		if ($iLastFeedUpdate && $bIsCheckForUpdate && ($iLastStoreUpdate > $iLastFeedUpdate)) {
			$iLastFeedUpdate = $iLastStoreUpdate;
		}

		if ($iLastFeedUpdate != $iLastStoreUpdate) {
			Phpfox::removeCookie('feed-last-check-id');
			Phpfox::setCookie('feed-last-check-id', $iLastFeedUpdate);
		}

		if  (!isset($params['bIsChildren']) || !$params['bIsChildren'])
		{
			if (($iCommentId = $oReq->getInt('comment-id'))) {
				if (isset($this->_aCallback['feed_comment'])) {
					$aCustomCondition = ['feed.type_id = \'' . $this->_aCallback['feed_comment'] . '\' AND feed.item_id = ' . (int)$iCommentId . ' AND feed.parent_user_id = ' . (int)$this->_aCallback['item_id']];
				} else {
					$aCustomCondition = ['feed.type_id IN(\'feed_comment\', \'feed_egift\') AND feed.item_id = ' . (int)$iCommentId . ' AND feed.parent_user_id = ' . (int)$iUserId];
				}

				$iFeedId = true;
			} elseif (($iStatusId = $oReq->getInt('status-id'))) {
				$aCustomCondition = ['feed.type_id = \'user_status\' AND feed.item_id = ' . (int)$iStatusId . ' AND feed.user_id = ' . (int)$iUserId];
				$iFeedId = true;
			} elseif (($iLinkId = $oReq->getInt('link-id'))) {
				$aCustomCondition = ['feed.type_id = \'link\' AND feed.item_id = ' . (int)$iLinkId . ' AND feed.user_id = ' . (int)$iUserId];
				$iFeedId = true;
			} elseif (($iLinkId = $oReq->getInt('plink-id'))) {
				$aCustomCondition = ['feed.type_id = \'link\' AND feed.item_id = ' . (int)$iLinkId . ' AND feed.parent_user_id  = ' . (int)$iUserId];
				$iFeedId = true;
			} elseif (($iPokeId = $oReq->getInt('poke-id'))) {
				$aCustomCondition = ['feed.type_id = \'poke\' AND feed.item_id = ' . (int)$iPokeId . ' AND feed.user_id = ' . (int)$iUserId];
				$iFeedId = true;
			}
		}

		$iTotalFeeds = (int)Phpfox::getComponentSetting(($iUserId === null ? Phpfox::getUserId() : $iUserId), 'feed.feed_display_limit_' . ($iUserId !== null ? 'profile' : 'dashboard'), Phpfox::getParam('feed.feed_display_limit'));
		if (isset($params['limit'])) {
			$iTotalFeeds = $params['limit'];
		}
		if (!$bLimit || (defined('FEED_LOAD_NEW_NEWS') && FEED_LOAD_NEW_NEWS)) {
			$iTotalFeeds = 101;
		}
		$sLoadMoreCond = null;
		$iOffset = (($iPage * $iTotalFeeds));
		if ($iOffset == '-1') {
			$iOffset = 0;
		}
		if ($iLastFeedId != null) {
			$aLastFeed = $this->getFeed($iLastFeedId);
			if (!empty($aLastFeed['time_update'])) {
				$iOffset = 0;
				$sLoadMoreCond = 'AND feed.time_update < ' . (int)$aLastFeed['time_update'];
			}
		}
		elseif(isset($params['order']) && $params['order'] == 'feed.total_view DESC' && isset($params['v_page'])) {
		    $iOffset = (int)($params['v_page'] * $iTotalFeeds);
        }
		elseif (isset($params['last-item']) && $params['last-item']){
            $sLoadMoreCond = ' AND feed.feed_id < ' . (int) $params['last-item'];
        }
		$extra = '';

        if (Phpfox::isUser()) {
            $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $extra .= ' AND feed.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }
        }

		if ($sLoadMoreCond != null) {
			$extra .= ' ' . $sLoadMoreCond;
		}
		(($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_start')) ? eval($sPlugin) : false);

		if (isset($params['type_id'])) {
			$extra .= ' AND feed.type_id ' . (is_array($params['type_id']) ? 'IN(' . implode(',', array_map(function ($value){
						return "'{$value}'";
					}, $params['type_id'])) . ')' : '= \'' . $params['type_id'] . '\'') . '';
		}
		//Do not hide feed when login as pages
        if (!Phpfox::getUserBy('profile_page_id') && defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE) {
            //Hide feed add on other user wall
            if (isset($iUserId)) {
                $extra .= ' AND (feed.parent_user_id=0 OR feed.parent_user_id = ' . (int)$iUserId . ')';
            }
        }

		$sOrder = 'feed.time_update DESC';
		if ((Phpfox::getParam('feed.allow_choose_sort_on_feeds') && Phpfox::getUserBy('feed_sort')) || defined('PHPFOX_IS_USER_PROFILE')) {
			$sOrder = 'feed.time_stamp DESC';
		}

		if (isset($this->_params['order'])) {
			$sOrder = $this->_params['order'];
		}

		$aCond = [];
		// Users must be active within 7 days or we skip their activity feed
		$iLastActiveTimeStamp = (((int)Phpfox::getParam('feed.feed_limit_days') <= 0 || !empty($this->_params['ignore_limit_feed'])) ? 0 : (PHPFOX_TIME - (86400 * Phpfox::getParam('feed.feed_limit_days'))));
		$is_app = false;
		if (isset($params['type_id']) && (new Core\App())->exists($params['type_id'])) {
			$is_app = true;
		}
		if (isset($this->_aCallback['module'])) {
			$aNewCond = [];
			if (($iCommentId = $oReq->getInt('comment-id'))) {
				if (!isset($this->_aCallback['feed_comment'])) {
					$aCustomCondition = ['feed.type_id = \'' . $this->_aCallback['module'] . '_comment\' AND feed.item_id = ' . (int)$iCommentId . ''];
				}
			}
			$aNewCond[] = 'AND feed.parent_user_id = ' . (int)$this->_aCallback['item_id'];
			if ($iUserId !== null && $iFeedId !== null) {
				$aNewCond[] = 'AND feed.feed_id = ' . (int)$iFeedId . ' AND feed.user_id = ' . (int)$iUserId;
			}

			if ($iUserId === null && $iFeedId !== null) {
				$aNewCond = [];
				$aNewCond[] = 'AND feed.feed_id = ' . (int)$iFeedId;
			}

            if (Phpfox::isUser()) {
                $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
                if (!empty($aBlockedUserIds)) {
                    $aNewCond[] = 'AND feed.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
                    if (!empty($aCustomCondition))
                    {
                        $aCustomCondition[] = 'AND feed.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
                    }
                }
            }

			if ($iFeedId === null && is_string($extra) && !empty($extra)) {
				$aNewCond[] = $extra;
			}

			if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                $aNewCond[] = 'AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
			}

            if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                $aNewCond[] = 'AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
            }

            if($is_app && isset($this->_params['when']) && $this->_params['when'])
            {
                $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                switch ($params['when'])
                {
                    case 'today':
                        $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                        $aNewCond[] = ' AND (' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND ' . 'feed.time_stamp' . ' < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')';
                        break;
                    case 'this-week':
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' >= ' . (int) Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart());
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' <= ' . (int) Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd());
                        break;
                    case 'this-month':
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'';
                        $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'), Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'';
                        break;
                    default:
                        break;
                }
            }

			$aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField() . ', u.view_id')
				->from(Phpfox::getT((isset($this->_aCallback['table_prefix']) ? $this->_aCallback['table_prefix'] : ''). 'feed'), 'feed')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->where((isset($aCustomCondition) ? $aCustomCondition : $aNewCond))
				->order($sOrder)
				->limit($iOffset, $iTotalFeeds, null, false, true)
				->execute('getSlaveRows');

			// Fixes missing page_user_id, required to create the proper feed target
			if ($this->_aCallback['module'] == 'pages') {
				foreach ($aRows as $iKey => $aValue) {
					$aRows[ $iKey ]['page_user_id'] = $iUserId;
				}
			}
		} // check feed id in exists list.
		elseif ($iUserId === null && $iFeedId === null && ($sIds = $oReq->get('ids'))) {

			$aParts = explode(',', $oReq->get('ids'));
			$sNewIds = '';
			foreach ($aParts as $sPart) {
				$sNewIds .= (int)$sPart . ',';
			}
			$sNewIds = rtrim($sNewIds, ',');

			$aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField() . ', u.view_id')
				->from($this->_sTable, 'feed')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->where('feed.feed_id IN(' . $sNewIds . ')')
				->order('feed.time_stamp DESC')
				->execute('getSlaveRows');
		} // get particular feed by id
		elseif ($iUserId === null && $iFeedId !== null) {
			if (isset($this->_aCallback['module'])) {
				$this->_sTable = Phpfox::getT($this->_aCallback['table_prefix'] . 'feed');
			}

			$aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField() . ', u.view_id')
				->from($this->_sTable, 'feed')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->where('feed.feed_id = ' . (int)$iFeedId)
				->order('feed.time_stamp DESC')
				->execute('getSlaveRows');
		} // get particular feed by id
		elseif ($iUserId !== null && $iFeedId !== null) {
			$aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField() . ', u.view_id')
				->from($this->_sTable, 'feed')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->where((isset($aCustomCondition) ? $aCustomCondition : 'feed.feed_id = ' . (int)$iFeedId . ' AND feed.user_id = ' . (int)$iUserId . ''))
				->order('feed.time_stamp DESC')
				->limit(1)
				->execute('getSlaveRows');
		} // get feed on particular profile, does not need to improve.
		elseif ($iUserId !== null) {
            $sOrder = 'feed.time_stamp desc';

			if ($iUserId == Phpfox::getUserId()) {
				$aCond[] = 'AND feed.privacy IN(0,1,2,3,4)';
			} else {
                $oUserObject = Phpfox::getService('user')->getUserObject($iUserId);
				if (isset($oUserObject->is_friend) && $oUserObject->is_friend) {
					$aCond[] = 'AND feed.privacy IN(0,1,2)';
				} else if (isset($oUserObject->is_friend_of_friend) && $oUserObject->is_friend_of_friend) {
					$aCond[] = 'AND feed.privacy IN(0,2)';
				} else {
					$aCond[] = 'AND feed.privacy IN(0)';
				}
			}

			$aCond[] = $extra;

            if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                $aCond[] = 'AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
            }

            if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                $aCond[] = 'AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
            }

			if (!$this->_params) {
                $more = '';
                if ($iLastFeedId != null) {
                    $aLastFeed = $this->getFeed($iLastFeedId);
                    if (!empty($aLastFeed['time_update'])) {
                        $more = ' AND feed.time_update < ' . (int)$aLastFeed['time_update'];
                    }
                }
				// There is no reciprocal feed when you add someone as friend
                if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                    $this->database()->join(Phpfox::getT('feed'), 'feed_search', 'feed_search.feed_id = feed.feed_id AND feed_search.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'');
                }
				$this->database()->select('DISTINCT feed.*')
					->from($this->_sTable, 'feed')
					->where('feed.type_id = \'friend\' AND feed.user_id = ' . (int)$iUserId . $more)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
					->union();
			}

			(($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_userprofile')) ? eval($sPlugin) : '');

			$this->database()->select('DISTINCT feed.*')
				->from($this->_sTable, 'feed')
				->where(array_merge($aCond, ['AND type_id = \'feed_comment\' AND feed.user_id = ' . (int)$iUserId . '']))
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
				->union();

			$this->database()->select('DISTINCT feed.*')
				->from($this->_sTable, 'feed')
				->where(array_merge($aCond, ['AND feed.user_id = ' . (int)$iUserId . ' AND feed.feed_reference = 0 AND feed.parent_user_id = 0']))
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
				->union();

			if (Phpfox::isUser()) {
				if (Phpfox::isModule('privacy')) {
					$this->database()->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
						->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '');
				}

				$this->database()->select('DISTINCT feed.*')
					->from($this->_sTable, 'feed')
					->where(array_merge($aCond, ['AND feed.privacy IN(4) AND feed.user_id = ' . (int)$iUserId . ' AND feed.feed_reference = 0']))
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
					->union();
			}

			$this->database()->select('DISTINCT feed.*')
				->from($this->_sTable, 'feed')
				->where(array_merge($aCond, ['AND feed.parent_user_id = ' . (int)$iUserId]))
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
				->union();
			$aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField())
				->unionFrom('feed')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->order('feed.time_stamp DESC')
				->limit(0, $iTotalFeeds, null, false, true)
				->execute('getSlaveRows');
		} elseif (
			// get main feed on "feed_only_friends" ON.
			// case 01.
			((Phpfox::getParam('feed.feed_only_friends') && !$is_app)
				|| Phpfox::getParam('core.friends_only_community')
				|| isset($this->_params['friends']))
			&& !$this->isSearchHashtag()
		) {
            $r_key = (auth()->isLoggedIn() ? 'feed_stream_' . user()->id : null);
            $do_redis = (redis()->enabled() && $r_key && empty($this->_params['type_id']));
            if ($do_redis && redis()->exists($r_key)) {

                $aRows = $query_redis($r_key);

            } else {

                if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                    $extra .= ' AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
                }

                if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                    $extra .= ' AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
                }

                if (Phpfox::isModule('friend')) {

					if($sOrder == 'feed.time_update DESC')
						$this->database()->forceIndex('time_update');

                    // Get my friends feeds
                    if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                        call_user_func($this->_params['join_query']);
                    }
                    $this->database()->select('DISTINCT feed.*')
                        ->from($this->_sTable, 'feed')
                        ->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                        ->where('feed.privacy IN(0,1,2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0')
                        ->order($sOrder)
                        ->limit($iOffset, $iTotalFeeds, null, false, true)
                        ->union();
                }

                // Get my feeds
                if (!isset($this->_params['friends'])) {
                    if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                        call_user_func($this->_params['join_query']);
                    }
                    $this->database()->select('DISTINCT feed.*')
                        ->from($this->_sTable, 'feed')
						->forceIndex('user_id')
                        ->where('feed.privacy IN(0,1,2,3,4) ' . $extra . ' AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0')
                        ->order($sOrder)
                        ->limit($iOffset, $iTotalFeeds, null, false, true)
                        ->union();
                }

                $sSelect = 'feed.*, u.view_id,  ' . Phpfox::getUserField();
                if (Phpfox::isModule('friend')) {
                    $sSelect .= ', f.friend_id AS is_friend';
                    $this->database()->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                        ->limit($iOffset, $iTotalFeeds, null, false, true)
                        ->order($sOrder);
                }

                $sWhere = '';
                if ($bIsCheckForUpdate) {
                    $sWhere .= ' feed.time_update > ' . intval($iLastFeedUpdate);
                }

                $aRows = $this->database()->select($sSelect)
                    ->unionFrom('feed')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                    ->where($sWhere)
                    ->order($sOrder)
                    ->limit(0, $iTotalFeeds)
                    ->execute('getSlaveRows');

                if ($do_redis) {
                    foreach ($aRows as $row) {
                        redis()->rpush($r_key, $row['feed_id']);
                    }
                    redis()->ltrim($r_key, 0, 49);
                }
            }
		} elseif (!$this->isSearchHashtag()) {

            $r_key = 'public_feeds';
            $do_redis = (redis()->enabled() && !isset($this->_params['type_id']));
            if ($do_redis && redis()->exists($r_key)) {

                $aRows = $query_redis($r_key);

            } else {
                // no search
                if ($bIsCheckForUpdate) {
                    $sMoreWhere = ' AND feed.time_update > ' . intval($iLastFeedUpdate);
                } else {
                    $sMoreWhere = '';
                }
                (($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_buildquery')) ? eval($sPlugin) : '');

                if (isset($this->_params['search']) && !empty($this->_params['search']) && is_scalar($this->_params['search'])) {
                    $extra .= ' AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
                }

                if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                    $extra .= ' AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
                }

                if($is_app && isset($this->_params['when']) && $this->_params['when'])
                {
                    $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                    switch ($params['when'])
                    {
                        case 'today':
                            $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                            $extra .= ' AND (' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND ' . 'feed.time_stamp' . ' < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')';
                            break;
                        case 'this-week':
                            $extra .= ' AND ' . 'feed.time_stamp' . ' >= ' . (int) Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart());
                            $extra .= ' AND ' . 'feed.time_stamp' . ' <= ' . (int) Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd());
                            break;
                        case 'this-month':
                            $extra .= ' AND ' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'';
                            $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'), Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                            $extra .= ' AND ' . 'feed.time_stamp' . ' <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'';
                            break;
                        default:
                            break;
                    }
                }

                if (Phpfox::isModule('friend')) {
                    // Get my friends feeds
                    if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                        call_user_func($this->_params['join_query']);
                    }
                    $this->database()->select('DISTINCT feed.*')
                        ->from($this->_sTable, 'feed')
                        ->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                        ->where('feed.privacy IN(1,2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                        ->order($sOrder)
                        ->limit($iOffset, $iTotalFeeds, null, false, true)
                        ->group('feed.feed_id')
                        ->union();

                    // Get my friends of friends feeds
                    if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                        call_user_func($this->_params['join_query']);
                    }
                    $this->database()->select('DISTINCT feed.*')
                        ->from($this->_sTable, 'feed')
                        ->join(Phpfox::getT('friend'), 'f1', 'f1.user_id = feed.user_id')
                        ->join(Phpfox::getT('friend'), 'f2', 'f2.user_id = ' . Phpfox::getUserId() . ' AND f2.friend_user_id = f1.friend_user_id')
                        ->where('feed.privacy IN(2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                        ->group('feed.feed_id')
                        ->order($sOrder)
                        ->limit($iOffset, $iTotalFeeds, null, false, true)
                        ->union();
                }

                // Get my feeds
                if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                    call_user_func($this->_params['join_query']);
                }
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->where('feed.privacy IN(1,2,3,4) ' . $extra . ' AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();

                // Get public feeds
                if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                    call_user_func($this->_params['join_query']);
                }
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->where('feed.privacy IN(0) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();

                if (Phpfox::isModule('privacy')) {
                    $this->database()->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
                        ->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '');

                }
                // Get feeds based on custom friends lists
                if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                    call_user_func($this->_params['join_query']);
                }
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->where('feed.privacy IN(4) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0 ' . $sMoreWhere)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();

                $sSelect = 'feed.*, u.view_id,  ' . Phpfox::getUserField();
                if (Phpfox::isModule('friend')) {
                    $sSelect .= ', f.friend_id AS is_friend';
                    $this->database()->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId());
                }

                $sWhere = '';
                if ($bIsCheckForUpdate) {
                    $sWhere .= ' feed.time_update > ' . intval($iLastFeedUpdate);
                }

                $aRows = $this->database()->select($sSelect)
                    ->unionFrom('feed')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                    ->where($sWhere)
                    ->order($sOrder)
                    ->limit(0, $iTotalFeeds)
                    ->execute('getSlaveRows');

                if ($do_redis) {
                    foreach ($aRows as $row) {
                        redis()->rpush($r_key, $row['feed_id']);
                    }
                    redis()->ltrim($r_key, 0, 199);
                }
            }

		} else {
			// Search hashtag
			$sOrder = 'feed.time_update DESC';

			$sTag = $this->getSearchHashtag();
			$sTag = \Phpfox_Parse_Output::instance()->parse($sTag);
			//https://github.com/moxi9/phpfox/issues/595
			$sTag = Phpfox::getLib('parse.input')->clean($sTag, 255);
			$sTag = mb_convert_case($sTag, MB_CASE_LOWER, "UTF-8");
			$sTag = Phpfox_Database::instance()->escape($sTag);

			if ($bIsCheckForUpdate) {
				$sMoreWhere = ' AND feed.time_update > ' . intval($iLastFeedUpdate);
			} else {
				$sMoreWhere = '';
			}
			$sMyFeeds = '0,1,2,3,4';

			(($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_buildquery')) ? eval($sPlugin) : '');

			if (Phpfox::isModule('friend')) {
				// Get my friends feeds
				$this->database()->select('DISTINCT feed.*')
					->from($this->_sTable, 'feed')
					->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
					->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND tag_type = 1 AND (tag_text = \'' .$sTag . '\' OR tag_url = \'' .$sTag  . '\')')
					->where('feed.privacy IN(1,2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
					->order($sOrder)
					->limit($iOffset, $iTotalFeeds, null, false, true)
					->union();

				// Get my friends of friends feeds
				$this->database()->select('DISTINCT feed.*')
					->from($this->_sTable, 'feed')
					->join(Phpfox::getT('friend'), 'f1', 'f1.user_id = feed.user_id')
					->join(Phpfox::getT('friend'), 'f2', 'f2.user_id = ' . Phpfox::getUserId() . ' AND f2.friend_user_id = f1.friend_user_id')
					->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND tag_type = 1 AND (tag_text = \'' .$sTag . '\' OR tag_url = \'' .$sTag  . '\')')
					->where('feed.privacy IN(2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
					->order($sOrder)
					->limit($iOffset, $iTotalFeeds, null, false, true)
					->union();
			}

			// Get my feeds
			$this->database()->select('DISTINCT feed.*')
				->from($this->_sTable, 'feed')
				->where('feed.privacy IN(' . $sMyFeeds . ') ' . $extra . ' AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
				->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND tag_type = 1 AND (tag_text = \'' .$sTag . '\' OR tag_url = \'' .$sTag  . '\')')
                ->limit($iOffset, $iTotalFeeds, null, false, true)
				->union();

			// Get public feeds
			$this->database()->select('DISTINCT feed.*')
				->from($this->_sTable, 'feed')
				->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND tag_type = 1 AND (tag_text = \'' .$sTag . '\' OR tag_url = \'' .$sTag  . '\')')
				->where('feed.privacy IN(0) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
				->order($sOrder)
				->limit($iOffset, $iTotalFeeds, null, false, true)
				->union();

			if (Phpfox::isModule('privacy')) {
				$this->database()->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
					->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '');

			}
			// Get feeds based on custom friends lists
			$this->database()->select('DISTINCT feed.*')
				->from($this->_sTable, 'feed')
				->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND tag_type = 1 AND (tag_text = \'' .$sTag . '\' OR tag_url = \'' .$sTag  . '\')')
				->where('feed.privacy IN(4) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
				->order($sOrder)
				->limit($iOffset, $iTotalFeeds, null, false, true)
				->union();

			$sSelect = 'feed.*, u.view_id,  ' . Phpfox::getUserField();
			if (Phpfox::isModule('friend')) {
				$sSelect .= ', f.friend_id AS is_friend';
				$this->database()->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId());
			}

			$aRows = $this->database()->select($sSelect)
				->unionFrom('feed')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
				->order($sOrder)
				->limit(0, $iTotalFeeds)
				->execute('getSlaveRows');
		}

		if ($bForceReturn === true) {
			return $aRows;
		}

		$bFirstCheckOnComments = false;
		if (Phpfox::getParam('feed.allow_comments_on_feeds') && Phpfox::isUser() && Phpfox::isModule('comment')) {
			$bFirstCheckOnComments = true;
		}

		$aFeeds = [];
		$aParentFeeds = [];
		foreach ($aRows as $sKey => $aRow) {
			if ($iLastFeedId) $iLastFeedId = $aRow['feed_id'];
			if ($aRow['parent_module_id'] && !Phpfox::hasCallback($aRow['parent_module_id'], 'getActivityFeed')) continue;
			$aRow['feed_time_stamp'] = $aRow['time_stamp'];

			if (($aReturn = $this->_processFeed($aRow, $sKey, $iUserId, $bFirstCheckOnComments))) {
				if (isset($aReturn['force_user'])) {
					$aReturn['user_name'] = $aReturn['force_user']['user_name'];
					$aReturn['full_name'] = $aReturn['force_user']['full_name'];
					$aReturn['user_image'] = $aReturn['force_user']['user_image'];
					$aReturn['server_id'] = $aReturn['force_user']['server_id'];
				}

				$aReturn['feed_month_year'] = date('m_Y', $aRow['feed_time_stamp']);
				$aReturn['feed_time_stamp'] = $aRow['feed_time_stamp'];

				/* Lets figure out the phrases for like.display right here */
				if (Phpfox::isModule('like')) {
					$this->getPhraseForLikes($aReturn);
				}
				$aFeeds[] = $aReturn;
			}

			// Show the feed properly. If user A posted on page 1, then feed will say "user A > page 1 posted ..."
            $aCustomModule = [
                'pages',
                'groups',
            ];
            (($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_custom_module')) ? eval($sPlugin) : false);

            if (isset($this->_aCallback['module']) && in_array($this->_aCallback['module'], $aCustomModule)) {
				// If defined parent user, and the parent user is not the same page (logged in as a page)
				if (isset($aRow['page_user_id']) && $aReturn['page_user_id'] != $aReturn['user_id']) {
					$aParentFeeds[ $aReturn['feed_id'] ] = $aRow['page_user_id'];
				}
			} elseif (isset($this->_aCallback['module']) && $this->_aCallback['module'] == 'event') {
				// Keep it empty
				$aParentFeeds = [];
			} elseif (isset($aRow['parent_user_id']) && !isset($aRow['parent_user']) && $aRow['type_id'] != 'friend') {
				if (!empty($aRow['parent_user_id'])) {
					$aParentFeeds[ $aRow['feed_id'] ] = $aRow['parent_user_id'];
				}
			}
		}

		if ($iLoopCount <= 50 && empty($aFeeds) && (count($aRows) == $iTotalFeeds)) {
			$iLoopCount++;

			return $this->get($iUserId, $iFeedId, ++$iPage, $bForceReturn, $bLimit, $iLastFeedId);
		}

		// Get the parents for the feeds so it displays arrow.png
		if (!empty($aParentFeeds)) {
			$search = implode(',', array_values($aParentFeeds));
			if (!empty($search)) {
				$aParentUsers = $this->database()->select(Phpfox::getUserField())
					->from(Phpfox::getT('user'), 'u')
					->where('user_id IN (' . $search . ')')
					->execute('getSlaveRows');

				$aFeedsWithParents = array_keys($aParentFeeds);
				foreach ($aFeeds as $sKey => $aRow) {
					if (in_array($aRow['feed_id'], $aFeedsWithParents) && $aRow['type_id'] != 'photo_tag') {
						foreach ($aParentUsers as $aUser) {
							if ($aUser['user_id'] == $aRow['parent_user_id']) {
								$aTempUser = [];
								foreach ($aUser as $sField => $sVal) {
									$aTempUser[ 'parent_' . $sField ] = $sVal;
								}
								$aFeeds[ $sKey ]['parent_user'] = $aTempUser;
							}
						}
                        // get tagged users
                        $aFeeds[$sKey]['friends_tagged'] = $this->_getTaggedFriends($aRow);
					}
				}
			}
		}

		$oReq = Phpfox_Request::instance();
		if (($oReq->getInt('status-id')
				|| $oReq->getInt('comment-id')
				|| $oReq->getInt('link-id')
				|| $oReq->getInt('poke-id')
			)
			&& isset($aFeeds[0])
		) {
			$aFeeds[0]['feed_view_comment'] = true;
		}
		return $aFeeds;
	}

    /**
     * Get tagged friends (tagged via `with` field) from a feed_comment
     *
     * @param $aFeed
     * @return array
     */
	private function _getTaggedFriends($aFeed)
    {
        if ((empty($aFeed['feed_reference']) || $aFeed['type_id'] != 'feed_comment') && $aFeed['type_id'] != 'feed_egift') {
            return [];
        }
        //We save item_id in feed_tag_data
        $aUserIds = db()->select('user_id')->from(':feed_tag_data')->where([
            'item_id' => $aFeed['item_id'],
            'type_id' => 'feed_comment'
        ])->executeRows();
        $aUsers = [];
        foreach (array_column($aUserIds, 'user_id') as $iId) {
            $aUsers[] = Phpfox::getService('user')->getUser($iId);
        }

        return $aUsers;
    }

    /**
     * @return void
     */
	public function _hashSearch()
	{
		if (Phpfox_Request::instance()->get('req1') != 'hashtag' && Phpfox_Request::instance()->get('hashtagsearch') == '') {
			if (isset($this->_params['search'])) {
				$this->database()->join(Phpfox::getT('feed'), 'feed_search', 'feed_search.feed_id = feed.feed_id AND feed_search.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'');
			}

			return;
		}


		$sRequest = (isset($_GET[ PHPFOX_GET_METHOD ]) ? $_GET[ PHPFOX_GET_METHOD ] : '');
		$sReq2 = '';
		if (!empty($sRequest)) {
			$aParts = explode('/', trim($sRequest, '/'));
			$iCnt = 0;
			// We have to count the "mobile" part as a req1
			// add one to the count
			$iCntTotal = 2;
			foreach ($aParts as $sPart) {
				$iCnt++;

				if ($iCnt === $iCntTotal) {
					$sReq2 = $sPart;
					break;
				}
			}
		}

		$sTag = (Phpfox_Request::instance()->get('hashtagsearch') ? Phpfox_Request::instance()->get('hashtagsearch') : $sReq2);
		$sTag = \Phpfox_Parse_Output::instance()->parse($sTag);
		//https://github.com/moxi9/phpfox/issues/595
		$sTag = urldecode($sTag);
		if (empty($sTag)) {
			return;
		}

		$sTag = Phpfox::getLib('parse.input')->clean($sTag, 255);
		$sTag = mb_convert_case($sTag, MB_CASE_LOWER, "UTF-8");

		$this->database()->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id = feed.item_id AND hashtag.category_id = feed.type_id AND tag_type = 1 AND (tag_text = \'' . Phpfox_Database::instance()->escape($sTag) . '\' OR tag_url = \'' . Phpfox_Database::instance()->escape($sTag) . '\')');
	}

    /**
     * @param array $aFeed
     * @param bool  $bForce
     *
     * @return string
     */
	public function getPhraseForLikes(&$aFeed, $bForce = false)
	{
		if (redis()->enabled()) {
			$aFeed['feed_is_liked'] = (redis()->get('is/feed/liked/' . user()->id . '/' . $aFeed['type_id'] . '/' . $aFeed['item_id']) ? true : false);
		}

		$sOriginalIsLiked = ((isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked']) ? $aFeed['feed_is_liked'] : '');

		if (!isset($aFeed['feed_total_like'])) {
			$aFeed['feed_total_like'] = isset($aFeed['likes']) ? count($aFeed['likes']) : 0;
		}

		if (!isset($aFeed['like_type_id'])) {
			$aFeed['like_type_id'] = isset($aFeed['type_id']) ? $aFeed['type_id'] : null;
		}

		$sPhrase = '<span class="people-liked-feed">';
		$oParse = Phpfox::getLib('phpfox.parse.output');
		if (Phpfox::isModule('like')) {
			$oLike = Phpfox::getService('like');
		}
		$oUrl = Phpfox_Url::instance();

		if ((!isset($aFeed['likes']) && isset($oLike)) || (isset($oLike) && count($aFeed['likes']) > Phpfox::getParam('feed.total_likes_to_display'))) {
			$aFeed['likes'] = $oLike->getLikesForFeed($aFeed['type_id'], $aFeed['item_id'], false, Phpfox::getParam('feed.total_likes_to_display'), false, (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
			$aFeed['total_likes'] = count($aFeed['likes']);
		}

		$bDidILikeIt = false;
		/* Check to see if I liked this */
		if (Phpfox::getParam('feed.cache_each_feed_entry')) {
			$aFeed['feed_is_liked'] = false;
		} else {
			if (!isset($aFeed['feed_is_liked'])) {
				if (Phpfox::isModule('like')) {
					$aFeed['feed_is_liked'] = Phpfox::getService('like')->didILike($aFeed['type_id'], $aFeed['item_id'], [],(isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
				}
			}
		}

		$iCountLikes = (isset($aFeed['likes']) && !empty($aFeed['likes'])) ? count($aFeed['likes']) : 0;

		if ($aFeed['feed_total_like'] < count($aFeed['likes'])) {
			$aFeed['feed_total_like'] = count($aFeed['likes']);
		}

		$iPhraseLimiter = Phpfox::getParam('feed.total_likes_to_display');
		if (isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked']) {
			if ($iPhraseLimiter == 1 || $iPhraseLimiter == 2) {
				if ($aFeed['feed_total_like'] == 2) {
					$sPhrase .= _p('you_and') . '&nbsp;';
				} else {
					$sPhrase .= _p('you');
				}
			} else if ($aFeed['feed_total_like'] == 1) {
				$sPhrase .= _p('you');
			} else if ($aFeed['feed_total_like'] == 2) {
				$sPhrase .= _p('you_and') . '&nbsp;';
			} else if ($iPhraseLimiter > 2) {
				$sPhrase .= _p('you_comma') . '&nbsp;';
			}
			$bDidILikeIt = true;
		} else {
			if (Phpfox::isModule('like')) {
				$sPhrase = '';
			}
		}

		if (isset($aFeed['likes']) && is_array($aFeed['likes']) && $iCountLikes > 0) {
			$iIteration = ($bDidILikeIt && ($iPhraseLimiter < $aFeed['feed_total_like']) ? 1 : 0);
			$aLikes = [];
			foreach ($aFeed['likes'] as $aLike) {
				if ($iIteration >= $iCountLikes) {
					break;
				} else {
					if ($aLike['user_id'] == Phpfox::getUserId() && !Phpfox::getParam('feed.cache_each_feed_entry')) {
						continue;
					}
                    if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aLike['user_id']))
                    {
                        $sUserLink = '<span class="user_profile_link_span" id="js_user_name_link_' . $aLike['user_name'] . '">' . $oParse->shorten($aLike['full_name'], 20) . '</span>';
                    }
                    else
                    {
                        $sUserLink = '<span class="user_profile_link_span" id="js_user_name_link_' . $aLike['user_name'] . '"><a href="' . $oUrl->makeUrl($aLike['user_name']) . '">' . $oParse->shorten($aLike['full_name'], 20) . '</a></span>';
                    }
					$aLikes[] = $sUserLink;
					$iIteration++;
				}
			}

			$sTempUser = array_pop($aLikes);
			$sImplode = implode(', ', $aLikes);
			$sPhrase .= $sImplode . ' ';

			if (isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked'] && $iPhraseLimiter >= 2 && $aFeed['feed_total_like'] > $iPhraseLimiter) {
				$sPhrase = trim($sPhrase) . ', ' /*. _p('and') . ' '*/
				;
			} else if (isset($aFeed['feed_total_like']) && ($aFeed['feed_total_like'] > Phpfox::getParam('feed.total_likes_to_display')) && Phpfox::getParam('feed.total_likes_to_display') != 1) {
				$sPhrase = trim($sPhrase) . ', ';
			} else if (count($aLikes) > 0) {
				$sPhrase .= _p('and') . ' ';
			} else {
				$sPhrase = trim($sPhrase);
			}
			$sPhrase .= $sTempUser;

		}

		if (isset($aFeed['feed_total_like']) && $aFeed['feed_total_like'] > Phpfox::getParam('feed.total_likes_to_display') && Phpfox::getParam('feed.total_likes_to_display') != 0) {
			$sLink = '<a href="#" onclick="return $Core.box(\'like.browse\', 400, \'in_feed=true&type_id=' . $aFeed['like_type_id'] . '&amp;item_id=' . $aFeed['item_id'] . '\');">';
			$iTotalLeftShow = ($aFeed['feed_total_like'] - Phpfox::getParam('feed.total_likes_to_display'));

			if ($iTotalLeftShow == 1) {
				$sPhrase .= '&nbsp;' . _p('and') . '&nbsp;' . $sLink . _p('1_other_person') . '&nbsp;';
			} else {
				$sPhrase .= '&nbsp;' . _p('and') . '&nbsp;' . $sLink . number_format($iTotalLeftShow) . '&nbsp;' . _p('others') . '&nbsp;';
			}
			$sPhrase .= '</a></span>' . _p('likes_this');
		} else {
			if (isset($aFeed['likes']) && count($aFeed['likes']) > 1) {
				$sPhrase .= '</span>&nbsp;' . _p('like_this');
			} else {
				if (isset($aFeed['feed_is_liked']) && $aFeed['feed_is_liked']) {

					if (count($aFeed['likes']) == 0 || count($aFeed['likes']) == 1) {
						$sPhrase .= '</span>&nbsp;' . _p('like_this');
					} else {
						if (count($aFeed['likes']) > 1) {
							$sPhrase .= '<a href="#" onclick="return $Core.box(\'like.browse\', 400, \'in_feed=true&type_id=' . $aFeed['like_type_id'] . '&amp;item_id=' . $aFeed['item_id'] . '\');">';
							$sPhrase .= number_format($aFeed['feed_total_like']) . '&nbsp;' . _p('others') . '&nbsp;';
							$sPhrase .= '</a></span>' . _p('likes_this');
						} else {
							$sPhrase .= '</span>' . _p('likes_this');
						}
					}
				} else {
					if (isset($aFeed['likes']) && count($aFeed['likes']) == 1) {
						$sPhrase .= '</span>&nbsp;' . _p('likes_this');
					} else if (strlen($sPhrase) > 1) {
						$sPhrase .= '</span>' . _p('like_this');
					}
				}
			}
		}

		$aActions = [];

		if (count($aActions) > 0) {
			$aFeed['bShowEnterCommentBlock'] = true;
			$aFeed['call_displayactions'] = true;
		}
		if (strlen($sPhrase) > 1 || count($aActions) > 0) {
			$aFeed['bShowEnterCommentBlock'] = true;
		}
		$sPhrase = str_replace(["&nbsp;&nbsp;", '  ', "\n"], ['&nbsp;', ' ', ''], $sPhrase);
		$sPhrase = str_replace(['  ', " &nbsp;", "&nbsp; "], ' ', $sPhrase);

		//',&nbsp;,'
		$sPhrase = str_replace(["\r\n", "\r"], "\n", $sPhrase);

		$aFeed['feed_like_phrase'] = $sPhrase;

		if (!empty($sOriginalIsLiked) && !$bForce) {
			$aFeed['feed_is_liked'] = $sOriginalIsLiked;
		}

		if (empty($sPhrase)) {
			$aFeed['feed_is_liked'] = false;
			$aFeed['feed_total_like'] = 0;
		}

		return $sPhrase;
	}

    /**
     * @return array
     */
	public function getTimeline()
	{
		return $this->_aFeedTimeline;
	}

    /**
     * @return string
     */
	public function getLastDay()
	{
		return $this->_sLastDayInfo;
	}

    /**
     * @param int $iFeed
     *
     * @return array
     */
	public function getLikeForFeed($iFeed)
	{
        $aLikeRows = $this->database()
            ->select('fl.feed_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('feed_like'), 'fl')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fl.user_id')
            ->where('fl.feed_id = ' . (int)$iFeed)
            ->execute('getSlaveRows');

		$aLikes = [];
		$aLikesCount = [];
		foreach ($aLikeRows as $aLikeRow) {
			if (!isset($aLikesCount[ $aLikeRow['feed_id'] ])) {
				$aLikesCount[ $aLikeRow['feed_id'] ] = 0;
			}

			$aLikesCount[ $aLikeRow['feed_id'] ]++;

			if ($aLikesCount[ $aLikeRow['feed_id'] ] > 3) {
				continue;
			}

			$aLikes[ $aLikeRow['feed_id'] ][] = $aLikeRow;
		}

		return [$aLikesCount, $aLikes];
	}

    /**
     * We get the redirect URL of the item depending on which module
     * it belongs to. We use the callback to connect to the correct module.
     *
     * @param integer $iId Is the ID# of the feed
     *
     * @return boolean|string If we are unable to find the correct feed, If we find the correct feed
     */
	public function getRedirect($iId)
	{
		// Get the feed
		$aFeed = $this->database()->select('privacy_comment, feed_id, type_id, item_id, user_id')
			->from($this->_sTable)
			->where('feed_id =' . (int)$iId)
			->execute('getSlaveRow');


		// Make sure we found a feed
		if (!isset($aFeed['feed_id'])) {
			return false;
		}
		$aProcessedFeed = $this->_processFeed($aFeed, false, $aFeed['user_id'], false);
		Phpfox_Url::instance()->send($aProcessedFeed['feed_link'], [], null, 302);
		/* Apparently in some CGI servers for some reason the redirect
         * triggers a 500 error when the callback doesnt exist
         * http://www.phpfox.com/tracker/view/6356/
         */
		if (!Phpfox::hasCallback($aFeed['type_id'], 'getFeedRedirect')) {
			return false;
		}

		// Run the callback so we get the correct link
		return Phpfox::callback($aFeed['type_id'] . '.getFeedRedirect', $aFeed['item_id'], $aFeed['child_item_id']);
	}

    /**
     * @param int    $iId
     * @param string $sPrefix
     *
     * @return array
     */
	public function getFeed($iId, $sPrefix = '')
	{
		return $this->database()->select('*')
			->from(Phpfox::getT(($sPrefix ? $sPrefix : (isset($this->_aCallback['table_prefix']) ? $this->_aCallback['table_prefix'] : '')) . 'feed'))
			->where('feed_id =' . (int)$iId)
			->executeRow();
	}

    /**
     * @param string $sText
     *
     * @return mixed
     */
	public function shortenText($sText)
	{
		$oParseOutput = Phpfox::getLib('parse.output');

		return $oParseOutput->split($oParseOutput->shorten($oParseOutput->parse($sText), 300, 'feed.view_more', true), 40);
	}

    /**
     * @param string $sText
     *
     * @return mixed
     */
	public function shortenTitle($sText)
	{
		$oParseOutput = Phpfox::getLib('parse.output');

		return $oParseOutput->shorten($oParseOutput->clean($sText), 60, '...');
	}

    /**
     * @param string $sText
     *
     * @return string
     */
	public function quote($sText)
	{
		Phpfox::getLib('parse.output')->setImageParser(['width' => 200, 'height' => 200]);

		$sNewText = '<div class="p_4">' . $this->shortenText($sText) . '</div>';

		Phpfox::getLib('parse.output')->setImageParser(['clear' => true]);

		return $sNewText;
	}

    /**
     * @param array  $aConds
     * @param string $sSort
     * @param string $iRange
     * @param string $sLimit
     *
     * @return array
     */
	public function getForBrowse($aConds, $sSort = 'feed.time_stamp DESC', $iRange = '', $sLimit = '')
	{
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'feed')
			->where($aConds)
			->execute('getSlaveField');

		$aRows = $this->database()->select('feed.*, fl.feed_id AS is_liked, ' . Phpfox::getUserField('u1', 'owner_') . ', ' . Phpfox::getUserField('u2', 'viewer_'))
			->from($this->_sTable, 'feed')
			->join(Phpfox::getT('user'), 'u1', 'u1.user_id = feed.user_id')
			->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = feed.item_user_id')
			->leftJoin(Phpfox::getT('feed_like'), 'fl', 'fl.feed_id = feed.feed_id AND fl.user_id = ' . Phpfox::getUserId())
			->where($aConds)
			->order($sSort)
			->limit($iRange, $sLimit, $iCnt)
			->execute('getSlaveRows');

		$aFeeds = [];
		foreach ($aRows as $aRow) {
			$aRow['link'] = Phpfox_Url::instance()->makeUrl('feed.view', ['id' => $aRow['feed_id']]);

			$aParts1 = explode('.', $aRow['type_id']);
			$sModule = $aParts1[0];
			if (strpos($sModule, '_')) {
				$aParts = explode('_', $sModule);
				$sModule = $aParts[0];
				if ($sModule == 'comment' && isset($aParts[1]) && !Phpfox::isModule($aParts[1])) {
					continue;
				}
			}

			if (!Phpfox::isModule($sModule)) {
				continue;
			}

			if (($aFeed = Phpfox::callback($aRow['type_id'] . '.getNewsFeed', $aRow))) {
				if (isset($aLikes[ $aFeed['feed_id'] ])) {
					$aFeed['like_rows'] = $aLikes[ $aFeed['feed_id'] ];
				}

				if (isset($aLikesCount[ $aFeed['feed_id'] ])) {
					$aFeed['like_count'] = ($aLikesCount[ $aFeed['feed_id'] ] - count($aFeed['like_rows']));
				}

				$aFeeds[] = $aFeed;
			}
		}

		return [$iCnt, $aFeeds];
	}

    /**
     * @param int $iId
     *
     * @return void
     * @throws Exception
     */
    public function processAjax($iId)
    {
        $oAjax = Phpfox_Ajax::instance();
        $aFeed = Phpfox::getService('feed')->get(Phpfox::getUserId(), $iId);
        $aFeed = reset($aFeed);

        if (!isset($aFeed)) {
            $oAjax->alert(_p('this_item_has_successfully_been_submitted'));
            $oAjax->call('$Core.resetActivityFeedForm();');

            return;
        }

        if (isset($aFeed['type_id'])) {
            Phpfox_Template::instance()->assign([
                'aFeed' => $aFeed,
                'aFeedCallback' => [
                    'module' => str_replace('_comment', '', $aFeed['type_id']),
                    'item_id' => $aFeed['item_id']
                ],
            ])->getTemplate('feed.block.entry');
        } else {
            Phpfox_Template::instance()->assign(['aFeed' => $aFeed])->getTemplate('feed.block.entry');
        }

        $sId = 'js_tmp_comment_' . md5('feed_' . uniqid() . Phpfox::getUserId()) . '';

        $sNewContent = '<div id="' . $sId . '" class="js_temp_new_feed_entry js_feed_view_more_entry_holder">' . $oAjax->getContent(false) . '</div>';

        $oAjax->insertAfter('#js_new_feed_comment', $sNewContent);

        $oAjax->removeClass('.js_user_feed', 'row_first');
        $oAjax->call("iCnt = 0; \$('.js_user_feed').each(function(){ iCnt++; if (iCnt == 1) { \$(this).addClass('row_first'); } });");
        if ($oAjax->get('force_form')) {
            $oAjax->call('tb_remove();');
            $oAjax->show('#js_main_feed_holder');
            $oAjax->call('setTimeout(function(){$Core.resetActivityFeedForm();$Core.loadInit();}, 500);');
        } else {
            $oAjax->call('$Core.resetActivityFeedForm();');
            $oAjax->call('$Core.loadInit();');
        }
    }

    /**
     * @param int $iId
     *
     * @return void
     */
    public function processUpdateAjax($iId)
    {
        $oAjax = Phpfox_Ajax::instance();
        $aFeeds = Phpfox::getService('feed')->get(null, $iId);
        if (!isset($aFeeds[0])) {
            $oAjax->alert(_p('this_item_has_successfully_been_submitted'));
            $oAjax->call('$Core.resetActivityFeedForm();');
            return;
        }

        if (isset($aFeeds[0]['type_id'])) {
            Phpfox_Template::instance()->assign([
                'aFeed'         => $aFeeds[0],
                'aFeedCallback' => [
                    'module'  => str_replace('_comment', '', $aFeeds[0]['type_id']),
                    'item_id' => $aFeeds[0]['item_id']
                ],
            ])->getTemplate('feed.block.entry');
        } else {
            Phpfox_Template::instance()->assign(['aFeed' => $aFeeds[0]])->getTemplate('feed.block.entry');
        }

        $oAjax->call('$("#js_item_feed_' . $iId . '").parent().html("' . $oAjax->getContent(true) . '");');
        $oAjax->call("tb_remove();");
        $oAjax->call('setTimeout(function(){$Core.resetActivityFeedForm();$Core.loadInit();}, 500);');
    }

    /**
     * @return array|int|mixed|string
     */
	public function getShareLinks()
	{
		if ($sPlugin = Phpfox_Plugin::get('feed.service_feed_getsharelinks__start')) {
			eval($sPlugin);
			if (isset($aPluginReturn)) {
				return $aPluginReturn;
			}
		}
		$sCacheId = $this->cache()->set('feed_share_link');

		if (!($aLinks = $this->cache()->get($sCacheId))) {
			$aLinks = $this->database()->select('fs.*')
				->from(Phpfox::getT('feed_share'), 'fs')
				->join(Phpfox::getT('module'), 'm', 'm.module_id = fs.module_id AND m.is_active = 1')
				->order('fs.ordering ASC')
				->execute('getSlaveRows');

			foreach ($aLinks as $iKey => $aLink) {
				$aLinks[ $iKey ]['module_block'] = $aLink['module_id'] . '.' . $aLink['block_name'];
			}

			$this->cache()->save($sCacheId, $aLinks);
            Phpfox::getLib('cache')->group(  'feed', $sCacheId);
		}
		$aNoDuplicates = [];
		if (!is_array($aLinks) || empty($aLinks)) {
			return $aLinks;
		}
		foreach ($aLinks as $iKey => $aLink) {
			unset($aLink['share_id']);
			if (in_array(serialize($aLink), $aNoDuplicates)) {
				unset($aLinks[ $iKey ]);
				continue;
			}
			if (Phpfox::hasCallback($aLink['module_id'], 'checkFeedShareLink') && Phpfox::callback($aLink['module_id'] . '.checkFeedShareLink') === false) {
				unset($aLinks[ $iKey ]);
			}
			$aNoDuplicates[] = serialize($aLink);
		}

		if ($sPlugin = Phpfox_Plugin::get('feed.service_feed_getsharelinks__end')) {
			eval($sPlugin);
			if (isset($aPluginReturn)) {
				return $aPluginReturn;
			}
		}

		foreach ($aLinks as $key => $value) {
			if ($value['module_id'] != 'photo') {
				unset($aLinks[ $key ]);
			}
		}

		return $aLinks;
	}

    /**
     * @deprecated This function will be removed in 4.6.0
     * @param array $aItem
     *
     * @return array|bool|int|string
     */
	public function getInfoForAction($aItem)
	{
		$aRow = $this->database()->select('fc.feed_comment_id, fc.content as title, fc.user_id, u.gender, u.user_name, u.full_name')
			->from(Phpfox::getT('feed_comment'), 'fc')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
			->where('fc.feed_comment_id = ' . (int)$aItem['item_id'])
			->execute('getSlaveRow');

		if (empty($aRow)) {
			return false;
		}
		$aRow['link'] = Phpfox_Url::instance()->makeUrl($aRow['user_name']);

		return $aRow;
	}

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return mixed
     */

	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('feed.service_feed__call')) {
			return eval($sPlugin);
		}

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
        return null;
	}

    /**
     * @param array  $aRow
     * @param string $sKey
     * @param int    $iUserId
     * @param bool   $bFirstCheckOnComments
     *
     * @return array|bool
     */
	private function _processFeed($aRow, $sKey, $iUserId, $bFirstCheckOnComments)
	{
		$original = (isset($aRow['content']) ? $aRow['content'] : '');
		switch ($aRow['type_id']) {
			case 'comment_profile':
			case 'comment_profile_my':
				$aRow['type_id'] = 'profile_comment';
				break;
			case 'profile_info':
				$aRow['type_id'] = 'custom';
				break;
			case 'comment_photo':
				$aRow['type_id'] = 'photo_comment';
				break;
			case 'comment_blog':
				$aRow['type_id'] = 'blog_comment';
				break;
			case 'comment_video':
				$aRow['type_id'] = 'video_comment';
				break;
			case 'comment_group':
				$aRow['type_id'] = 'pages_comment';
				break;
		}

		if (preg_match('/(.*)_feedlike/i', $aRow['type_id'])
			|| $aRow['type_id'] == 'profile_design'
		) {
			$this->database()->delete(Phpfox::getT('feed'), 'feed_id = ' . (int)$aRow['feed_id']);

			return false;
		}
		try {
			$App = (new Core\App())->get($aRow['type_id']);
			$isApp = true;
		} catch (Exception $e) {
			$isApp = false;
		}

		if (!$isApp && !Phpfox::hasCallback($aRow['type_id'], 'getActivityFeed')) {
			return false;
		}

		$is_cached = false;
		if (!isset($this->_aCallback['module']) && redis()->enabled() && ($aFeed = redis()->get('feed/' . $aRow['feed_id']))) {
			$is_cached = true;
			$aFeed = (array) $aFeed;
			if ($isApp) {
				$aRow['type_id'] = 'app';
				$aRow['item_id'] = $aRow['feed_id'];
				$aFeed['type_id'] = 'app';
				$aFeed['item_id'] = $aRow['feed_id'];
			}

			$aFeed['total_comment'] = redis()->get('total/comments/' . $aRow['type_id'] . '/' . $aRow['item_id']);
			$aFeed['feed_is_liked'] = redis()->get('is/feed/liked/' . user()->id . '/' . $aRow['type_id'] . '/' . $aRow['item_id']);
			$aFeed['feed_total_like'] = redis()->get('total/feed/liked/' . $aRow['type_id'] . '/' . $aRow['item_id']);

			if ($focus = redis()->get('feed_focus_' . $aRow['feed_id'])) {
				$aFeed['focus'] = (array) $focus;
			}

		} else {
			if (isset($App) && $isApp) {
				$aMap = $aRow;
				if ($aRow['parent_feed_id']) {
                    $aRow['main_feed_id'] = $aRow['feed_id'];
					$aMap['feed_id'] = $aRow['parent_feed_id'];
                    $aRow['feed_id'] = $aRow['parent_feed_id'];

				}
                $aRow['ori_item_id'] = $aRow['feed_id'];
                $aRow['item_id'] = $aRow['feed_id'];
				$Map = $App->map($aRow['content'], $aMap);
				$Map->data_row = $aRow;
				\Core\Event::trigger('feed_map', $Map);
				//add the app_id for event name to avoid conflict with another apps. (Rob)
				\Core\Event::trigger('feed_map_'.$App->id, $Map);
				if ($Map->error) {
				    return false;
                }

				$aFeed = [
				    'feed_table_prefix' => $Map->feed_table_prefix,
					'is_app'          => true,
					'app_object'      => $App->id,
					'feed_link'       => $Map->link,
					'feed_title'      => $Map->title,
					'feed_info'       => $Map->feed_info,
					'item_id'         => $aRow['feed_id'],
					'comment_type_id' => 'app',
					'like_type_id'    => 'app',
					'feed_total_like' => (int)$this->database()->select('COUNT(*)')->from(':like')->where(['type_id' => 'app', 'item_id' => $aRow['feed_id'], 'feed_table' => ($Map->feed_table_prefix . 'feed')])->execute('getSlaveField'),
					'total_comment'   => (int)$this->database()->select('COUNT(*)')->from(':comment')->where(['type_id' => 'app', 'item_id' => $aRow['feed_id'], 'feed_table' => ($Map->feed_table_prefix . 'feed')])->execute('getSlaveField'),
					'feed_is_liked'   => ($this->database()->select('COUNT(*)')->from(':like')->where(['type_id' => 'app', 'item_id' => $aRow['feed_id'], 'user_id' => Phpfox::getUserId()])->execute('getSlaveField') ? true : false),
				];

				if ($Map->content) {
					$aFeed['app_content'] = $Map->content;
				}

				if ($Map->more_params) {
					$aFeed = array_merge($aFeed, $Map->more_params);
				}


			} else {
				$aFeed = Phpfox::callback($aRow['type_id'] . '.getActivityFeed', $aRow, (isset($this->_aCallback['module']) ? $this->_aCallback : null));

				if (!empty($aRow['parent_feed_id']) && (new Core\App())->exists($aRow['parent_module_id'])) {
					$parent = $this->get(['id' => $aRow['parent_feed_id'], 'bIsChildren' => true]);
					if (isset($parent[0]) && isset($parent[0]['feed_id']) && (new Core\App())->exists($parent[0]['type_id'])) {
						$aFeed['parent_is_app'] = $parent[0]['feed_id'];
						if (Phpfox::hasCallback($parent[0]['type_id'], 'getActivityFeed'))
						{
							$aFeed['parent_module_id'] = $parent[0]['type_id'];
						}
					}
				}

				if ($aFeed === false) {
					return false;
				}
			}

			if (isset($this->_aViewMoreFeeds[ $sKey ])) {
				foreach ($this->_aViewMoreFeeds[ $sKey ] as $iSubKey => $aSubRow) {
					$mReturnViewMore = $this->_processFeed($aSubRow, $iSubKey, $iUserId, $bFirstCheckOnComments);

					if ($mReturnViewMore === false) {
						continue;
					}
					$mReturnViewMore['call_displayactions'] = true;
					$aFeed['more_feed_rows'][] = $mReturnViewMore;
				}
			}
		}

		if (Phpfox::isModule('like') && (isset($aFeed['like_type_id']) || isset($aRow['item_id'])) && ((isset($aFeed['enable_like']) && $aFeed['enable_like'])) || (!isset($aFeed['enable_like'])) && (isset($aFeed['feed_total_like']) && (int)$aFeed['feed_total_like'] > 0)) {
            $aFeed['likes'] = Phpfox::getService('like')->getLikesForFeed($aFeed['like_type_id'], (isset($aFeed['like_item_id']) ? $aFeed['like_item_id'] : $aRow['item_id']), ((int)$aFeed['feed_is_liked'] > 0 ? true : false), Phpfox::getParam('feed.total_likes_to_display'), !isset($aFeed['feed_total_like']), (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
            if(!isset($aFeed['feed_total_like'])){
                $aFeed['feed_total_like'] = Phpfox::getService('like')->getTotalLikeCount();
            }
		}

		if (isset($aFeed['comment_type_id']) && (int)$aFeed['total_comment'] > 0 && Phpfox::isModule('comment')) {
			$aFeed['comments'] = Phpfox::getService('comment')->getCommentsForFeed($aFeed['comment_type_id'], $aRow['item_id'], Phpfox::getParam('comment.comment_page_limit'), null, null, (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
		}

		$aRow['can_post_comment'] = true;
		$aFeed['bShowEnterCommentBlock'] = false;

//        if (Phpfox::isModule('share')) {
//            $aFeed['total_share'] = $this->getShareCount($aRow['type_id'], $aRow['item_id']);
//        }


		$aOut = array_merge($aRow, $aFeed);
        $aFeedActions = $this->getFeedActions($aOut);
        $aOut = array_merge($aOut, $aFeedActions);
		$aOut['_content'] = $original;
		$aOut['type_id'] = $aRow['type_id'];

		if (!isset($this->_aCallback['module']) && redis()->enabled() && !$is_cached) {
			redis()->set('feed/' . $aRow['feed_id'], $aOut);
		}

		if (($sPlugin = Phpfox_Plugin::get('feed.service_feed_processfeed'))) {
			eval($sPlugin);
		}
		return $aOut;
	}

    /**
     * Get feed actions
     * @param $aFeed
     * @return array
     */
	public function getFeedActions($aFeed)
    {
        $aActions = [];
        // can user like this feed?
        $aActions['can_like'] = (isset($aFeed['like_type_id']) || !empty($aFeed['type_id'])) && !(isset($aFeed['disable_like_function']) && $aFeed['disable_like_function']) && !Phpfox::getService('user.block')->isBlocked(null, $aFeed['user_id']);

        // check group member
        if (defined('PHPFOX_PAGES_ITEM_ID') && defined('PHPFOX_PAGES_ITEM_TYPE') && PHPFOX_PAGES_ITEM_TYPE == 'groups') {
            $aGroup = Phpfox::getService('groups')->getPage(PHPFOX_PAGES_ITEM_ID);
            $bGroupIsShareable = true;
            if (isset($aGroup['reg_method'])) {
                $bGroupIsShareable = ($aGroup['reg_method'] == 0) ? true : false;
            }
            $bIsGroupMember = Phpfox::isAdmin() ? true : Phpfox::getService('groups')->isMember($aGroup['page_id']);
        }
        // can user comment this feed?
        $aActions['can_comment'] = Phpfox::isModule('comment') && isset($aFeed['comment_type_id']) &&
            Phpfox::getUserParam('comment.can_post_comments') && Phpfox::getParam('feed.allow_comments_on_feeds') &&
            Phpfox::isUser() && $aFeed['can_post_comment'] && Phpfox::getUserParam('feed.can_post_comment_on_feed') &&
            (!isset($bIsGroupMember) || $bIsGroupMember);

        // can user share this feed?
        $aActions['can_share'] = Phpfox::isModule('share') && !isset($aFeed['no_share']) && !empty($aFeed['type_id']) &&
            (!isset($bGroupIsShareable) || $bGroupIsShareable) &&
            !Phpfox::getService('user.block')->isBlocked(null, $aFeed['user_id']);

        // total action
        $aActions['total_action'] = intval($aActions['can_like']) + intval($aActions['can_comment']) + intval($aActions['can_share']);

        return $aActions;
    }

    /**
     * @param string $sTypeId
     * @param int    $iItemId
     *
     * @return array
     */
	public function getParentFeedItem($sTypeId, $iItemId)
	{
		$aRow = $this->database()->select('f.*,' . Phpfox::getUserField('u'))
			->from(':feed', 'f')
			->join(':user', 'u', 'u.user_id=f.user_id')
			->where('type_id=\'' . $sTypeId . '\' AND item_id=' . (int)$iItemId)
			->executeRow();
		return $aRow;
	}

    public function getShareCount($sTypeId, $iItemId)
    {
        $aRow = $this->database()->select('COUNT(*)')
            ->from(':feed', 'f')
            ->where('parent_module_id=\'' . $sTypeId . '\' AND parent_feed_id=' . (int)$iItemId)
            ->executeField();
        return $aRow;
    }

    /**
     * @param array $aCallback
     * @param int $iFeedId
     * @param bool $bUseCache
     *
     * @return bool|mixed
     */
    public function getUserStatusFeed($aCallback, $iFeedId, $bUseCache = true)
    {
        //Make hash for cache
        $hash = 'hash_';
        if (isset($aCallback['module'])) {
            $hash .= $aCallback['module'];
        }
        if (isset($aCallback['table_prefix'])) {
            $hash .= $aCallback['table_prefix'];
        }
        if (isset($aCallback['item_id'])) {
            $hash .= $aCallback['item_id'];
        }
        $hash = md5($hash);

        $sCacheId = $this->cache()->set('feed_status_' . $iFeedId . '_' . $hash);

        if (!$bUseCache || !$aStatusFeed = $this->cache()->get($sCacheId)) {
            $aData = $this->callback($aCallback)->get(null, $iFeedId);
            if (isset($aData[0])) {
                $aStatusFeed = $aData[0];
            } else {
                return false;
            }
            $this->cache()->save($sCacheId, $aStatusFeed);
            Phpfox::getLib('cache')->group(  'feed', $sCacheId);
        }

        return $aStatusFeed;
    }

    /**
     * @param string $sType
     * @param int $iId
     *
     * @return bool
     */
    public function canSponsoredInFeed($sType, $iId)
    {
        $bPluginInChange = true;
        if (($sPlugin = Phpfox_Plugin::get('feed.service_feed_can_sponsored'))) {
            eval($sPlugin);
        }

        if ($bPluginInChange && !Phpfox::isModule('ad')) {
            return false;
        }

        $iFeedId = $this->database()->select('feed_id')
            ->from(':feed')
            ->where('type_id="' . $sType . '" AND item_id=' . (int)$iId)
            ->execute('getSlaveField');

        if (!$iFeedId) {
            return false;
        }

        $aRow = $this->database()->select('*')
            ->from(Phpfox::getT('ad_sponsor'))
            ->where('module_id = "feed" AND item_id=' . (int)$iFeedId)
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            return true;
        }

        if ($aRow['is_active'] == 0) {
            return false;
        }

        return $aRow['item_id'];
    }

    /**
     * @param $iItemId
     * @param $sType
     * @return array|int|string
     */
    public function getTaggedUsers($iItemId, $sType)
    {
        return db()->select('td.*, '.Phpfox::getUserField())
            ->from(':feed_tag_data','td')
            ->join(':user','u','u.user_id = td.user_id')
            ->where('td.item_id = '.(int)$iItemId.' AND td.type_id = \''.$sType.'\'')
            ->execute('getSlaveRows');
    }
}
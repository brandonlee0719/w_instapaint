<?php
namespace Apps\Core_Forums\Service\Thread;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Request;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');


class Thread extends Phpfox_Service
{
    /**
     * @var bool
     */
    private $_bIsSearch = false;

    /**
     * @var bool
     */
    private $_bIsTagSearch = false;

    /**
     * @var bool
     */
    private $_bIsNewSearch = false;

    /**
     * @var bool
     */
    private $_isSubscribeSearch = false;

    /**
     * @var bool
     */
    private $_bIsModuleTagSearch = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('forum_thread');
    }

    /**
     * @param bool $bIsSearch
     *
     * @return $this
     */
    public function isSearch($bIsSearch = true)
    {
        $this->_bIsSearch = $bIsSearch;
        return $this;
    }

    /**
     * @param bool $bIsTagSearch
     *
     * @return $this
     */
    public function isTagSearch($bIsTagSearch = false)
    {
        $this->_bIsTagSearch = $bIsTagSearch;
        return $this;
    }

    /**
     * @param bool $bIsNewSearch
     *
     * @return $this
     */
    public function isNewSearch($bIsNewSearch = false)
    {
        $this->_bIsNewSearch = $bIsNewSearch;
        return $this;
    }

    /**
     * @param $bIsSubscribeSearch
     *
     * @return $this
     */
    public function isSubscribeSearch($bIsSubscribeSearch)
    {
        $this->_isSubscribeSearch = $bIsSubscribeSearch;
        return $this;
    }

    /**
     * @param bool $bIsModuleTagSearch
     *
     * @return $this
     */
    public function isModuleSearch($bIsModuleTagSearch)
    {
        $this->_bIsModuleTagSearch = $bIsModuleTagSearch;
        return $this;
    }

    /**
     * @param int $iForumId
     *
     * @return bool
     */
    public function canViewForumId($iForumId)
    {
        $aAllowed = $this->getCanViewForumIdList([$iForumId]);
        if (isset($aAllowed[0])) {
            return ($aAllowed[0] == 0) ? false : true;
        }
        return false;
    }

    /**
     * @param array $aIds check input
     *
     * @return array
     */
    public function getCanViewForumIdList($aIds = [])
    {
        $aNotAllowed = $this->database()->select('forum_id')
            ->from(Phpfox::getT('forum_access'))
            ->where('user_group_id = ' . (int)Phpfox::getUserBy('user_group_id') . ' AND var_name = \'can_view_forum\' and var_value=0')
            ->execute('getSlaveRows');

        $aNotAllowed = array_map(function ($row) {
            return $row['forum_id'];
        }, $aNotAllowed);

        $aIds = array_map(function ($id) {
            return intval($id);
        }, array_diff($aIds, $aNotAllowed));

        if (!$aIds) {
            $aIds = [0];
        }

        return $aIds;
    }

    /**
     * @param array $mConditions
     * @param string $sOrder
     * @param string $iPage
     * @param string|int $iPageSize
     * @param bool $bCount
     * @param bool $check_access , remove in 4.7.0
     *
     * @return array|int|string
     */
    public function get(
        $mConditions = array(),
        $sOrder = 'ft.time_update DESC',
        $iPage = '',
        $iPageSize = '',
        $bCount = true,
        $check_access = false
    ) {

        if ($this->_bIsTagSearch !== false) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag',
                "tag.item_id = ft.thread_id AND tag.category_id = '" . ($this->_bIsModuleTagSearch ? 'forum_group' : 'forum') . "'");
        }

        if ($this->_bIsNewSearch !== false) {
            $mConditions[] = 'AND ft.time_update > \'' . $this->getNewTimeStamp() . '\'';
            $mConditions[] = 'AND fp.time_stamp > \'' . $this->getNewTimeStamp() . '\'';
        }

        if ($this->_isSubscribeSearch !== false) {
            $this->database()->join(Phpfox::getT('forum_subscribe'), 'fs',
                'fs.thread_id = ft.thread_id AND fs.user_id = ' . Phpfox::getUserId());
        } else {
            $this->database()->leftJoin(Phpfox::getT('forum_subscribe'), 'fs',
                'fs.thread_id = ft.thread_id AND fs.user_id = ' . Phpfox::getUserId());
        }
        if ($this->_bIsSearch === true) {
            $this->database()->select('f.name AS forum_name, f.name_url AS forum_url, ')->leftJoin(Phpfox::getT('forum'),
                'f', 'f.forum_id = ft.forum_id');
        } else {
            $this->database()->leftJoin(Phpfox::getT('forum'), 'f', 'f.forum_id = ft.forum_id');
        }

        if (Phpfox::getParam('forum.forum_database_tracking')) {
            $this->database()->select('ftr.item_id AS is_seen, ftr.time_stamp AS last_seen_time, ')
                ->leftJoin(Phpfox::getT('track'), 'ftr',
                    'ftr.item_id = ft.thread_id AND ftr.user_id = ' . Phpfox::getUserId() . ' AND ftr.type_id=\'forum_thread\'');
        }

        (($sPlugin = Phpfox_Plugin::get('forum.service_thread_get_query')) ? eval($sPlugin) : false);

        if (isset($bLeftJoinQuery)) {
            $this->database()->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id');
        } else {
            $this->database()->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id');
        }

        $aThreads = $this->database()->select('ft.*, fs.subscribe_id AS is_subscribed, f.is_closed as forum_is_closed, ' . Phpfox::getUserField() . ', ' . Phpfox::getUserField('u2',
                'last_') . '')
            ->from($this->_sTable, 'ft')
            ->leftJoin(Phpfox::getT('forum_post'), 'fp', 'ft.thread_id = fp.thread_id')
            ->leftJoin(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
            ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = ft.last_user_id')
            ->where($mConditions)
            ->group('ft.thread_id', true)
            ->order($sOrder)
            ->limit($iPage, $iPageSize)
            ->forCount()
            ->execute('getSlaveRows');
        $iCnt = $this->database()->getCount();
        foreach ($aThreads as $iKey => $aThread) {
            $this->getThreadPermission($aThreads[$iKey]);
            $sCss = 'new';
            if ($aThread['is_closed']) {
                $sCss = 'closed';
            } else {
                if (!isset($aThread['is_seen'])) {
                    $aThread['is_seen'] = 0;
                }

                // Thread not seen
                if (!$aThread['is_seen']) {
                    // User has signed up after the post so they have already seen the post
                    if ((Phpfox::isUser() && Phpfox::getUserBy('joined') > $aThread['time_update']) || (!Phpfox::isUser() && Phpfox::getCookie('visit') > $aThread['time_update'])) {
                        $sCss = 'old';
                    } elseif (($iLastTimeViewed = Phpfox::getLib('session')->getArray('forum_view',
                            $aThread['thread_id'])) && (int)$iLastTimeViewed > $aThread['time_update']
                    ) {
                        $sCss = 'old';
                    } // Checks if the post is older then our default active post time limit
                    elseif ((PHPFOX_TIME - Phpfox::getParam('forum.keep_active_posts') * 60) > $aThread['time_update']) {
                        $sCss = 'old';
                    } elseif (!empty($aThread['time_update']) && Phpfox::isUser() && $aThread['time_update'] < Phpfox::getCookie('last_login')) {
                        $sCss = 'old';
                    }
                } else {
                    // New post was added
                    if ($aThread['time_update'] <= $aThread['last_seen_time']) {
                        $sCss = 'old';
                    }
                }
            }

            $aThreads[$iKey]['css_class'] = $sCss;

            switch ($sCss) {
                case 'new':
                    $sCssPhrase = _p('thread_contains_new_posts');
                    break;
                case 'old':
                    $sCssPhrase = _p('no_new_posts');
                    break;
                case 'closed':
                    $sCssPhrase = _p('thread_is_closed');
                    break;
                default:
                    $sCssPhrase = '';
                    break;
            }
            $aThreads[$iKey]['css_class_phrase'] = $sCssPhrase;
            $aThreads[$iKey]['last_post'] = Phpfox::getService('forum.post')->getLastPost($aThread['thread_id']);
        }


        if (!$bCount) {
            return $aThreads;
        }

        return array($iCnt, $aThreads);
    }

    /**
     * @return int|string
     */
    public function getNewTimeStamp()
    {
        $iJoined = Phpfox::getUserBy('joined');
        $iOld = (PHPFOX_TIME - (Phpfox::getParam('forum.keep_active_posts') * 60));

        return ($iJoined > $iOld ? $iJoined : $iOld);
    }

    public function getThreadPermission(&$aRow, $aCallback = null)
    {
        if (Phpfox::isModule('pages') || Phpfox::isModule('groups')) {
            if ($aCallback == null && $aRow['group_id'] > 0 && ($sParentId = Phpfox::getPagesType($aRow['group_id'])) && Phpfox::isModule($sParentId)) {
                $aCallback = Phpfox::callback($sParentId . '.addForum', $aRow['group_id']);
                if (isset($aCallback['module']) && !isset($aCallback['module_id'])) {
                    $aCallback['module_id'] = $aCallback['module'];
                }
            }
        }
        if (!isset($aRow['forum_is_closed']) || !$aRow['forum_is_closed']) {
            if ($aCallback === null) {
                $aRow['canEdit'] = ((Phpfox::getUserParam('forum.can_edit_own_post') && $aRow['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_edit_other_posts') || Phpfox::getService('forum.moderate')->hasAccess($aRow['forum_id'],
                        'edit_post'));
                $aRow['canDelete'] = (Phpfox::getUserParam('forum.can_delete_own_post') && $aRow['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts') || Phpfox::getService('forum.moderate')->hasAccess($aRow['forum_id'],
                        'delete_post');
                $aRow['canStick'] = !$aRow['view_id'] && !$aRow['is_announcement'] && (Phpfox::getUserParam('forum.can_stick_thread') || Phpfox::getService('forum.moderate')->hasAccess($aRow['forum_id'],
                            'post_sticky'));
                $aRow['canClose'] = !$aRow['view_id'] && !$aRow['is_announcement'] && (Phpfox::getUserParam('forum.can_close_a_thread') || Phpfox::getService('forum.moderate')->hasAccess($aRow['forum_id'],
                            'close_thread'));
                $aRow['canMerge'] = !$aRow['view_id'] && !$aRow['is_announcement'] && (Phpfox::getUserParam('forum.can_merge_forum_threads') || Phpfox::getService('forum.moderate')->hasAccess($aRow['forum_id'],
                            'merge_thread'));
                $aRow['canMove'] = !$aRow['view_id'] && Phpfox::getUserParam('forum.can_move_forum_thread') || Phpfox::getService('forum.moderate')->hasAccess('' . $aRow['forum_id'] . '',
                        'move_thread');
                $aRow['canCopy'] = !$aRow['view_id'] && Phpfox::getUserParam('forum.can_copy_forum_thread') || Phpfox::getService('forum.moderate')->hasAccess('' . $aRow['forum_id'] . '',
                        'copy_thread');
            } else {
                if (Phpfox::getService($aCallback['module'])->isAdmin($aCallback['item'])) {
                    $aRow['canEdit'] = $aRow['canDelete'] = true;
                    $aRow['canStick'] = $aRow['canClose'] = $aRow['canMerge'] = !$aRow['is_announcement'] && !$aRow['view_id'];
                } else {
                    $aRow['canEdit'] = (Phpfox::getUserParam('forum.can_edit_own_post') && $aRow['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_edit_other_posts');
                    $aRow['canDelete'] = (Phpfox::getUserParam('forum.can_delete_own_post') && $aRow['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts');
                    $aRow['canStick'] = !$aRow['view_id'] && !$aRow['is_announcement'] && Phpfox::getUserParam('forum.can_stick_thread');
                    $aRow['canClose'] = !$aRow['view_id'] && !$aRow['is_announcement'] && Phpfox::getUserParam('forum.can_close_a_thread');
                    $aRow['canMerge'] = !$aRow['view_id'] && !$aRow['is_announcement'] && Phpfox::getUserParam('forum.can_merge_forum_threads');
                }
                $aRow['canMove'] = $aRow['canCopy'] = false;
            }
            $aRow['canApprove'] = $aRow['view_id'] && (Phpfox::getUserParam('forum.can_approve_forum_thread') || Phpfox::getService('forum.moderate')->hasAccess('' . $aRow['forum_id'] . '',
                        'approve_thread'));
            $aRow['canSponsor'] = !$aRow['view_id'] && !$aRow['is_announcement'] && Phpfox::isModule('ad') && !defined('PHPFOX_IS_GROUP_VIEW') && Phpfox::getUserParam('forum.can_sponsor_thread');
            $aRow['canPurchaseSponsor'] = !$aRow['view_id'] && !$aRow['is_announcement'] && (Phpfox::getUserParam('forum.can_purchase_sponsor') && Phpfox::getService('forum')->getSponsorPrice()) && !defined('PHPFOX_IS_GROUP_VIEW') && Phpfox::isModule('ad');
            $aRow['canReply'] =  !$aRow['is_closed'] && !$aRow['view_id'] && !$aRow['is_announcement'] && Phpfox::isUser() && Phpfox::getService('forum.thread')->canReplyOnThread($aRow['thread_id']) && ((user('forum.can_reply_to_own_thread') && $aRow['user_id'] == Phpfox::getUserId()) || Phpfox::getService('forum.moderate')->hasAccess($aRow['forum_id'],
                        'can_reply') || user('forum.can_reply_on_other_threads'));
        } else {
            if ($aCallback === null) {

                $aRow['canDelete'] = (Phpfox::getUserParam('forum.can_delete_own_post') && $aRow['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts') || Phpfox::getService('forum.moderate')->hasAccess($aRow['forum_id'],
                        'delete_post');
            } else {
                $aRow['canDelete'] = (Phpfox::getUserParam('forum.can_delete_own_post') && $aRow['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts') || Phpfox::getService($aCallback['module'])->isAdmin($aCallback['item']);
            }
            $aRow['canMove'] = $aRow['canCopy'] = $aRow['canEdit'] = $aRow['canApprove'] = $aRow['canSponsor'] = $aRow['canPurchaseSponsor'] = $aRow['canReply'] = $aRow['canStick'] = $aRow['canClose'] = $aRow['canMerge'] = false;
        }
        $aRow['hasPermission'] = true;

    }

    /**
     * @param $iThreadId
     *
     * @return bool
     */
    public function canReplyOnThread($iThreadId)
    {
        if (!Phpfox::isModule('pages') && !Phpfox::isModule('groups')) {
            return true;
        }
        $aPage = $this->database()->select('p.*, ft.forum_id')
            ->from(':forum_thread', 'ft')
            ->leftJoin(':pages', 'p', 'p.page_id=ft.group_id')
            ->where('ft.thread_id=' . (int)$iThreadId)
            ->execute('getSlaveRow');
        if (isset($aPage['page_id'])) {
            if ($aPage['item_type']) {
                return Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'forum.reply_forum');
            } else {
                return Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'forum.reply_forum');
            }
        } else {
            return true;
        }
    }

    /**
     * @param array $mConditions
     * @param string $sOrder
     *
     * @return array
     */
    public function getSearch($mConditions = array(), $sOrder = 'ft.time_update DESC')
    {
        if ($this->_bIsNewSearch !== false) {
            $mConditions[] = 'AND ft.time_update > \'' . $this->getNewTimeStamp() . '\' AND ' . $this->database()->isNull('ftr.item_id');
            $this->database()->leftJoin(Phpfox::getT('track'), 'ftr',
                'ftr.item_id = ft.thread_id AND ftr.user_id = ' . Phpfox::getUserId() . ' AND ftr.type_id="forum_thread"');
        }

        $aThreads = $this->database()->select('ft.thread_id')
            ->from($this->_sTable, 'ft')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
            ->where($mConditions)
            ->order($sOrder)
            ->execute('getSlaveRows');

        $aSearchIds = array();
        foreach ($aThreads as $aThread) {
            $aSearchIds[] = $aThread['thread_id'];
        }

        return $aSearchIds;
    }

    /**
     * @param null|int $iForumId
     * @param null|int $iGroupId
     *
     * @return array|bool|int|string
     */
    public function getAnnoucements($iForumId = null, $iGroupId = null)
    {
        if ($iForumId !== null) {
            $key = 'forum/announcement/' . $iForumId;
            if (redis()->enabled() && redis()->exists($key)) {
                $aAnnouncements = redis()->get_as_array($key);
            } else {
                $aAnnouncements = $this->database()->select('ft.*, fs.subscribe_id AS is_subscribed, fa.announcement_id, ' . Phpfox::getUserField())
                    ->from(Phpfox::getT('forum_announcement'), 'fa')
                    ->join($this->_sTable, 'ft', 'ft.thread_id = fa.thread_id')
                    ->leftJoin(Phpfox::getT('forum_subscribe'), 'fs', 'fs.thread_id = ft.thread_id AND fs.user_id = ' . Phpfox::getUserId())
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
                    ->where('fa.forum_id = ' . (int)$iForumId)
                    ->order('ft.time_update DESC')
                    ->execute('getSlaveRows');

                if (redis()->enabled()) {
                    redis()->set($key, $aAnnouncements);
                }
            }
        } else {
            $aAnnouncements = $this->database()->select('ft.*, fs.subscribe_id AS is_subscribed,' . Phpfox::getUserField())
                ->from($this->_sTable, 'ft', 'ft.thread_id = fa.thread_id')
                ->leftJoin(Phpfox::getT('forum_subscribe'), 'fs', 'fs.thread_id = ft.thread_id AND fs.user_id = ' . Phpfox::getUserId())
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
                ->where('ft.group_id = ' . (int)$iGroupId . ' AND ft.view_id = 0 AND ft.is_announcement = 1')
                ->order('ft.time_update DESC')
                ->execute('getSlaveRows');

        }

        if (is_bool($aAnnouncements)) {
            $aAnnouncements = [];
        }

        foreach ($aAnnouncements as $iKey => $aAnnouncement) {
            $aAnnouncements[$iKey]['css_class'] = 'old';
            $aAnnouncements[$iKey]['css_class_phrase'] = _p('no_new_posts');
            $aAnnouncements[$iKey]['time_stamp_phrase'] = Phpfox::getTime(Phpfox::getParam('forum.forum_time_stamp'),
                $aAnnouncement['time_stamp']);
            $aAnnouncements[$iKey]['last_post'] = Phpfox::getService('forum.post')->getLastPost($aAnnouncement['thread_id']);
            $this->getThreadPermission($aAnnouncements[$iKey]);
        }
        return $aAnnouncements;
    }

    /**
     * @param int $iId
     *
     * @return array|bool|int|string
     */
    public function getForRedirect($iId)
    {
        $aThread = $this->database()->select('ft.thread_id, ft.forum_id, ft.group_id, ft.title_url, f.name_url AS forum_url, f.is_closed as forum_is_closed, pt.item_id AS is_viewed')
            ->from($this->_sTable, 'ft')
            ->leftJoin(Phpfox::getT('forum'), 'f', 'f.forum_id = ft.forum_id')
            ->leftJoin(Phpfox::getT('track'), 'pt',
                'pt.item_id = ft.thread_id AND pt.user_id = ' . Phpfox::getUserId() . ' AND ft.type_id=\'forum\'')
            ->where('ft.thread_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aThread['thread_id'])) {
            return false;
        }

        return $aThread;
    }

    /**
     * @param int $iId
     * @param bool $aCallback
     *
     * @return array|int|string
     */
    public function getActualThread($iId, $aCallback = false)
    {
        if ($aCallback === false) {
            $this->database()->select('f.forum_id, f.name_url AS forum_url, f.is_closed AS forum_is_closed, ')->leftJoin(Phpfox::getT('forum'),
                'f', 'f.forum_id = ft.forum_id');
        }

        $aRow = $this->database()->select('ft.*, fs.subscribe_id AS is_subscribed')
            ->from($this->_sTable, 'ft')
            ->leftJoin(Phpfox::getT('forum_subscribe'), 'fs',
                'fs.thread_id = ft.thread_id AND fs.user_id = ' . Phpfox::getUserId())
            ->where('ft.thread_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['is_subscribed'] = ($aRow['is_subscribed'] > 0 ? '1' : '0');

        return $aRow;
    }

    /**
     * @param int $iId
     *
     * @return array|int|string
     */
    public function getForEdit($iId)
    {
        if (Phpfox::isModule('poll')) {
            $this->database()->select('p.question AS poll_question, ')->leftJoin(Phpfox::getT('poll'), 'p',
                'p.poll_id = ft.poll_id');
        }

        $aThread = $this->database()->select('ft.*, fpt.text, f.name_url AS forum_url')
            ->from($this->_sTable, 'ft')
            ->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = ft.start_id')
            ->leftJoin(Phpfox::getT('forum'), 'f', 'f.forum_id = ft.forum_id')
            ->where('ft.thread_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if ($aThread['is_announcement']) {
            $aThread['type_id'] = 'announcement';
        } elseif ($aThread['order_id']) {
            $aThread['type_id'] = 'sticky';
        }

        $aThread['total_attachment'] = Phpfox::getService('attachment')->getCountForItem($aThread['start_id'], 'forum');

        return $aThread;
    }

    /**
     * @param int $iLimit
     * @param null|string $sForumIds
     * @param int $iGroupId
     *
     * @return array|int|string
     */
    public function getForRss($iLimit, $sForumIds = null, $iGroupId = 0)
    {
        $aCond = [];
        if ($sForumIds !== null && !empty($sForumIds)) {
            $aCond[] = 'AND ft.forum_id IN(' . $sForumIds . ')';
        }
        $aCond[] = 'AND ft.group_id = ' . (int)$iGroupId . ' AND ft.view_id = 0 AND ft.is_announcement = 0';

        $sNotAllowed = Phpfox::getService('forum')->getCanViewForumAccess('can_view_forum');
        if (!empty($sNotAllowed)) {
            $aCond[] = 'AND ft.forum_id NOT IN(' . $sNotAllowed . ')';
        }
        $aRows = $this->database()->select('ft.thread_id, ft.title, ft.title_url, ft.forum_id, ft.group_id, ft.time_stamp, ' . (Phpfox::getParam('core.allow_html') ? 'fpt.text_parsed' : 'fpt.text') . ' AS description, f.name AS forum_name, f.name_url AS forum_url, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ft')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
            ->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = ft.start_id')
            ->leftJoin(Phpfox::getT('forum'), 'f', 'f.forum_id = ft.forum_id')
            ->where($aCond)
            ->limit($iLimit)
            ->order('ft.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['link'] = Phpfox::permalink('forum.thread', $aRow['thread_id'], $aRow['title']);
            $aRows[$iKey]['creator'] = $aRow['full_name'];
        }

        return $aRows;
    }

    /**
     * @param int $iGroup
     *
     * @return array|int|string
     */
    public function getForParent($iGroup)
    {
        $aRows = $this->database()->select('ft.title, ft.title_url, ft.time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ft')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
            ->where('ft.group_id = ' . (int)$iGroup . ' AND ft.view_id = 0 AND ft.is_announcement = 0')
            ->limit(5)
            ->order('ft.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['time_stamp_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'),
                $aRow['time_stamp']);
        }

        return $aRows;
    }

    /**
     * @return int
     */
    public function getPendingThread()
    {
        return (int)$this->database()
            ->select('COUNT(*)')
            ->from(Phpfox::getT('forum_thread'))
            ->where('view_id = 1')
            ->execute('getSlaveField');
    }

    /**
     * @param array $mConditions
     * @param string $sOrder
     * @param string|int $iPage
     * @param string|int $iPageSize
     * @param bool $bCount
     * @param bool $check_access , remove in 4.7.0
     *
     * @return array|int|string
     */
    public function getRecentDiscussions(
        $mConditions = array(),
        $sOrder = 'ft.time_update DESC',
        $iPage = '',
        $iPageSize = '',
        $bCount = true,
        $check_access = false
    ) {
        $aThreads = $this->database()->select('ft.*, fpt.text, fpt.text_parsed, ' . Phpfox::getUserField() . ', ' . Phpfox::getUserField('u2',
                'last_') . '')
            ->from($this->_sTable, 'ft')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
            ->join(Phpfox::getT('forum_post'), 'fp', 'ft.thread_id = fp.thread_id AND fp.title != \'\'')
            ->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
            ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = ft.last_user_id')
            ->where($mConditions)
            ->order($sOrder)
            ->limit($iPage, $iPageSize)
            ->execute('getSlaveRows');


        foreach ($aThreads as $iKey => $aThread) {
            $sCss = 'new';
            $aThreads[$iKey]['css_class'] = 'new';
            switch ($sCss) {
                case 'new':
                    $sCssPhrase = _p('thread_contains_new_posts');
                    break;
                case 'old':
                    $sCssPhrase = _p('no_new_posts');
                    break;
                case 'closed':
                    $sCssPhrase = _p('thread_is_closed');
                    break;
                default:
                    $sCssPhrase = '';
                    break;
            }
            $aThreads[$iKey]['css_class_phrase'] = $sCssPhrase;
            $aThreads[$iKey]['is_block'] = true;
        }


        if (!$bCount) {
            return $aThreads;
        }

        return array(count($aThreads), $aThreads);

    }

    /**
     * @param $iForumId
     * @return array|int|string
     */
    public function getTotalThreadBelongToForum($iForumId)
    {
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('forum_id =' . (int)$iForumId)
            ->execute('getSlaveField');
    }

    /**
     * @return array|int|string
     */
    public function getMyThreadTotal()
    {
        return db()->select('COUNT(*)')
            ->from(':forum_thread')
            ->where('user_id =' . (int)Phpfox::getUserId() . ' AND group_id = 0 AND view_id >= 0 and is_announcement = 0')
            ->execute('getSlaveField');
    }

    public function getRandomSponsored($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('forum_thread_sponsored');
        if (!($sThreadIds = $this->cache()->get($sCacheId, $iCacheTime))) {
            $aThreadIds = $this->database()->select('ft.thread_id')
                ->from($this->_sTable, 'ft')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
                ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = ft.thread_id')
                ->where('ft.view_id = 0 AND ft.order_id = 2 AND ft.group_id = 0 AND s.module_id = \'forum_thread\' AND s.is_active = 1')
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');
            foreach ($aThreadIds as $key => $aId) {
                if ($key != 0) {
                    $sThreadIds .= ',' . $aId['thread_id'];
                } else {
                    $sThreadIds = $aId['thread_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sThreadIds);
            }

        }
        if (empty($sThreadIds)) {
            return [];
        }
        $aThreadIds = explode(',', $sThreadIds);
        shuffle($aThreadIds);
        $aThreadIds = array_slice($aThreadIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));
        $aThreads = $this->database()->select('s.*, ft.*, fpt.text, fpt.text_parsed, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ft')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ft.user_id')
            ->join(Phpfox::getT('forum_post'), 'fp', 'ft.thread_id = fp.thread_id AND fp.title != \'\'')
            ->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
            ->join(Phpfox::getT('ad_sponsor'), 's', 's.item_id = ft.thread_id AND s.module_id = \'forum_thread\'')
            ->where('ft.thread_id IN (' . implode(',', $aThreadIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        if (!isset($aThreads[0]) || empty($aThreads[0])) {
            return array();
        }
        if (Phpfox::isModule('ad')) {
            $aThreads = Phpfox::getService('ad')->filterSponsor($aThreads);
        }
        shuffle($aThreads);
        return $aThreads;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('forum.service_thread_thread__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * Check if current user is admin of event's parent item
     * @param $iThreadId
     * @return bool|mixed
     */
    public function isAdminOfParentItem($iThreadId)
    {
        $aThread = Phpfox::getService('forum.thread')->getForEdit($iThreadId);
        if (!Phpfox::isModule('pages') && !Phpfox::isModule('groups')) {
            return false;
        }
        $sParentId = Phpfox::getPagesType($aThread['group_id']);

        if (!$aThread || !$aThread['group_id'] || !Phpfox::isModule($sParentId) || !Phpfox::hasCallback($sParentId, 'isAdmin')) {
            return false;
        }
        return Phpfox::callback($sParentId . '.isAdmin', $aThread['group_id']);
    }

    /**
     * @param array $aThreadCondition
     * @param array $mConditions
     * @param string $sOrder
     * @param string|int $iPage
     * @param string|int $iPageSize
     * @param null|string $sPermaView
     *
     * @return array
     */
    public function getThread(
        $aThreadCondition = array(),
        $mConditions = array(),
        $sOrder = 'fp.time_stamp ASC',
        $iPage = '',
        $iPageSize = '',
        $sPermaView = null
    ) {
        if (Phpfox::isModule('track')) {
            $sJoinQuery = Phpfox::isUser() ? 'ftr.user_id = ' . Phpfox::getUserBy('user_id') : 'ftr.ip_address = \'' . $this->database()->escape(Phpfox::getIp()) . '\'';
            $this->database()->select('ftr.item_id AS is_seen, ftr.time_stamp AS last_seen_time, ')
                ->leftJoin(Phpfox::getT('track'), 'ftr',
                    'ftr.item_id = ft.thread_id AND ftr.type_id=\'forum_thread\' AND '.$sJoinQuery);
        }

        $aThread = $this->database()->select('ft.thread_id, ft.time_stamp, ft.time_update, ft.group_id, ft.view_id, ft.forum_id, ft.is_closed, ft.user_id, ft.is_announcement, ft.order_id, ft.title_url, ft.time_update AS last_time_stamp, ft.title, fs.subscribe_id AS is_subscribed, ft.poll_id, f.forum_id, f.is_closed as forum_is_closed')
            ->from($this->_sTable, 'ft')
            ->leftjoin(Phpfox::getT('forum'), 'f', 'f.forum_id = ft.forum_id')
            ->leftJoin(Phpfox::getT('forum_subscribe'), 'fs',
                'fs.thread_id = ft.thread_id AND fs.user_id = ' . Phpfox::getUserId())
            ->where($aThreadCondition)
            ->execute('getSlaveRow');

        if (!isset($aThread['thread_id'])) {
            return array(0, array());
        }

        if (!isset($aThread['is_seen'])) {
            $aThread['is_seen'] = 0;
        }

        // Thread not seen
        if (!$aThread['is_seen'] && Phpfox::isUser()) {
            // User has signed up after the post so they have already seen the post
            if ((Phpfox::isUser() && Phpfox::getUserBy('joined') > $aThread['last_time_stamp']) || (!Phpfox::isUser() && Phpfox::getCookie('visit') > $aThread['last_time_stamp'])) {
                $aThread['is_seen'] = 1;
            } elseif (($iLastTimeViewed = Phpfox::getLib('session')->getArray('forum_view',
                    $aThread['thread_id'])) && (int)$iLastTimeViewed > $aThread['last_time_stamp']
            ) {
                $aThread['is_seen'] = 1;
            } // Checks if the post is older then our default active post time limit
            elseif ((PHPFOX_TIME - Phpfox::getParam('forum.keep_active_posts') * 60) > $aThread['last_time_stamp']) {
                $aThread['is_seen'] = 1;
            }
        } else {
            // New post was added
            if ($aThread['last_time_stamp'] > $aThread['last_seen_time']) {
                $aThread['is_seen'] = 0;
            }
        }

        $sViewId = (Phpfox::getUserParam('forum.can_approve_forum_post') || Phpfox::getService('forum.moderate')->hasAccess($aThread['forum_id'], 'approve_post')) ? '' : ' AND fp.view_id = 0';

        $mConditions[] = 'fp.thread_id = ' . $aThread['thread_id'] . $sViewId;

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('forum_post'), 'fp')
            ->where($mConditions)
            ->execute('getSlaveField');

        $aThread['last_update_on'] = '';

        if ($sPermaView !== null) {
            $mConditions[] = 'AND fp.post_id = ' . (int)$sPermaView;
        }

        if (!empty($aThread['poll_id']) && Phpfox::isModule('poll')) {
            $aThread['poll'] = Phpfox::getService('poll')->getPollByUrl((int)$aThread['poll_id'], false, false, false,
                true);
            $aThread['poll']['bCanEdit'] = false;
            $aThread['poll']['bCanDelete'] = false;
            $aThread['poll']['canViewResult'] = ((Phpfox::getUserParam('poll.can_view_user_poll_results_own_poll') && $aThread['poll']['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('poll.can_view_user_poll_results_other_poll'));
            $aThread['poll']['canViewResultVote'] = isset($aThread['poll']['user_voted_this_poll']) && ($aThread['poll']['user_voted_this_poll'] == false && Phpfox::getUserParam('poll.view_poll_results_before_vote')) || ($aThread['poll']['user_voted_this_poll'] == true && Phpfox::getUserParam('poll.view_poll_results_after_vote'));
            $aThread['poll']['canVotesWithCloseTime'] = $aThread['poll']['close_time'] == 0 || $aThread['poll']['close_time'] > PHPFOX_TIME;
        }

        (($sPlugin = Phpfox_Plugin::get('forum.service_thread_getthread_query')) ? eval($sPlugin) : false);

        if (!isset($bLeftJoinQuery)) {
            $bLeftJoinQuery = false;
        }

        $theJoins = function () use ($bLeftJoinQuery) {
            if (isset($bLeftJoinQuery) && $bLeftJoinQuery !== false) {
                $this->database()->leftJoin(Phpfox::getT('user'), 'u',
                    'u.user_id = fp.user_id')->leftJoin(Phpfox::getT('user_field'), 'uf', 'uf.user_id = fp.user_id');
            } else {
                $this->database()->join(Phpfox::getT('user'), 'u',
                    'u.user_id = fp.user_id')->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = fp.user_id');
            }

            if (Phpfox::isModule('like')) {
                $this->database()->select('l.like_id AS is_liked, ')
                    ->leftJoin(Phpfox::getT('like'), 'l',
                        'l.type_id = \'forum_post\' AND l.item_id = fp.post_id AND l.user_id = ' . Phpfox::getUserId());
            }
        };

        if (!$iPage) {
            $theJoins();
            $aThread['post_starter'] = $this->database()->select('fp.*, ft.thank_id, ' . (Phpfox::getParam('core.allow_html') ? 'fpt.text_parsed' : 'fpt.text') . ' AS text, ' . Phpfox::getUserField() . ', u.joined, u.country_iso, uf.signature, uf.total_post')
                ->from(Phpfox::getT('forum_post'), 'fp')
                ->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
                ->leftJoin(':forum_thank', 'ft', 'ft.post_id = fp.post_id AND ft.user_id ='.(int)Phpfox::getUserId())
                ->where($mConditions)
                ->order('fp.time_stamp ASC')
                ->limit(1)
                ->get();
        }

        if (!$iPage) {
            $iPageSize = Phpfox::getParam('forum.total_posts_per_thread');
            if (Phpfox_Request::instance()->get('is_ajax_get')) {
                $iPageSize = null;
            }
            $sOrder = 'fp.time_stamp DESC';
        }

        $theJoins();
        if (isset($aThread['post_starter']) && !empty($aThread['post_starter'])) {
            $mConditions[] = ' AND fp.post_id <> ' . $aThread['post_starter']['post_id'];
        }
        $aThread['posts'] = $this->database()->select('fp.*, ft.thank_id, ' . (Phpfox::getParam('core.allow_html') ? 'fpt.text_parsed' : 'fpt.text') . ' AS text, ' . Phpfox::getUserField() . ', u.joined, u.country_iso, uf.signature, uf.total_post')
            ->from(Phpfox::getT('forum_post'), 'fp')
            ->join(Phpfox::getT('forum_post_text'), 'fpt', 'fpt.post_id = fp.post_id')
            ->leftJoin(':forum_thank', 'ft', 'ft.post_id = fp.post_id AND ft.user_id ='.(int)Phpfox::getUserId())
            ->where($mConditions)
            ->order($sOrder)
            ->limit($iPage, $iPageSize, $iCnt, false, false)
            ->execute('getSlaveRows');

        if (isset($aThread['post_starter'])) {
            $aThread['posts'][] = $aThread['post_starter'];
            $aThread['posts'] = array_reverse($aThread['posts']);
        }
        $sPostIds = '';
        $aThread['has_pending_post'] = false;

        foreach ($aThread['posts'] as $iKey => $aPost) {

            $aThread['posts'][$iKey]['count'] = Phpfox::getService('forum.post')->getPostCount($aThread['thread_id'], $aPost['post_id']) - 1;
            $aThread['posts'][$iKey]['forum_id'] = $aThread['forum_id'];
            $aThread['posts'][$iKey]['last_update_on'] = _p('last_update_on_time_stamp_by_update_user', array(
                    'time_stamp' => Phpfox::getTime(Phpfox::getParam('forum.forum_time_stamp'), $aPost['update_time']),
                    'update_user' => $aPost['update_user']
                )
            );

            $aThread['posts'][$iKey]['aFeed'] = array(
                'privacy' => 0,
                'comment_privacy' => 0,
                'like_type_id' => 'forum_post',
                'feed_is_liked' => ($aPost['is_liked'] ? true : false),
                'item_id' => $aPost['post_id'],
                'user_id' => $aPost['user_id'],
                'total_like' => $aPost['total_like'],
                'feed_link' => Phpfox::permalink('forum.thread', $aThread['thread_id'],
                        $aThread['title']) . 'view_' . $aPost['post_id'] . '/',
                'feed_title' => $aThread['title'],
                'feed_display' => 'mini',
                'feed_total_like' => $aPost['total_like'],
                'report_module' => 'forum_post',
                'report_phrase' => _p('report_this_post'),
                'force_report' => true,
                'time_stamp' => $aPost['time_stamp'],
                'type_id' => 'forum_post',
                'disable_like_function' => Phpfox::getParam('forum.enable_thanks_on_posts')
            );
            if ($aPost['view_id'] == 1) {
                $aThread['posts'][$iKey]['pending_action'] = [
                    'message' => _p('this_post_is_waiting_for_approval_please_review_the_content'),
                    'actions' => [
                        'approve' => [
                            'is_ajax' => true,
                            'label' => _p('approve'),
                            'action' => '$.ajaxCall(\'forum.approvePost\', \'detail=true&amp;post_id='.$aPost['post_id'].'\', \'GET\'); return false;'
                        ]
                    ]
                ];
                if ((!isset($aThread['forum_is_closed']) || !$aThread['forum_is_closed']) && ((user('forum.can_edit_own_post') && $aPost['user_id'] == Phpfox::getUserId()) || user('forum.can_edit_other_posts') || Phpfox::getService('forum.moderate')->hasAccess($aThread['forum_id'], 'edit_post'))) {
                    $aThread['posts'][$iKey]['pending_action']['actions']['edit'] = [
                        'is_ajax' => true,
                        'label' => _p('edit'),
                        'action' => '$Core.box(\'forum.reply\', 800, \'id=' . $aPost['thread_id'] . '&amp;edit=' . $aPost['post_id'] . '\'); return false;'
                    ];
                }
                if (((Phpfox::getUserParam('forum.can_delete_own_post') && $aPost['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_other_posts') || Phpfox::getService('forum.moderate')->hasAccess($aThread['forum_id'], 'delete_post') || (!empty($aThread['group_id']) && (Phpfox::isModule('pages') || Phpfox::isModule('groups')) && ($sModule = Phpfox::getPagesType($aThread['group_id'])) && Phpfox::isModule($sModule) && Phpfox::getService($sModule)->isAdmin($aThread['group_id'])))) {
                    $aThread['posts'][$iKey]['pending_action']['actions']['delete'] = [
                        'is_ajax' => true,
                        'label' => _p('delete'),
                        'action' => 'return $Core.forum.deletePost(\'' . $aPost['post_id'] . '\');'
                    ];
                }
            }
            if (Phpfox::isModule('like') && Phpfox::isModule('feed')) {
                $aThread['posts'][$iKey]['aFeed']['feed_like_phrase'] = Phpfox::getService('feed')->getPhraseForLikes($aThread['posts'][$iKey]['aFeed']);
            }

            if (isset($aThread['post_starter']) && $aThread['post_starter']['post_id'] == $aPost['post_id']) {
                $iFirstPostKey = $iKey;
            }

            if ($aPost['total_attachment']) {
                $sPostIds .= $aPost['post_id'] . ',';
            }
            if ($aPost['view_id']) {
                $aThread['has_pending_post'] = true;
            }
        }
        $sPostIds = rtrim($sPostIds, ',');

        if (!empty($sPostIds)) {
            list(, $aAttachments) = Phpfox::getService('attachment')->get('attachment.item_id IN(' . $sPostIds . ') AND attachment.view_id = 0 AND attachment.category_id = \'forum\' AND attachment.is_inline = 0',
                'attachment.attachment_id DESC', false);

            $aAttachmentCache = array();
            foreach ($aAttachments as $aAttachment) {
                $aAttachmentCache[$aAttachment['item_id']][] = $aAttachment;
            }

            foreach ($aThread['posts'] as $iKey => $aPost) {
                if (isset($aAttachmentCache[$aPost['post_id']])) {
                    $aThread['posts'][$iKey]['attachments'] = $aAttachmentCache[$aPost['post_id']];
                }
            }
        }
        if (isset($aThread['post_starter']) && isset($iFirstPostKey)) {
            $aThread['post_starter'] = array_merge($aThread['post_starter'], $aThread['posts'][$iFirstPostKey]);
            $aThread['post_starter']['is_started'] = true;
            unset($aThread['posts'][$iFirstPostKey]);
        }

        return array($iCnt, $aThread);
    }
}
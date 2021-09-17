<?php
namespace Apps\Core_Forums\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Locale;
use Phpfox_Plugin;
use Phpfox_Search;
use Phpfox_Service;
use Phpfox_Template;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');


class Forum extends Phpfox_Service
{
    /**
     * @var array
     */
    private $_aForums = [];

    /**
     * @var array
     */
    private $_aLive = [];

    /**
     * @var null|int
     */
    private $_iForumId = null;

    /**
     * @var array
     */
    private $_aBuild = [];

    /**
     * @var null|int
     */
    private $_iActive = null;

    /**
     * @var array
     */
    private $_aBreadcrumbs = [];

    /**
     * @var string
     */
    private $_sParentList = '';

    /**
     * @var string
     */
    private $_sChildren = '';

    /**
     * @var bool
     */
    private $_bIsFirst = false;

    /**
     * @var bool
     */
    private $_bHasCategory = false;

    /**
     * @var array
     */
    private $_aStat = [
        'thread' => 0,
        'post' => 0
    ];

    /**
     * @var int
     */
    private $_iEditId = 0;

    /**
     * @var
     */
    private $_bNoClosed = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('forum');
    }

    /**
     * @return $this
     */
    public function live()
    {
        if (Phpfox::getParam('forum.forum_database_tracking')) {
            $this->database()->select('ftrack.item_id AS is_seen, ftrack.time_stamp AS last_seen_time, ')
                ->leftJoin(Phpfox::getT('track'), 'ftrack',
                    'ftrack.item_id = f.forum_id AND ftrack.user_id = ' . Phpfox::getUserId() . ' AND ftrack.type_id=\'forum\'');
        }

        $aLiveForums = $this->database()
            ->select('f.forum_id, f.thread_id, f.total_thread, f.total_post, f.post_id, ft.title AS thread_title, ft.title_url AS thread_title_url, ft.time_update AS thread_time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'f')
            ->leftJoin(Phpfox::getT('forum_thread'), 'ft', 'ft.thread_id = f.thread_id')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = f.last_user_id')
            ->where('f.view_id = 0')
            ->execute('getSlaveRows');

        foreach ($aLiveForums as $aForum) {
            $this->_aLive[$aForum['forum_id']] = $aForum;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function first()
    {
        $this->_bIsFirst = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCategory()
    {
        return $this->_bHasCategory;
    }

    /**
     * @param int $iActive
     *
     * @return $this
     */
    public function active($iActive)
    {
        $this->_iActive = $iActive;
        return $this;
    }

    /**
     * @param int $iId
     *
     * @return $this
     */
    public function edit($iId)
    {
        $this->_iEditId = $iId;
        return $this;
    }

    /**
     * @param $bNoClosed bool, true if you do not want to list closed forum in options list
     * @return $this
     */
    public function noClosed($bNoClosed)
    {
        $this->_bNoClosed = $bNoClosed;
        return $this;
    }

    /**
     * @param bool $bIdOnly
     * @param bool $bIsEditing
     * @param array $aValues
     * @param bool $bSelectAll
     *
     * @return string
     */
    public function getJumpTool($bIdOnly = false, $bIsEditing = false, $aValues = array(), $bSelectAll = false)
    {
        return $this->_getFromCache()->_buildJump(0, $bIdOnly, 0, $bIsEditing, $aValues, $bSelectAll);
    }

    /**
     * @param int $iForumId
     * @param bool $bIdOnly
     * @param int $iCnt
     * @param bool $bIsEditing
     * @param array $aValues
     * @param bool $bSelectAll
     *
     * @return string
     */
    private function _buildJump(
        $iForumId,
        $bIdOnly,
        $iCnt = 0,
        $bIsEditing = false,
        $aValues = array(),
        $bSelectAll = false
    ) {
        $sOptions = '';
        foreach ($this->_aForums as $aForum) {
            if ((int)$this->_iEditId > 0 && $this->_iEditId == $aForum['forum_id']) {
                continue;
            }
            if ($aForum['parent_id'] != $iForumId || ($this->_bNoClosed && $aForum['is_closed'] == 1)) {
                continue;
            }

            if (!Phpfox::getService('forum')->hasAccess($aForum['forum_id'], 'can_view_forum')) {
                continue;
            }

            $sExt = '';
            for ($i = 0; $i < $iCnt; $i++) {
                $sExt .= '&nbsp;&nbsp;&nbsp;';
            }
            $sOptions .= '<option value="' . ($bIdOnly ? $aForum['forum_id'] : Phpfox_Url::instance()->permalink('forum',
                    $aForum['forum_id'],
                    Phpfox::getSoftPhrase($aForum['name']))) . '"' . (($bSelectAll || $this->_iActive == $aForum['forum_id'] || in_array($aForum['forum_id'],
                        $aValues)) ? ' selected="selected"' : '') . '>' . $sExt . Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aForum['name'])) . '</option>' . "\n";
            $sOptions .= $this->_buildJump($aForum['forum_id'], $bIdOnly, ($iCnt + 1), $bIsEditing, $aValues,
                $bSelectAll);
        }

        return $sOptions;
    }

    /**
     * Checks if a user has access to a forum based on their user group and on the
     * variable that represents the feature they are trying to use.
     *
     * @param int $iForumId Forum ID#
     * @param string $sVar Variable name for the rule.
     *
     * @return bool TRUE if can use the feature, FALSE if user cannot use the feature.
     */
    public function hasAccess($iForumId, $sVar)
    {
        static $aForumPerms = array();

        if (!isset($aForumPerms[$iForumId][Phpfox::getUserBy('user_group_id')])) {
            $sCacheId = $this->cache()->set('forum_group_permission_' . Phpfox::getUserBy('user_group_id') . '_' . $iForumId);
            if (!($aPerms = $this->cache()->get($sCacheId))) {
                $aUserGroupPerms = array();
                $aRows = $this->database()->select('*')
                    ->from(Phpfox::getT('forum_access'))
                    ->where('forum_id = ' . (int)$iForumId . ' AND user_group_id = ' . (int)Phpfox::getUserBy('user_group_id'))
                    ->execute('getSlaveRows');
                foreach ($aRows as $aRow) {
                    $aUserGroupPerms[$aRow['var_name']] = ($aRow['var_value'] ? true : false);
                }

                foreach ($this->getAccess() as $sPerm => $aPerm) {
                    if (isset($aUserGroupPerms[$sPerm])) {
                        $aPerms[$sPerm] = $aUserGroupPerms[$sPerm];

                        continue;
                    }

                    $aPerms[$sPerm] = $aPerm['value'];
                }

                $this->cache()->save($sCacheId, $aPerms);
            }

            if ($sPlugin = Phpfox_Plugin::get('forum.service_forum_hasaccess')) {
                eval($sPlugin);
            }

            $aForumPerms[$iForumId][Phpfox::getUserBy('user_group_id')] = $aPerms;
        }

        (($sPlugin = Phpfox_Plugin::get('forum.service_forum_hasaccess_check')) ? eval($sPlugin) : false);

        if (isset($bForceReturn)) {
            return $bForceReturn;
        }

        return (isset($aForumPerms[$iForumId][Phpfox::getUserBy('user_group_id')][$sVar]) ? $aForumPerms[$iForumId][Phpfox::getUserBy('user_group_id')][$sVar] : true);
    }

    /**
     * Get all the user group forum access params.
     *
     * @return array
     */
    public function getAccess()
    {
        $aPerms = array(
            'can_start_thread' => [
                'phrase' => _p('can_start_a_new_discussion'),
                'value' => true
            ],
            'can_view_forum' => array(
                'phrase' => _p('can_view_forum'),
                'value' => true
            ),
            'can_view_thread_content' => array(
                'phrase' => _p('can_view_thread_content'),
                'value' => true
            )
        );

        if ($sPlugin = Phpfox_Plugin::get('forum.service_forum_getaccess')) {
            eval($sPlugin);
        }

        return $aPerms;
    }

    /**
     * @return $this
     * @param $bNoCache
     */
    private function _getFromCache($bNoCache = false)
    {
        static $bIsSet = false;

        if ($bIsSet === true) {
            return $this;
        }

        $sCacheId = $this->cache()->set('forum');

        if (!($this->_aForums = $this->cache()->get($sCacheId)) || $bNoCache) {
            $aForums = $this->database()->select('f.forum_id, f.parent_id, f.view_id, f.is_category, f.name, f.name_url, f.description, f.is_closed')
                ->from($this->_sTable, 'f')
                ->where('f.view_id = 0')
                ->order('f.ordering ASC')
                ->execute('getSlaveRows');

            foreach ($aForums as $aForum) {
                $aModerators = $this->database()->select(Phpfox::getUserField())
                    ->from(Phpfox::getT('forum_moderator'), 'fm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = fm.user_id')
                    ->where('forum_id = ' . $aForum['forum_id'])
                    ->execute('getSlaveRows');

                foreach ($aModerators as $iModKey => $aModerator) {
                    foreach ($aModerator as $sKey => $sValue) {
                        $aForum['moderators'][$iModKey][$sKey] = $sValue;
                    }
                }

                $this->_aForums[$aForum['forum_id']] = $aForum;
            }

            $this->cache()->save($sCacheId, $this->_aForums);
        }

        if (is_bool($this->_aForums)) {
            $this->_aForums = array();
        }

        foreach ($this->_aForums as $key => $value) {
            $this->_aForums[$key]['toggle_class'] = (Phpfox::getCookie('forum_toggle_' . $value['forum_id']) ? '' : ' is_toggled');
        }

        $bIsSet = true;

        return $this;
    }

    /**
     * @return bool|string
     */
    public function getAdminCpList()
    {
        return $this->_getFromCache()->_buildAdminCp(0);
    }

    /**
     * @param int $iForumId
     *
     * @return bool|string
     */
    private function _buildAdminCp($iForumId)
    {
        static $iCnt = 0;

        if ($iCnt === 0 && !count($this->_aForums)) {
            return false;
        }

        $sHtml = '<ul>' . "\n";
        foreach ($this->_aForums as $aForum) {
            if ($aForum['parent_id'] != $iForumId) {
                continue;
            }

            $iCnt++;

            $sHtml .= '<li><i class="fa fa-sort" style="padding-right: 10px;"></i> <input type="hidden" name="order[' . $aForum['forum_id'] . ']" value="' . $iCnt . '" /> <a href="#?id=' . $aForum['forum_id'] . '" class="js_drop_down">' . Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aForum['name'])) . '</a>';
            $sHtml .= $this->_buildAdminCp($aForum['forum_id']) . '</li>' . "\n";
        }
        $sHtml .= '</ul>' . "\n";

        return $sHtml;
    }

    /**
     * @return array
     */
    public function getForums()
    {
        $aForums = $this->_getFromCache()->_buildForum(0);

        $this->_aLive = [];
        if ($this->_iForumId !== null && isset($this->_aBuild[$this->_iForumId])) {
            return $this->_aBuild[$this->_iForumId]['sub_forum'];
        }

        return $aForums;
    }

    /**
     * @param int $iForumId
     *
     * @return array
     */
    private function _buildForum($iForumId)
    {
        $aForums = array();
        foreach ($this->_aForums as $aForum) {
            if ($aForum['parent_id'] != $iForumId) {
                continue;
            }

            if (!Phpfox::getService('forum')->hasAccess($aForum['forum_id'], 'can_view_forum')) {
                continue;
            }

            if ($aForum['is_category'] && $this->_bHasCategory === false) {
                $this->_bHasCategory = true;
            }

            $aForum['sub_forum'] = $this->_buildForum($aForum['forum_id']);

            if (isset($this->_aLive[$aForum['forum_id']])) {
                foreach ($this->_aLive[$aForum['forum_id']] as $sKey => $mValue) {
                    if (isset($aForum[$sKey])) {
                        continue;
                    }

                    $aForum[$sKey] = $mValue;
                }

                if (!isset($aForum['is_seen'])) {
                    $aForum['is_seen'] = 0;
                }

                if (!$aForum['is_seen']) {
                    // User has signed up after the post so they have already seen the post
                    if ((Phpfox::isUser() && Phpfox::getUserBy('joined') > $aForum['thread_time_stamp']) || (!Phpfox::isUser() && Phpfox::getCookie('visit') > $aForum['thread_time_stamp'])) {
                        $aForum['is_seen'] = 1;
                    } elseif (($iLastTimeViewed = Phpfox::getLib('session')->getArray('forum_view',
                            $aForum['thread_id'])) && (int)$iLastTimeViewed > $aForum['thread_time_stamp']
                    ) {
                        $aForum['is_seen'] = 1;
                    } // Checks if the post is older then our default active post time limit
                    elseif (!empty($aForum['thread_time_stamp']) && ((PHPFOX_TIME - Phpfox::getParam('forum.keep_active_posts') * 60) > $aForum['thread_time_stamp'])) {
                        $aForum['is_seen'] = 1;
                    } elseif (!empty($aForum['thread_time_stamp']) && Phpfox::isUser() && $aForum['thread_time_stamp'] < Phpfox::getCookie('last_login')) {
                        $aForum['is_seen'] = 1;
                    }
                } else {
                    // New post was added
                    if ($aForum['thread_time_stamp'] > $aForum['last_seen_time']) {
                        $aForum['is_seen'] = 0;
                    }
                }

                if (!$aForum['parent_id']) {
                    $this->_aStat['thread'] += $aForum['total_thread'];
                    $this->_aStat['post'] += $aForum['total_post'];
                }
            }

            $aForums[$aForum['forum_id']] = $aForum;

            if ($this->_iForumId !== null && $aForum['forum_id'] == $this->_iForumId) {
                $this->_aBuild[$aForum['forum_id']] = $aForum;
            }

        }

        return $aForums;
    }

    /**
     * @return array
     * @param $bNoCache bool
     */
    public function getParents($bNoCache = false)
    {
        $this->_getFromCache($bNoCache);

        $this->_sParentList = '';

        if ($this->_aForums[$this->_iForumId]['parent_id'] > 0) {
            $this->_getParents($this->_aForums[$this->_iForumId]['parent_id']);
        }
        $this->_sParentList .= $this->_aForums[$this->_iForumId]['forum_id'];

        return explode(',', $this->_sParentList);
    }

    /**
     * @param int $iId
     */
    private function _getParents($iId)
    {
        if (isset($this->_aForums[$iId])) {
            if ($this->_aForums[$iId]['parent_id'] > 0) {
                $this->_getParents($this->_aForums[$iId]['parent_id']);
            }
            $this->_sParentList .= $this->_aForums[$iId]['forum_id'] . ',';
        }
    }

    /**
     * @return array
     */
    public function getStats()
    {
        return $this->_aStat;
    }

    /**
     * @param int $iId
     *
     * @return array|int|string
     */
    public function getForumUrl($iId)
    {
        return $this->database()->select('name_url')
            ->from($this->_sTable)
            ->where('forum_id = ' . (int)$iId)
            ->execute('getSlaveField');
    }

    /**
     * @param int $iId
     *
     * @return array|bool|int|mixed|string
     */
    public function getForEdit($iId)
    {
        if ($iId == 0) {
            return false;
        }
        $sCacheId = $this->cache()->set('forum_forum_edit_' . (int)$iId);
        if (!$aForum = $this->cache()->get($sCacheId)) {
            $aForum = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('forum_id = ' . (int)$iId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aForum);
        }
        return $aForum;
    }

    /**
     * @param bool $bIsSearchQuery
     * @param int $forumId
     * @param array $aParam
     *
     * @param string $sView
     * @return Phpfox_Search
     */
    public function getSearchFilter($bIsSearchQuery = false, $forumId = 0, $aParam = array(), $sView = '')
    {
        $aPages = array(5, 10, 15, 20);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

        $aSorts = array(
            'ft.time_stamp' => _p('post_time'),
            'u.full_name' => _p('author'),
            'ft.total_post' => _p('replies'),
            'ft.title' => _p('subject'),
            'ft.total_view' => _p('views')
        );

        $aFilters = array(
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '5'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'ft.time_stamp'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => 'DESC'
            ),
            'keyword' => array(
                'type' => 'input:text',
                'size' => '40'
            ),
            'user' => array(
                'type' => 'input:text',
                'size' => '40'
            ),
            'result' => array(
                'type' => 'input:radio',
                'options' => array(
                    '0' => _p('threads'),
                    '1' => _p('posts')
                )
            ),
            'days_prune' => array(
                'type' => 'select',
                'options' => array(
                    '1' => _p('last_day'),
                    '2' => _p('last_2_days'),
                    '7' => _p('last_week'),
                    '10' => _p('last_10_days'),
                    '14' => _p('last_2_weeks'),
                    '30' => _p('last_month'),
                    '45' => _p('last_45_days'),
                    '60' => _p('last_2_months'),
                    '75' => _p('last_75_days'),
                    '100' => _p('last_100_days'),
                    '365' => _p('last_year'),
                    '-1' => _p('beginning')
                ),
                'default_view' => '-1'
            )
        );

        //search forum of pages supported module
        $aCustomFilters = array();
        if ($sView == '') {
            $aCustomFilters[_p('default_show')] = array(
                'param' => 'result',
                'default_phrase' => (Phpfox::getParam('forum.default_search_type',
                        'posts') == 'posts') ? _p('show_posts') : _p('show_threads'),
                'data' => array(
                    array(
                        'link' => 'threads',
                        'phrase' => _p('show_threads')
                    ),
                    array(
                        'link' => 'posts',
                        'phrase' => _p('show_posts')
                    ),
                ),
            );
        }
        $aCustomFilters[_p('sort')] = array(
            'param' => 'sort',
            'default_phrase' => _p('post_time'),
            'data' => array(
                array(
                    'link' => 'time_stamp',
                    'phrase' => _p('post_time')
                ),
                array(
                    'link' => 'full_name',
                    'phrase' => _p('author')
                ),
                array(
                    'link' => 'total_post',
                    'phrase' => _p('total_replies')
                ),
                array(
                    'link' => 'title',
                    'phrase' => _p('subject')
                ),
                array(
                    'link' => 'total_view',
                    'phrase' => _p('total_views')
                ),
            )
        );
        $aCustomFilters[_p('sort_by')] = array(
            'param' => 'sort_by',
            'default_phrase' => _p('descending'),
            'data' => array(
                array(
                    'link' => 'DESC',
                    'phrase' => _p('descending')
                ),
                array(
                    'link' => 'ASC',
                    'phrase' => _p('ascending')
                ),
            )
        );

        $aSettings = array(
            'type' => 'forum',
            'filters' => $aFilters,
            'search_tool' => array(
                'table_alias' => 'ft',
                'search' => array(
                    'action' => (!empty($aParam) && isset($aParam['module_id']) ? $aParam['url'] . 'forum/' : Phpfox_Url::instance()->makeUrl('forum.search')),
                    'hidden' => '<input type="hidden" class="not_remove" name="forum_id" value="' . htmlspecialchars($forumId) . '"><input type="hidden" class="not_remove" name="view" value="' . $sView . '">',
                    'default_value' => _p('search_this_forum'),
                    'name' => 'search',
                    'field' => array('ft.title', 'fp.title', 'fpt.text')
                ),
                'show' => $aPages,
                'custom_filters' => $aCustomFilters,
                'no_filters' => array(_p('sort'), _p('when'))
            ),
            'field' => array(
                'depend' => 'result',
                'fields' => array('fp.post_id', 'ft.thread_id')
            )
        );

        if ($bIsSearchQuery) {
            $aSettings['search'] = array(
                'keyword',
                'user'
            );
        }

        return Phpfox_Search::instance()->set($aSettings);
    }

    /**
     * @param int $iId
     *
     * @return array|bool
     */
    public function getForRss($iId)
    {
        if (!Phpfox::isModule('rss')) {
            return [];
        }
        $aForum = $this->id($iId)->getForum();

        if ($aForum === false) {
            return false;
        }

        $aItems = Phpfox::getService('forum.thread')->getForRss(Phpfox::getParam('rss.total_rss_display'),
            ($aForum['forum_id'] . (is_array($this->getChildren()) ? ',' . implode(',', $this->getChildren()) : '')));

        $aRss = array(
            'href' => Phpfox_Url::instance()->makeUrl('forum', array($aForum['name_url'])),
            'title' => _p('latest_threads_in') . ': ' . Phpfox::getSoftPhrase($aForum['name']),
            'description' => _p('latest_threads_on') . ': ' . Phpfox::getParam('core.site_title'),
            'items' => $aItems
        );

        return $aRss;
    }

    /**
     * @return bool|mixed
     */
    public function getForum()
    {
        $this->_getFromCache();

        if (!isset($this->_aForums[$this->_iForumId])) {
            return false;
        }
        $aForum = $this->_aForums[$this->_iForumId];
        $this->_getBreadcrumb($aForum['forum_id']);
        $aForum['breadcrumb'] = $this->_aBreadcrumbs;

        return $aForum;
    }

    /**
     * @param int $iId
     */
    private function _getBreadcrumb($iId)
    {
        if (isset($this->_aForums[$iId])) {
            if ($this->_aForums[$iId]['parent_id'] > 0) {
                $this->_getBreadcrumb($this->_aForums[$iId]['parent_id']);
            }
            $this->_aBreadcrumbs[] = [
                Phpfox_Locale::instance()->convert($this->_aForums[$iId]['name']),
                Phpfox_Url::instance()
                    ->permalink('forum', $this->_aForums[$iId]['forum_id'], $this->_aForums[$iId]['name'])
            ];
        }
    }

    /**
     * @param int $iForumId
     *
     * @return $this
     */
    public function id($iForumId)
    {
        $this->_iForumId = $iForumId;
        return $this;
    }

    /**
     * @return array|bool
     */
    public function getChildren()
    {
        $this->_getFromCache();

        $this->_sChildren = '';
        $this->_getChildren($this->_iForumId);
        $this->_sChildren = rtrim($this->_sChildren, ',');

        if (empty($this->_sChildren)) {
            return false;
        }

        return explode(',', $this->_sChildren);
    }

    /**
     * @param int $iParent
     *
     * @return void
     */
    private function _getChildren($iParent)
    {
        foreach ($this->_aForums as $aForum) {
            if ($aForum['parent_id'] == $iParent) {
                $this->_sChildren .= $aForum['forum_id'] . ',';

                if ($this->_bIsFirst === false) {
                    $this->_getChildren($aForum['forum_id']);
                }
            }
        }
    }

    /**
     * @param int $iForumId
     *
     * @return bool
     */
    public function isPrivateForum($iForumId)
    {
        $aUserGroups = $this->database()->select('user_group_id')
            ->from(Phpfox::getT('user_group'))
            ->execute('getSlaveRows');
        $aPerms = array();
        foreach ($aUserGroups as $aUserGroup) {
            $aPerms[] = $this->getUserGroupAccess($iForumId, $aUserGroup['user_group_id']);
        }

        $bIsPrivate = false;
        foreach ($aPerms as $aPerm) {
            if (!$aPerm['can_view_forum']['value']) {
                $bIsPrivate = true;
                break;
            }
        }

        return $bIsPrivate;
    }

    /**
     * Get user group access for a specific user group and forum.
     *
     * @param int $iForumId Forum ID#
     * @param int $iUserGroupId User group ID#
     *
     * @return array
     */
    public function getUserGroupAccess($iForumId, $iUserGroupId)
    {
        $aPerms = $this->getAccess();
        $cache = cache('forum/access/' . $iForumId . '/' . $iUserGroupId);
        if (!($aRows = $cache->get())) {
            $aRows = $this->database()->select('forum_id, var_name, var_value')
                ->from(Phpfox::getT('forum_access'))
                ->where('forum_id = ' . (int)$iForumId . ' AND user_group_id = ' . (int)$iUserGroupId)
                ->execute('getSlaveRows');
            foreach ($aRows as $aRow) {
                $aPerms[$aRow['var_name']]['value'] = $aRow['var_value'];
            }

            $cache->set($aPerms);
        }

        return $aPerms;
    }

    /**
     * Get a specific access rule based on the user group of the user.
     *
     * @param string $sVar Variable for the rule.
     *
     * @return bool|string FALSE if rule does not exist.|String of forum ID# if rule exists.
     */
    public function getCanViewForumAccess($sVar)
    {
        $sForums = '';
        $aRows = $this->database()->select('forum_id, var_value')
            ->from(Phpfox::getT('forum_access'))
            ->where('user_group_id = ' . (int)Phpfox::getUserBy('user_group_id') . ' AND var_name = \'' . $sVar . '\'')
            ->execute('getSlaveRows');
        foreach ($aRows as $aRow) {
            if (!$aRow['var_value']) {
                $sForums .= $aRow['forum_id'] . ',';
            }
        }
        $sForums = rtrim($sForums, ',');

        return (empty($sForums) ? false : $sForums);
    }

    /**
     * @return void
     */
    public function buildMenu()
    {
        $iMyThreadTotal = Phpfox::getService('forum.thread')->getMyThreadTotal();
        $aFilterMenu = [
            _p('forums') => '',
            _p('new_posts') => 'forum.search.view_new',
            _p('my_threads') . (($iMyThreadTotal) ? '<span class="my count-item">' . ($iMyThreadTotal > 99 ? '99+' : $iMyThreadTotal) . '</span>' : '') => 'forum.search.view_my-thread',
            _p('subscribed_threads') => 'forum.search.view_subscribed'
        ];

        if (Phpfox::getUserParam('forum.can_approve_forum_thread')) {
            $iPendingThreads = Phpfox::getService('forum.thread')->getPendingThread();
            if ($iPendingThreads) {
                $aFilterMenu[_p('pending_threads') . '<span id="thread_pending" class="pending count-item">' . ($iPendingThreads > 99 ? '99+' : $iPendingThreads) . '</span>'] = 'forum.search.view_pending-thread';
            }
        }
        if (Phpfox::getUserParam('forum.can_approve_forum_post')) {
            $iPendingPosts = Phpfox::getService('forum.post')->getPendingPost();
            if ($iPendingPosts) {
                $aFilterMenu[_p('pending_posts') . '<span id="post_pending" class="pending count-item">' . ($iPendingPosts > 99 ? '99+' : $iPendingPosts) . '</span>'] = 'forum.search.view_pending-post';
            }
        }

        Phpfox_Template::instance()->buildSectionMenu('forum', $aFilterMenu);
    }

    /**
     * @param array $aItem
     *
     * @return array|int|string
     */
    public function getInfoForAction($aItem)
    {
        if (is_numeric($aItem)) {
            $aItem = array('item_id' => $aItem);
        }
        $aRow = $this->database()->select('p.post_id, p.thread_id, p.title, pt.text_parsed, p.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('forum_post'), 'p')
            ->join(Phpfox::getT('forum_post_text'), 'pt', 'pt.post_id = p.post_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.post_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow['title'])) {
            $aRow['title'] = $aRow['text_parsed'];
        }

        $aRow['link'] = Phpfox_Url::instance()->permalink('forum.thread', $aRow['thread_id'], $aRow['title']);
        return $aRow;
    }

    /**
     * User groups may be denied access to specific forums. This function returns the forums for which this user group does not have access, be it because cant view the forum or the contents of the threads. It is used in the controller forum.forum to filter searches by newest reply
     *
     * @return array
     */
    public function getForbiddenForums()
    {
        $cache = cache('forum/forbidden/' . Phpfox::getUserBy('user_group_id'));
        if (!($aOut = $cache->get())) {
            $aForums = $this->database()->select('forum_id')
                ->from(Phpfox::getT('forum_access'))
                ->where('var_value = 0 AND var_name = \'can_view_forum\' AND user_group_id = ' . Phpfox::getUserBy('user_group_id'))
                ->execute('getSlaveRows');

            foreach ($aForums as $aForum) {
                $aOut[] = $aForum['forum_id'];
            }
            $cache->set($aOut);
        }

        if (is_bool($aOut)) {
            $aOut = [];
        }

        return $aOut;
    }

    /**
     * @return int
     */
    public function getSponsorPrice()
    {
        $aPrice = Phpfox::getUserParam('forum.forum_thread_sponsor_price');

        //Get default currency of user
        $sCurrency = $this->database()->select('default_currency')
            ->from(':user_field')
            ->where('user_id=' . (int)Phpfox::getUserId())
            ->execute('getSlaveField');
        if (!isset($sCurrency) || empty($sCurrency)) {
            //Get default currency of site if user don't set
            $sCurrency = Phpfox::getService('core.currency')->getDefault();
        }

        if (empty($sCurrency)) {
            return 0;
        }

        if (isset($aPrice[$sCurrency])) {
            return $aPrice[$sCurrency];
        } else {
            return 0;
        }
    }

    /**
     * @param $iForumId
     * @return array|int|string
     */
    public function getTotalSubBelongToForum($iForumId)
    {
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('parent_id =' . (int)$iForumId)
            ->execute('getSlaveField');
    }

    /**
     * @return $this
     */
    public function clearBreadCrumb()
    {
        $this->_aBreadcrumbs = [];
        return $this;
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
        if ($sPlugin = Phpfox_Plugin::get('forum.service_forum__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Forums\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Database;
use Phpfox_Error;
use Phpfox_Locale;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ForumController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('forum.can_view_forum', true);

        $aParentModule = $this->getParam('aParentModule');

        $bIsSearch = ($this->request()->get('search') || $this->request()->get('s') ? true : false);
        $aCallback = $this->getParam('aCallback', null);
        $sView = $this->request()->get('view');
        $bIsPendingSearch = ($sView == 'pending-post') ? true : false;
        $aSearchValues = [
            'user' => '',
            'adv_search' => 0,
        ];
        $aSearchForumId = [];
        $bIsTagSearch = false;
        $bIsModuleTagSearch = false;
        if ($this->request()->get('req2') == 'tag' && $this->request()->get('req3')) {
            $bIsSearch = true;
            $bIsTagSearch = true;
        }

        if ($this->request()->get('req2') == 'tag' && $this->request()->get('req5') && $this->request()->get('module')) {
            if ($aCallback = Phpfox::getService('group')->getGroup($this->request()->get('item'))) {
                $bIsSearch = true;
                $bIsTagSearch = true;
                $bIsModuleTagSearch = true;
                $aCallback['url_home'] = 'group.' . $aCallback['title_url'] . '.forum';
            }
        }

        $oSearch = Phpfox::getService('forum')->getSearchFilter($this->getParam('bIsSearchQuery', false),
            ($this->request()->get('forum_id') ? $this->request()->get('forum_id') : $this->request()->getInt('req2')),
            $aParentModule, $sView);
        $iResult = $oSearch->get('result');
        if (!$iResult && $oSearch->isSearch()) {
            $iResult = ($this->request()->get('result') == 'threads') ? 0 : (($this->request()->get('result') == 'posts') ? 1 : null);
        }
        if ((Phpfox::getParam('forum.default_search_type',
                'posts') == 'posts' && $iResult === null && !in_array($sView,
                ['pending-thread', 'my-thread', 'subscribed']) || in_array($sView, ['pending-post', 'new']))
        ) {
            $bForceResult = true;
        }

        $bIsAdvSearch = ($oSearch->get('adv_search') ? true : false);
        $module_id = !empty($aParentModule['module_id']) ? $aParentModule['module_id'] : null;

        if (empty($module_id) && $oSearch->isSearch()) {
            $aIds = [];

            if ($bIsAdvSearch) {
                $aForums = ($oSearch->get('forum')) ? $oSearch->get('forum') : [];
                $aSearchForumId = array_unique((!empty($aForums) && is_array($aForums)) ? $aForums : []);
                foreach ($aSearchForumId as $iSearchForum) {
                    if (!is_numeric($iSearchForum)) {
                        continue;
                    }
                    $aIds[] = $iSearchForum;
                }
            } else {
                $iSearchForumId = $this->request()->get('forum_id') ? $this->request()->get('forum_id') : ($this->request()->getInt('req2') ? $this->request()->getInt('req2') : '');
                $aForums = ($iSearchForumId ? Phpfox::getService('forum')->id($iSearchForumId)->live()->getForums() : Phpfox::getService('forum')->live()->getForums());
                if ($iSearchForumId) {
                    $aIds[] = $iSearchForumId;
                }
                foreach ($aForums as $aForum) {
                    if ($aForum['forum_id']) {
                        $aIds[] = $aForum['forum_id'];
                    }

                    $aChilds = (array)Phpfox::getService('forum')->id($aForum['forum_id'])->getChildren();
                    foreach ($aChilds as $iId) {
                        if ($iId) {
                            $aIds[] = $iId;
                        }
                    }
                }
            }

            $aIds = Phpfox::getService('forum.thread')->getCanViewForumIdList($aIds);

            $oSearch->setCondition('AND ft.forum_id IN(' . implode(',', $aIds) . ')');

        } elseif (!empty($module_id)) {
            $oSearch->setCondition('AND ft.forum_id = 0 AND ft.group_id = ' . $aParentModule['item_id'] . ' AND ft.is_announcement = 0');
        }

        $iPage = $this->request()->getInt('page');
        $iPageSize = $oSearch->getDisplay();
        $iForumId = 0;
        $sViewId = 'ft.view_id = 0';
        if ($aCallback === null) {
            $iForumId = $this->request()->getInt('req2');
        }
        if ($aParentModule == null) {
            $iForumId = $this->request()->getInt('req2');

            $aForums = Phpfox::getService('forum')->live()->id($iForumId)->getForums();
            $aForum = Phpfox::getService('forum')->id($iForumId)->getForum();
            $this->template()->assign('isSubForumList', true);
        } else {
            $aForum = [];
            $aForums = [];
        }

        if (!$bIsSearch && $sView != 'pending-post') {
            if ($aParentModule === null) {
                if (!isset($aForum['forum_id']) && empty($sView)) {

                    return Phpfox_Error::display(_p('not_a_valid_forum'));
                }

                if (isset($aForum['forum_id'])) {
                    $this->setParam('iActiveForumId', $aForum['forum_id']);
                }

                if (!empty($sView)) {
                    switch ($sView) {
                        case 'my-thread':
                            $oSearch->setCondition('AND ft.user_id = ' . Phpfox::getUserId());
                            $sViewId = 'ft.view_id >= 0';
                            break;
                        case 'pending-thread':
                            if (Phpfox::getUserParam('forum.can_approve_forum_thread')) {
                                $sViewId = 'ft.view_id = 1';
                            }
                            $bIsPendingSearch = true;
                            break;
                        default:
                            break;
                    }

                    $oSearch->setCondition(($bIsPendingSearch ? 'AND ' : 'AND ft.group_id = 0 AND ft.is_announcement = 0 AND ') . $sViewId);

                    $bIsSearch = true;
                } else {
                    $oSearch->setCondition('AND ft.forum_id = ' . $aForum['forum_id'] . ' AND ft.group_id = 0 AND ' . $sViewId . ' AND ft.is_announcement = 0');
                }
            } else {
                $oSearch->setCondition('AND ft.forum_id = 0 AND ft.group_id = ' . $aParentModule['item_id'] . ' AND ' . $sViewId . ' AND ft.is_announcement = 0');
            }

            // get the forums that we cant access
            $aForbiddenForums = Phpfox::getService('forum')->getForbiddenForums();
            if (!empty($aForbiddenForums)) {
                $oSearch->setCondition(' AND ft.forum_id NOT IN (' . implode(',', $aForbiddenForums) . ')');
            }
        } else {
            if ($aParentModule !== null) {
                $oSearch->setCondition('AND ft.forum_id = 0 AND ft.group_id = ' . $aParentModule['item_id'] . ' AND ' . $sViewId . ' AND ft.is_announcement = 0');
            } else {
                $oSearch->setCondition(($aForum ? 'AND ft.forum_id = ' . $aForum['forum_id'] : '') . ' AND ' . $sViewId . ($bIsPendingSearch ? '' : ' AND ft.is_announcement = 0 AND ft.group_id = 0'));
            }
        }


        if ($bIsAdvSearch) {
            if ($oSearch->get('user')) {
                $oSearch->search('like%', 'u.full_name', $oSearch->get('user'));
            }
            $aSearchValues = array_merge($aSearchValues, $oSearch->get());
        }
        $sSort = ($this->request()->get('sort') || $this->request()->get('sort_by')) ? $this->_getSort() : $oSearch->getSort();

        if ($iResult || isset($bForceResult)) {
            if ($sView == 'pending-post') {
                $bIsSearch = true;
                $bForceResult = true;
                $oSearch->clearConditions();
                $oSearch->setCondition('AND fp.view_id = 1');
            }
            if ($bIsTagSearch === true) {
                if ($bIsModuleTagSearch) {
                    $oSearch->setCondition("AND ft.group_id = " . (int)$aCallback['item_id'] . " AND tag.tag_url = '" . Phpfox_Database::instance()->escape(urldecode($this->request()->get('req5'))) . "'");
                } else {
                    $oSearch->setCondition("AND ft.group_id = 0 AND tag.tag_url = '" . Phpfox_Database::instance()->escape(urldecode($this->request()->get('req3'))) . "'");
                }
            }
            if ($oSearch->get('search')) {
                $oSearch->search('like%', ['fp.title', 'fpt.text'], $oSearch->get('search'));
            }

            list($iCnt, $aThreads) = Phpfox::getService('forum.post')->callback($aCallback)
                ->isTagSearch($bIsTagSearch)
                ->isNewSearch(($sView == 'new' ? true : false))
                ->isSubscribeSearch(($sView == 'subscribed' ? true : false))
                ->isModuleSearch($bIsModuleTagSearch)
                ->get($oSearch->getConditions(), $sSort, $oSearch->getPage(), $iPageSize);
        } else {
            if (($iDaysPrune = $oSearch->get('days_prune')) && $iDaysPrune != '-1') {
                $oSearch->setCondition('AND ft.time_stamp >= ' . (PHPFOX_TIME - ($iDaysPrune * 86400)));
            }

            if ($bIsTagSearch === true) {
                if ($bIsModuleTagSearch) {
                    $oSearch->setCondition("AND ft.group_id = " . (int)$aCallback['item_id'] . " AND tag.tag_url = '" . Phpfox_Database::instance()->escape(urldecode($this->request()->get('req5'))) . "'");
                } else {
                    $oSearch->setCondition("AND ft.group_id = 0 AND tag.tag_url = '" . Phpfox_Database::instance()->escape(urldecode($this->request()->get('req3'))) . "'");
                }
            }

            if ($oSearch->get('search')) {
                $oSearch->search('like%', ['ft.title'], $oSearch->get('search'));
            }

            list($iCnt, $aThreads) = Phpfox::getService('forum.thread')->isSearch($bIsSearch)
                ->isTagSearch($bIsTagSearch)
                ->isNewSearch(($sView == 'new' ? true : false))
                ->isSubscribeSearch(($sView == 'subscribed' ? true : false))
                ->isModuleSearch($bIsModuleTagSearch)
                ->get($oSearch->getConditions(), 'ft.order_id DESC, ' . $sSort, $oSearch->getPage(),
                    $iPageSize);
        }

        $aAccess = Phpfox::getService('forum')->getUserGroupAccess($iForumId, Phpfox::getUserBy('user_group_id'));

        Phpfox_Pager::instance()->set([
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'paging_mode' => Phpfox::getParam('forum.forum_paging_mode', 'loadmore')
        ]);

        /*
         * Check admin of forum's parent item
         */
        $bIsAdmin = false;
        if (count($aParentModule) && Phpfox::hasCallback($aParentModule['module_id'], 'isAdmin')) {
            $bIsAdmin = Phpfox::callback($aParentModule['module_id'] . '.isAdmin', $aParentModule['item_id']);
        }
        // search forum's post in pages/groups/ etc
        if (!empty($aParentModule)) {
            $this->template()->assign('sResetUrl', url(sprintf("%s.%s.forum", $aParentModule['module_id'], $aParentModule['item_id'])));
        }

        $this->template()->assign([
                'aThreads' => $aThreads,
                'iSearchId' => $this->request()->getInt('search-id'),
                'aCallback' => $aParentModule,
                'sView' => $sView,
                'aPermissions' => $aAccess,
                'aSearchValues' => $aSearchValues,
                'sForumList' => Phpfox::getService('forum')->getJumpTool(true, false, $aSearchForumId, !$bIsAdvSearch),
                'bIsAdmin' => $bIsAdmin
            ]
        )
            ->setMeta('keywords', Phpfox::getParam('forum.forum_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('forum.forum_meta_description'))
            ->setPhrase(['are_you_sure_you_want_to_delete_this_thread_permanently']);

        if ($bIsSearch && !isset($aForum['forum_id'])) {
            if (is_array($aCallback)) {
                $this->template()
                    ->setBreadCrumb(_p('pages'), $this->url()->makeUrl('pages'))
                    ->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
            } else {
                $this->template()->setBreadCrumb($this->_getBreadCrumbTitle($sView), $this->url()->makeUrl('current'));
            }

            if ($bIsTagSearch) {
                $sSearchTag = urldecode(($bIsModuleTagSearch ? $this->request()->get('req5') : $this->request()->get('req3')));
                $aTag = Phpfox::getService('tag')->getTagInfo('forum', $sSearchTag);
                if (!empty($aTag['tag_text'])) {
                    if ($bIsModuleTagSearch) {
                        $this->template()->setBreadCrumb(_p('threads_tagged_with') . ': ' . $aTag['tag_text'],
                            $this->url()->makeUrl('forum.tag.module_group.item_' . $this->request()->get('item') . '.' . $this->request()->get('req5')),
                            true);
                    } else {
                        $this->template()->setBreadCrumb(_p('tags'),
                            $this->url()->makeUrl('forum.tag'))->setBreadCrumb(_p('threads_tagged_with') . ': ' . $aTag['tag_text'],
                            $this->url()->makeUrl('forum.tag.' . $this->request()->get('req3')), true);
                    }
                }
            }

            $this->template()->assign([
                    'bIsSearch' => true,
                    'bResult' => (isset($bForceResult) ? true : $iResult),
                    'aForumResults' => $oSearch->get('forum'),
                    'bIsTagSearch' => $bIsTagSearch,
                ]
            );
        } else {
            if (Phpfox::getParam('forum.rss_feed_on_each_forum')) {
                if ($aParentModule === null) {
                    $this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('forum') . ': ' . Phpfox::getSoftPhrase($aForum['name']) . '" href="' . $this->url()->makeUrl('forum',
                            ['rss', 'forum' => $aForum['forum_id']]) . '" />');
                } else {
                    $this->template()->setHeader('<link rel="alternate" type="application/rss+xml" title="' . _p('group_forum') . ': ' . $aCallback['title'] . '" href="' . $this->url()->makeUrl('forum',
                            ['rss', 'group' => $aCallback['item_id']]) . '" />');
                }
            }

            if ($aCallback === null && $aParentModule === null) {
                if (!$aForum['is_closed'] && Phpfox::getService('forum')->hasAccess($aForum['forum_id'], 'can_start_thread') && (Phpfox::getUserParam('forum.can_add_new_thread')|| Phpfox::getService('forum.moderate')->hasAccess($aForum['forum_id'],
                        'add_thread'))
                ) {
                    $this->template()->setMenu([
                        'forum.forum' => [
                            'menu_id' => null,
                            'module' => 'forum',
                            'url' => $this->url()->makeUrl('forum.post.thread', ['id' => $aForum['forum_id']]),
                            'var_name' => 'add_thread',
                        ],
                    ]);
                }
            } else {
                if ($aParentModule !== null) {
                    $this->template()->setMenu([
                        'forum.forum' => [
                            'menu_id' => null,
                            'module' => 'forum',
                            'url' => $this->url()->makeUrl('forum.post.thread',
                                ['module' => $aParentModule['module_id'], 'item' => $aParentModule['item_id']]),
                            'var_name' => 'add_thread',
                        ],
                    ]);
                }
            }
            if ($aParentModule === null) {
                if (!Phpfox::getService('forum')->hasAccess($aForum['forum_id'], 'can_view_forum')) {
                    $this->url()->send('forum');
                }
                $this->template()->setTitle(Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aForum['name'])))
                    ->setBreadCrumb(_p('forum'),$this->url()->makeUrl('forum'))
                    ->setBreadCrumb($aForum['breadcrumb'])
                    ->assign([
                            'bDisplayThreads' => true,
                            'aAnnouncements' => Phpfox::getService('forum.thread')->getAnnoucements($iForumId),
                            'aForums' => $aForums,
                            'aForumData' => $aForum,
                            'bHasCategory' => false,
                            'bIsSubForum' => true,
                            'bIsSearch' => false,
                            'bIsTagSearch' => false,
                            'bResult' => (isset($bForceResult) ? true : $iResult),
                        ]
                    );
            } else {
                $sTitle = Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']);
                $this->template()
                    ->setTitle(_p('discussions') . ' &raquo; ' . $sTitle, true)
                    ->assign([
                            'bDisplayThreads' => true,
                            'bHasCategory' => false,
                            'bIsSubForum' => true,
                            'bIsSearch' => false,
                            'bIsTagSearch' => false,
                            'aAnnouncements' => Phpfox::getService('forum.thread')->getAnnoucements(null,
                                isset($aParentModule['item_id']) ? $aParentModule['item_id'] : 1),
                            'bResult' => (isset($bForceResult) ? true : $iResult),
                        ]
                    );
            }
        }

        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE,
                    'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission',
                    $aParentModule['item_id'], 'forum.view_browse_forum')
            ) {
                $this->template()->assign(['aSearchTool' => []]);
                return Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
            }
            $this->template()
                ->clearBreadCrumb();
            $this->template()
                ->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']),
                    $aParentModule['url'])
                ->setBreadCrumb(_p('forum'), $aParentModule['url'] . 'forum/');
        }
        $bCanDeleteAll = $bShowModerator = false;
        if ($aCallback !== null && Phpfox::hasCallback($aCallback['module_id'], 'isAdmin')) {
            $bCanDeleteAll = Phpfox::callback($aCallback['module_id'] . '.isAdmin', $aCallback['item_id']);
        }
        if (!$bCanDeleteAll) {
            $bCanDeleteAll = Phpfox::getUserParam('forum.can_delete_other_posts');
        }
        $aModerationMenu = [];
        if ($bIsSearch && (isset($bForceResult) || $iResult)) {
            if (isset($bForceResult)) {
                if ($bCanDeleteAll) {
                    $aModerationMenu[] = [
                            'phrase' => _p('delete'),
                            'action' => 'delete',
                    ];
                }
                if ($sView == 'pending-post' && Phpfox::getUserParam('forum.can_approve_forum_post')) {
                    $aModerationMenu[] = [
                        'phrase' => _p('approve'),
                        'action' => 'approve',
                    ];
                }
                if (count($aModerationMenu)) {
                    $this->setParam('global_moderation', [
                            'name' => 'forumpost',
                            'ajax' => 'forum.postModeration',
                            'menu' => $aModerationMenu,
                            'extra' => "&module_id=$aCallback[module_id]&item_id=$aCallback[item_id]"
                        ]
                    );
                    $bShowModerator = true;
                }
            } else {
                $this->template()->assign('bIsPostSearch', true);
            }
        } else {
            if ($bCanDeleteAll) {
                $aModerationMenu[] = [
                    'phrase' => _p('delete'),
                    'action' => 'delete',
                ];
            }
            if ($sView == 'pending-thread' && Phpfox::getUserParam('forum.can_approve_forum_thread')) {
                $aModerationMenu[] = [
                    'phrase' => _p('approve'),
                    'action' => 'approve',
                ];
            }

            if (count($aModerationMenu)) {
                $this->setParam('global_moderation', [
                        'name' => 'forum',
                        'ajax' => 'forum.moderation',
                        'menu' => $aModerationMenu,
                        'extra' => "&module_id=$aCallback[module_id]&item_id=$aCallback[item_id]"
                    ]
                );
                $bShowModerator = true;
            }
        }
        $this->template()->assign(['bShowModerator' => ($bShowModerator || $bIsAdmin)]);

        // set param for trending (tag) block
        $this->setParam('sTagType', 'forum');
        $this->setParam('bShowHashTag', false);

        return null;
    }

    /**
     * @return string
     */
    private function _getSort()
    {
        $sSort = $this->request()->get('sort');
        $sSortBy = $this->request()->get('sort_by', 'DESC');
        switch ($sSort) {
            case 'time_stamp':
                $sResult = 'ft.time_stamp';
                break;
            case 'full_name':
                $sResult = 'u.full_name';
                break;
            case 'total_post':
                $sResult = 'ft.total_post';
                break;
            case 'title':
                $sResult = 'ft.title';
                break;
            case 'total_view':
                $sResult = 'ft.total_view';
                break;
            default:
                $sResult = 'ft.time_stamp';
                break;

        }
        return $sResult . ' ' . $sSortBy;

    }

    private function _getBreadCrumbTitle($sView)
    {
        switch ($sView) {
            case 'new':
                $sTitle = _p('new_posts');
                break;
            case 'my-thread':
                $sTitle = _p('my_threads');
                break;
            case 'subscribed':
                $sTitle = _p('subscribed_threads');
                break;
            case 'pending-thread':
                $sTitle = _p('pending_threads');
                break;
            case 'pending-post':
                $sTitle = _p('pending_posts');
                break;
            default:
                $sTitle = _p('forum');
                break;
        }
        return $sTitle;
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_forum_clean')) ? eval($sPlugin) : false);
    }
}

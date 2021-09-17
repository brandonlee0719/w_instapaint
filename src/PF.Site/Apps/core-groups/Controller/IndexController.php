<?php

namespace Apps\PHPfox_Groups\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Core\Lib;
use Phpfox;
use Phpfox_Component;
use Phpfox_Locale;
use Phpfox_Module;
use Phpfox_Pager;
use Phpfox_Plugin;


class IndexController extends Phpfox_Component
{
    public function process()
    {
        $bIsUserProfile = $this->getParam('bIsProfile');
        $aUser = [];
        if ($bIsUserProfile) {
            $aUser = $this->getParam('aUser');
        }

        user('pf_group_browse', null, null, true);

        if (($iDeleteId = $this->request()->getInt('delete')) && Phpfox::getService('groups.process')->delete($iDeleteId)) {
            // clear cache if group's event is featured or sponsored
            if (Phpfox::isModule('event')) {
                $iEventFeatured = db()->select('COUNT(*)')->from(':event')->where('module_id = "groups" AND item_id = ' . $iDeleteId . ' AND is_featured = 1')->executeField();
                if ($iEventFeatured) {
                    \Phpfox_Cache::instance()->remove();
                }
                $iEventSponsored = db()->select('COUNT(*)')->from(':event')->where('module_id = "groups" AND item_id = ' . $iDeleteId . ' AND is_sponsor = 1')->executeField();
                if ($iEventSponsored) {
                    \Phpfox_Cache::instance()->remove();
                }
                // delete event belong to group
                db()->delete(':event', "module_id = 'groups' AND item_id = $iDeleteId");
            }

            if ($iProfileId = $this->request()->getInt('profile')) {
                $aUser = Phpfox::getService('user')->getUser($iProfileId);
                $this->url()->send($aUser['user_name'] . '.groups', [], _p('Group successfully deleted.'));
            } else {
                $this->url()->send('groups', [], _p('Group successfully deleted.'));
            }
        }

        $oGroupFacade = Phpfox::getService('groups.facade');
        $sView = $this->request()->get('view');

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }

        if ($bIsProfile) {
            section(_p('Groups'), url('/' . $aUser['user_name'] . '/groups'));
        } else {
            section(_p('Groups'), url('/groups'));
        }

        $this->search()->set([
                'type' => 'groups',
                'field' => 'pages.page_id',
                'search_tool' => [
                    'table_alias' => 'pages',
                    'search' => [
                        'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                            ['groups', 'view' => $this->request()->get('view')]) : $this->url()->makeUrl('groups',
                            ['view' => $this->request()->get('view')])),
                        'default_value' => _p('Search groups'),
                        'name' => 'search',
                        'field' => 'pages.title',
                    ],
                    'sort' => [
                        'latest' => ['pages.time_stamp', _p('Latest')],
                        'most-liked' => ['pages.total_like', _p('Most Popular')],
                    ],
                    'show' => [10, 15, 20],
                ],
            ]
        );

        $aBrowseParams = [
            'module_id' => 'groups',
            'alias' => 'pages',
            'field' => 'page_id',
            'table' => Phpfox::getT('pages'),
            'hide_view' => ['pending', 'my'],
            'select' => 'pages_type.name as type_name, '
        ];

        $aFilterMenu = [];
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            $iMyGroupsCount = Phpfox::getService('groups')->getMyPages(true, true);
            $aFilterMenu = [
                _p('All Groups') => '',
                _p('My Groups') . ($iMyGroupsCount ? '<span class="count-item">' . $iMyGroupsCount . '</span>' : '') => 'my',
            ];

            list($iTotalFriend,) = Phpfox::getService('friend')->get('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId());
            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !Phpfox::getUserBy('profile_page_id') && $iTotalFriend) {
                $aFilterMenu[_p('Friends\' Groups')] = 'friend';
            }

            if (Phpfox::getService('groups.facade')->getUserParam('can_approve_pages')) {
                $iPendingTotal = Phpfox::getService('groups')->getPendingTotal();

                if ($iPendingTotal) {
                    $aFilterMenu[_p('Pending Groups') . '<span class="count-item">' . $iPendingTotal . '</span>'] = 'pending';
                }
            }
        }
        $sView = trim($sView, '/');
        $aModerations = [];
        if ($oGroupFacade->getUserParam('can_delete_all_pages')) {
            $aModerations[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
        }
        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id IN(0,1) AND pages.user_id = ' . Phpfox::getUserId());
                break;
            case 'pending':
                Phpfox::isUser(true);
                if (Phpfox::getService('groups.facade')->getUserParam('can_approve_pages')) {
                    $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id = 1');
                    $aModerations[] = [
                        'phrase' => _p('approve'),
                        'action' => 'approve'
                    ];
                } else {
                    \Phpfox_Url::instance()->send('groups');
                }
                break;
            default:
                if (Phpfox::getUserParam('privacy.can_view_all_items')) {
                    $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id = 0');
                } else {
                    $this->search()->setCondition('AND pages.app_id = 0 AND pages.view_id = 0 AND pages.privacy IN(%PRIVACY%)');
                }
                break;
        }

        // moderations mass actions
        if (!empty($aModerations)) {
            $this->setParam('global_moderation', array(
                    'name' => 'groups',
                    'ajax' => 'groups.pageModeration',
                    'menu' => $aModerations
                )
            );

            $this->template()->assign('bShowModeration', true);
        } else {
            $this->template()->assign('bShowModeration', false);
        }

        $this->template()->buildSectionMenu('groups', $aFilterMenu);

        // add button to add new group
        if (user('pf_group_add', '0') == '1' &&
            (!defined('PHPFOX_CURRENT_TIMELINE_PROFILE') || PHPFOX_CURRENT_TIMELINE_PROFILE == Phpfox::getUserId())
        ) {
            sectionMenu(_p('Add a Group'), url('/groups/add'));
        }

        $bIsValidCategory = false;

        if ($this->request()->get('req2') == 'category' && ($iCategoryId = $this->request()->getInt('req3')) && ($aType = Phpfox::getService('groups.type')->getById($iCategoryId))) {
            $bIsValidCategory = true;
            $this->setParam('sCurrentCategory', $iCategoryId);
            $this->setParam('iParentCategoryId', $aType['type_id']);

            $sType = (Lib::phrase()->isPhrase($aType['name'])) ? _p($aType['name']) : Phpfox_Locale::instance()->convert($aType['name']);
            $this->template()->setBreadCrumb($sType, Phpfox::permalink('groups.category', $aType['type_id'],
                    $sType) . ($sView ? 'view_' . $sView . '/' . '' : ''), true);
            $this->template()->assign('aType', $aType);
        }

        if ($this->request()->get('req2') == 'sub-category' && ($iSubCategoryId = $this->request()->getInt('req3')) && ($aCategory = Phpfox::getService('groups.category')->getById($iSubCategoryId))) {
            $bIsValidCategory = true;
            $this->setParam('sCurrentCategory', $iSubCategoryId);
            $this->setParam('iParentCategoryId', $aCategory['type_id']);
            $sTypeName = (Lib::phrase()->isPhrase($aCategory['type_name'])) ? _p($aCategory['type_name']) : Phpfox_Locale::instance()->convert($aCategory['type_name']);
            $this->template()->setBreadCrumb($sTypeName, Phpfox::permalink('groups.category', $aCategory['type_id'],
                    $sTypeName) . ($sView ? 'view_' . $sView . '/' . '' : ''));
            $sCategoryName = (Lib::phrase()->isPhrase($aCategory['name'])) ? _p($aCategory['name']) : Phpfox_Locale::instance()->convert($aCategory['name']);
            $this->template()->setBreadCrumb($sCategoryName,
                Phpfox::permalink('groups.sub-category', $aCategory['category_id'],
                    $sCategoryName) . ($sView ? 'view_' . $sView . '/' . '' : ''), true);

            // set search condition
            $this->search()->setCondition('AND pages.category_id = ' . (int)$aCategory['category_id']);
        }

        if (isset($aType) && isset($aType['type_id'])) {
            $this->search()->setCondition('AND pages.type_id = ' . (int)$aType['type_id']);
        }

        if (isset($aType) && isset($aType['category_id'])) {
            $this->search()->setCondition('AND pages.category_id = ' . (int)$aType['category_id']);
        } elseif (isset($aType) && isset($aCategory) && isset($aCategory['category_id'])) {
            $this->search()->setCondition('AND pages.category_id = ' . (int)$aCategory['category_id']);
        }

        if ($bIsUserProfile) {
            if ($sView != 'all') {
                $this->search()->setCondition('AND pages.user_id = ' . (int)$aUser['user_id']);
            }
            if ($aUser['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('core.can_view_private_items')) {
                $this->search()->setCondition('AND pages.reg_method <> 2');
            }
        }

        $aPages = [];
        $aCategories = [];
        $bShowCategories = false;
        if ($this->search()->isSearch() || defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsValidCategory = true;
        }

        if ($bIsValidCategory) {
            $this->search()->setCondition(Phpfox::callback('groups.getExtraBrowseConditions', 'pages'));
            $this->search()->browse()->params($aBrowseParams)->execute(function (\Phpfox_Search_Browse $browse) {
                $browse->database()->join(':pages_type', 'pages_type',
                    'pages_type.type_id = pages.type_id AND pages_type.item_type = 1');
            });
            $aPages = $this->search()->browse()->getRows();

            foreach ($aPages as $iKey => &$aPage) {
                if (!isset($aPage['vanity_url']) || empty($aPage['vanity_url'])) {
                    $aPage['url'] = Phpfox::permalink('groups', $aPage['page_id'], $aPage['title']);
                } else {
                    $aPage['url'] = $aPage['vanity_url'];
                }

                if ($aPage['category_id']) {
                    $aPage['category_name'] = Phpfox::getService('groups.category')->getCategoryName($aPage['category_id']);
                    $aPage['category_link'] = Phpfox::permalink('groups.sub-category', $aPage['category_id'], $aPage['category_name']);
                }

                if ($aPage['type_name']) {
                    $aPage['type_link'] = Phpfox::permalink('groups.category', $aPage['type_id'], $aPage['type_name']);
                }

                list($iCnt, $aMembers) = Phpfox::getService('groups')->getMembers($aPage['page_id']);
                $aPage['members'] = $aMembers;
                $aPage['total_members'] = $iCnt;
                $aPage['remain_members'] = $iCnt - 3;
                $aPage['text_parsed'] = Phpfox::getService('groups')->getInfo($aPage['page_id'], true);
                Phpfox::getService('groups')->getActionsPermission($aPage, $sView);
            }

            $this->search()->browse()->setPagingMode(Phpfox::getParam('groups.pagination_at_search_groups',
                'loadmore'));
            Phpfox_Pager::instance()->set([
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => $this->search()->browse()->getCount(),
                'paging_mode' => $this->search()->browse()->getPagingMode()
            ]);
        } else {
            $bShowCategories = true;
            $iGroupsLimitPerCategory = Phpfox::getParam('groups.groups_limit_per_category', 0);
            $aCategories = Phpfox::getService('groups.category')->getForBrowse(0, true,
                ($sView == 'my' ? Phpfox::getUserId() : ($bIsProfile ? $aUser['user_id'] : null)),
                $iGroupsLimitPerCategory, $sView);
        }

        $iCountPage = 0;
        if (count($aCategories)) {
            foreach ($aCategories as &$aCategory) {
                if (isset($aCategory['pages']) && is_array($aCategory['pages'])) {
                    $iCountPage += count($aCategory['pages']);
                    // count number of pages that not show
                    if (isset($iGroupsLimitPerCategory) && $iGroupsLimitPerCategory && ($aCategory['total_pages'] - $iGroupsLimitPerCategory > 0)) {
                        $aCategory['remain_pages'] = $aCategory['total_pages'] - count($aCategory['pages']);
                    }
                }
            }
        }

        // no pending items in pending view => redirect to all groups
        if ($sView == 'pending' && (($bIsValidCategory && empty($aPages)) || (!$bIsValidCategory && !$iCountPage))) {
            \Phpfox_Url::instance()->send('groups');
        }

        $this->template()->assign([
            'sView' => $sView,
            'aPages' => $aPages,
            'aCategories' => $aCategories,
            'bShowCategories' => $bShowCategories,
            'iCountPage' => $iCountPage,
            'bIsSearch' => $this->search()->isSearch()
        ])->setMeta([
            'keywords' => _p('groups_meta_keywords'),
            'description' => _p('groups_meta_description')
        ]);

        $iStartCheck = 0;
        if ($bIsValidCategory == true) {
            $iStartCheck = 5;
        }
        $aRediAllow = ['category'];
        if (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE) {
            $aRediAllow[] = 'groups';
        }
        $aCheckParams = [
            'url' => $this->url()->makeUrl('groups'),
            'start' => $iStartCheck,
            'reqs' => [
                '2' => $aRediAllow,
            ],
        ];

        if (defined('PHPFOX_CURRENT_TIMELINE_PROFILE') && PHPFOX_CURRENT_TIMELINE_PROFILE) {
            $this->template()->assign('iCurrentProfileId', PHPFOX_CURRENT_TIMELINE_PROFILE);
        }

        if (Phpfox::getParam('core.force_404_check') && !Phpfox::getService('core.redirect')->check404($aCheckParams)) {
            return Phpfox_Module::instance()->setController('error.404');
        }

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}

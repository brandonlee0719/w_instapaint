<?php

namespace Apps\PHPfox_Videos\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

/**
 * Class IndexController
 * @package Apps\PHPfox_Videos\Controller
 */
class IndexController extends Phpfox_Component
{
    public function process()
    {
        user('pf_video_view', '1', null, true);
        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('video_id', 'title'),
                    'table' => 'video',
                    'redirect' => 'video',
                    'title' => $sLegacyTitle
                )
            );
        }
        if (($this->request()->get('req2') == 'delete' && ($iDeleteId = $this->request()->getInt('req3'))) || $iDeleteId = $this->request()->getInt('delete')) {
            $sView = $this->request()->get('view', '');
            $iUserId = $this->request()->getInt('user_id', 0);
            if ($sParentReturn = Phpfox::getService('v.process')->delete($iDeleteId, $sView, $iUserId)) {
                if (is_bool($sParentReturn)) {
                    $this->url()->send('video', [], _p('video_successfully_deleted'));
                } else {
                    $this->url()->forward($sParentReturn, _p('video_successfully_deleted'));
                }
            } else {
                return Phpfox_Error::display(_p('unable_to_delete_this_video'));
            }
        }

        $sView = $this->request()->get('view');
        $aParentModule = $this->getParam('aParentModule');

        $aUser = [];
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

        // add breadcrumb
        if ($bIsProfile) {
            section(_p('Videos'), url('/' . $aUser['user_name'] . '/video'));
        } else {
            section(_p('Videos'), url('/video'));
        }
        // add button to add new video
        if (!defined('PHPFOX_IS_PAGES_VIEW') && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            if (Phpfox::getUserParam('v.pf_video_share', false)) {
                sectionMenu(' ' . _p('share_a_video'), url('/video/share'));
            }
        }

        $this->search()->set([
                'type' => 'video',
                'field' => 'video.video_id',
                'search_tool' => [
                    'table_alias' => 'video',
                    'search' => [
                        'action' => ($aParentModule === null ? ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                            array('video', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('video',
                            array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'video?view=' . $this->request()->get('view')),
                        'default_value' => _p('search_videos'),
                        'name' => 'search',
                        'field' => 'video.title',
                    ],
                    'sort' => [
                        'latest' => ['video.time_stamp', _p('latest')],
                        'most-viewed' => ['video.total_view', _p('most_viewed')],
                        'most-liked' => ['video.total_like', _p('most_liked')],
                        'most-talked' => ['video.total_comment', _p('most_discussed')]
                    ],
                    'show' => [12, 18, 24],
                ],
            ]
        );

        $aBrowseParams = [
            'module_id' => 'v',
            'alias' => 'video',
            'field' => 'video_id',
            'table' => Phpfox::getT('video'),
            'hide_view' => ['pending', 'my'],
        ];

        (($sPlugin = Phpfox_Plugin::get('video.component_controller_index_process_search')) ? eval($sPlugin) : false);

        $aFilterMenu = [];
        if (!defined('PHPFOX_IS_USER_PROFILE') && !isset($aParentModule['module_id'])) {
            $iMyVideoTotal = Phpfox::getService('v.video')->getMyVideoTotal();
            $aFilterMenu = [
                _p('all_videos') => '',
                _p('my_videos') . ($iMyVideoTotal ? ('<span class="my count-item">' . (($iMyVideoTotal > 99) ? '99+' : $iMyVideoTotal) . '</span>') : '') => 'my'
            ];

            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !Phpfox::getUserBy('profile_page_id')) {
                $aFilterMenu[_p('friends_videos')] = 'friend';
            }

            if (user('pf_video_approve', 0)) {
                $iPendingTotal = Phpfox::getService('v.video')->getPendingTotal();
                if ($iPendingTotal) {
                    $aFilterMenu[_p('pending_videos') . ($iPendingTotal ? ('<span id="video_pending" class="pending count-item">' . (($iPendingTotal > 99) ? '99+' : $iPendingTotal) . '</span>') : '')] = 'pending';
                }
            }
        }
        $this->template()->buildSectionMenu('video', $aFilterMenu);

        $sView = trim($sView, '/');
        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $sCondition = ' AND video.user_id = ' . Phpfox::getUserId();
                $aModules = ['user'];
                if (!Phpfox::isModule('groups')) {
                    $aModules[] = 'groups';
                }
                if (!Phpfox::isModule('pages')) {
                    $aModules[] = 'pages';
                }
                $sCondition .= ' AND video.module_id NOT IN ("' . implode('","', $aModules) . '")';
                $this->search()->setCondition($sCondition);
                break;
            case 'pending':
                Phpfox::isUser(true);
                user('pf_video_approve', null, null, true);
                $sCondition = ' AND video.view_id = 2';
                $aModules = [];
                if (!Phpfox::isModule('groups')) {
                    $aModules[] = 'groups';
                }
                if (!Phpfox::isModule('pages')) {
                    $aModules[] = 'pages';
                }
                $sCondition .= ' AND video.module_id NOT IN ("' . implode('","', $aModules) . '")';
                $this->search()->setCondition($sCondition);
                break;
            default:
                if ($bIsProfile) {
                    $this->search()->setCondition(' AND video.in_process = 0 AND video.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND video.item_id = 0 AND video.privacy IN(' . (setting('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND video.user_id = ' . (int)$aUser['user_id']);
                } else {
                    $sCondition = ' AND video.in_process = 0 AND video.view_id = 0';
                    if (defined('PHPFOX_IS_PAGES_VIEW')) {
                        $sCondition .= ' AND video.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND video.item_id = ' . (int)$aParentModule['item_id'];
                        if (!user('privacy.can_view_all_items')) {
                            $sCondition .= ' AND video.privacy IN(%PRIVACY%)';
                        }
                    } else {
                        if (setting('pf_video_display_video_created_in_group') || setting('pf_video_display_video_created_in_page')) {
                            $aModules = ['video'];
                            if (setting('pf_video_display_video_created_in_group') && Phpfox::isModule('groups')) {
                                $aModules[] = 'groups';
                            }
                            if (setting('pf_video_display_video_created_in_page') && Phpfox::isModule('pages')) {
                                $aModules[] = 'pages';
                            }
                            $sCondition .= ' AND video.module_id IN ("' . implode('","', $aModules) . '")';
                        } else {
                            $sCondition .= ' AND video.item_id = 0';
                        }
                        if (!user('privacy.can_view_all_items')) {
                            $sCondition .= ' AND video.privacy IN(%PRIVACY%)';
                        }
                    }
                    $this->search()->setCondition($sCondition);
                }
                break;
        }

        $sCategory = null;
        if ($this->request()->get('req2') == 'category') {
            $sCategory = $this->request()->getInt('req3');
            $aCategory = Phpfox::getService('v.category')->getCategory($sCategory);
            if ($aCategory['category_id']) {
                $this->setParam('sCurrentCategory', $aCategory['category_id']);
                $this->setParam('iParentCategoryId', $aCategory['parent_id']);
                if (!Phpfox::isAdmin()) {
                    // check this category de-active
                    $aCategory = Phpfox::getService('v.category')->getCategory($sCategory);
                    if (!$aCategory['is_active']) {
                        $this->url()->send('video', [],
                            _p('the_category_you_are_looking_for_does_not_exist_or_has_been_removed'));
                    }
                    // check parent categories de-active
                    $aParentCategories = Phpfox::getService('v.category')->getParentCategories($sCategory);
                    foreach ($aParentCategories as $aParentCategory) {
                        if (!$aParentCategory['is_active']) {
                            $this->url()->send('video', [],
                                _p('the_category_you_are_looking_for_does_not_exist_or_has_been_removed'));
                        }
                    }
                }
                $sChildIds = Phpfox::getService('v.category')->getChildIds($sCategory);
                $sCategoryIds = $sCategory;

                if ($sChildIds) {
                    $sCategoryIds .= ',' . $sChildIds;
                }

                $this->search()->setCondition('AND vcd.category_id IN (' . $sCategoryIds . ')');
            } else {
                $this->url()->send('video', [],
                    _p('the_category_you_are_looking_for_does_not_exist_or_has_been_removed'));
            }
        }

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->setPagingMode(Phpfox::getParam('v.pf_video_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();
        $aVideos = $this->search()->browse()->getRows();

        Phpfox::getLib('pager')->set([
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ]);
        /**
         * Check owner of page/group that blogs belong to.
         */
        $bIsAdmin = false;
        if (!empty($aParentModule) && Phpfox::hasCallback($aParentModule['module_id'], 'isAdmin')) {
            $bIsAdmin = Phpfox::callback($aParentModule['module_id'] . '.isAdmin', $aParentModule['item_id']);
        }
        $aModerationMenus = [];
        $bShowModerator = false;
        if (user('pf_video_delete_all_video') || $bIsAdmin) {
            $aModerationMenus[] = [
                'phrase' => _p('Delete'),
                'action' => 'delete',
            ];
        }

        if ($sView == 'pending' && user('pf_video_approve')) {
            $aModerationMenus[] = [
                'phrase' => _p('Approve'),
                'action' => 'approve',
            ];
        }

        if ($sView != 'pending' && user('pf_video_feature')) {
            $aModerationMenus[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenus[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }

        if (count($aModerationMenus)) {
            $this->setParam('global_moderation', [
                    'name' => 'video',
                    'ajax' => 'v.moderation',
                    'menu' => $aModerationMenus
                ]
            );
            $bShowModerator = true;
        }

        $iProfileId = 0;
        if ($bIsProfile && !empty($aUser)) {
            $sView = 'profile';
            $iProfileId = $aUser['user_id'];
        }

        $this->template()->setTitle(($bIsProfile ? _p('full_name_s_videos',
            ['full_name' => $aUser['full_name']]) : _p('Videos')))
            ->setMeta('keywords', setting('pf_video_meta_keywords'))
            ->setMeta('description', setting('pf_video_meta_description'))
            ->assign(array(
                    'aVideos' => $aVideos,
                    'sView' => $sView,
                    'iProfileId' => $iProfileId,
                    'bShowModerator' => $bShowModerator
                )
            );

        if ($sCategory !== null) {
            $aCategories = Phpfox::getService('v.category')->getParentBreadcrumb($sCategory);
            foreach ($aCategories as $aCategory) {
                $this->template()->setTitle(Phpfox::getSoftPhrase($aCategory[0]));
                $this->template()->setBreadCrumb($aCategory[0], $aCategory[1], true);
            }
        }

        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE,
                    'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission',
                    $aParentModule['item_id'], 'pf_video.view_browse_videos')) {
                $this->template()->assign(['aSearchTool' => []]);

                return Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
            }
            $sTitle = Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']);
            $this->template()
                ->clearBreadCrumb()
                ->setBreadCrumb($sTitle, $aParentModule['url'])
                ->setBreadCrumb(_p('videos'), $aParentModule['url'] . 'video/')
                ->setTitle(_p('videos') . ' &raquo; ' . $sTitle, true);
        }

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}

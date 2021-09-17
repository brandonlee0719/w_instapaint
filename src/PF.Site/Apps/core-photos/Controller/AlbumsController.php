<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AlbumsController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('photo.can_view_photos', true);
        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $bSpecialMenu = (!defined('PHPFOX_IS_AJAX_CONTROLLER')) && Phpfox::getUserParam('photo.can_view_photo_albums');
            $aTplParam = array('bSpecialMenu' => $bSpecialMenu);
            if (defined('PHPFOX_IS_PAGES_VIEW')) {
                $aTplParam['bShowPhotos'] = false;
            }
            $this->template()->assign($aTplParam);
        } else {
            $this->template()->assign(array('bSpecialMenu' => false));
        }
        $aParentModule = $this->getParam('aParentModule');
        $sView = $this->request()->get('view', '');

        if ($iDeleteId = $this->request()->getInt('delete')) {
            if ($sParentReturn = Phpfox::getService('photo.album.process')->delete($iDeleteId)) {
                if (is_bool($sParentReturn)) {
                    $this->url()->send('photo.albums', null, _p('photo_album_successfully_deleted'));
                } else {
                    $this->url()->forward($sParentReturn, _p('photo_album_successfully_deleted'));
                }
            }
        }

        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $aUser = [];
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsUserProfile = true;
            $aUser = $this->getParam('aUser');
        }

        $sPhotoUrl = ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'] . '.photo') : ($aParentModule === null ? $this->url()->makeUrl('photo',
            array('view' => $sView)) : $aParentModule['url'] . 'photo/'));
        $sSearchPhotoUrl = ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'] . '.photo.albums') : ($aParentModule === null ? $this->url()->makeUrl('photo.albums',
            array('view' => $sView)) : $aParentModule['url'] . 'photo/albums/'));

        $aBrowseParams = array(
            'module_id' => 'photo.album',
            'alias' => 'pa',
            'field' => 'album_id',
            'table' => Phpfox::getT('photo_album'),
            'hide_view' => array('pending', 'myalbums')
        );

        $aSearchParam = array(
            'type' => 'photo.album',
            'field' => 'pa.album_id',
            'ignore_blocked' => true,
            'search_tool' => array(
                'table_alias' => 'pa',
                'search' => array(
                    'action' => $sSearchPhotoUrl,
                    'default_value' => _p('search_photo_albums'),
                    'name' => 'search',
                    'field' => 'pa.name'
                ),
                'sort' => array(
                    'latest' => array('pa.time_stamp', _p('latest')),
                    'most-talked' => array('pa.total_comment', _p('most_discussed'))
                ),
                'show' => array(9, 12, 15)
            )
        );
        if (!Phpfox::getUserParam('photo.can_search_for_photos')) {
            unset($aSearchParam['search_tool']['search']);
        }
        $this->search()->set($aSearchParam);

        if ($bIsUserProfile) {
            $sView = 'profile';
            $this->search()->setCondition('AND pa.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND pa.group_id = 0 AND pa.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND pa.user_id = ' . (int)$aUser['user_id']);
        } else {
            if ($aParentModule !== null && !empty($aParentModule['item_id'])) {
                // support new pages setting "Display pages profile photo within gallery" and "Display pages cover photo within gallery" (gallery of pages)
                $aHiddenAlbums = [];
                if (Phpfox::hasCallback($aParentModule['module_id'], 'getHiddenAlbums')) {
                    $aHiddenAlbums = Phpfox::callback($aParentModule['module_id'] . '.getHiddenAlbums', $aParentModule['item_id']);
                }

                $this->search()->setCondition('AND pa.module_id = \'' . $aParentModule['module_id']. '\' AND pa.group_id = ' . (int) $aParentModule['item_id'] . (count($aHiddenAlbums) ? ' AND pa.album_id NOT IN (' . implode(',', $aHiddenAlbums) . ')' : ''));
            } else {
                if ($sView == 'myalbums') {
                    Phpfox::isUser(true);
                    $sCondition = ' AND pa.user_id = ' . Phpfox::getUserId();
                    //hide special album if have no photos
                    $sCondition .= ' AND (((pa.profile_id > 0 OR pa.cover_id > 0 OR pa.timeline_id > 0) AND pa.total_photo > 0) OR (pa.profile_id = 0 AND pa.cover_id = 0 AND pa.timeline_id = 0))';
                    $aModules = [];
                    if (!Phpfox::isModule('groups')) {
                        $aModules[] = 'groups';
                    }
                    if (!Phpfox::isModule('pages')) {
                        $aModules[] = 'pages';
                    }
                    if (count($aModules)) {
                        $sCondition .= ' AND (pa.module_id NOT IN ("' . implode('","',
                                $aModules) . '") OR pa.module_id IS NULL)';
                    }
                    $this->search()->setCondition($sCondition);
                } else {
                    $sCondition = 'AND pa.view_id = 0 AND pa.total_photo > 0';
                    $sCondition .= Phpfox::getService('photo')->getConditionsForSettingPageGroup('pa');
                    if (!Phpfox::getUserParam('privacy.can_view_all_items')) {
                        $sCondition .= ' AND pa.privacy IN(%PRIVACY%)';
                    }
                    $this->search()->setCondition($sCondition);
                }
            }
        }

        // not use this setting in pages view, because pages have seperate settings about this.
        if ($sView != 'myalbums' && !defined('PHPFOX_IS_PAGES_VIEW')) {
            if (!Phpfox::getParam('photo.display_profile_photo_within_gallery')) {
                $this->search()->setCondition('AND pa.profile_id = 0');
            }
            if (!Phpfox::getParam('photo.display_cover_photo_within_gallery')) {
                $this->search()->setCondition('AND pa.cover_id = 0');
            }
            if (!Phpfox::getParam('photo.display_timeline_photo_within_gallery')) {
                $this->search()->setCondition('AND pa.timeline_id = 0');
            }
        }

        $this->search()->browse()
            ->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('photo.photo_paging_mode', 'loadmore'))
            ->execute();

        $aAlbums = $this->search()->browse()->getRows();

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $aUser = $this->getParam('aUser');
            if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'photo.display_on_profile')) {
                $aAlbums = array();
            }
        }

        $aPager = array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        );

        \Phpfox_Pager::instance()->set($aPager);
        /**
         * Check owner of page/group that blogs belong to.
         */
        $bIsAdmin = false;
        if (!empty($aParentModule) && Phpfox::hasCallback($aParentModule['module_id'], 'isAdmin')) {
            $bIsAdmin = Phpfox::callback($aParentModule['module_id'] . '.isAdmin', $aParentModule['item_id']);
        }
        $bShowModerator = false;
        if (Phpfox::getUserParam('photo.can_delete_other_photo_albums') || $bIsAdmin) {
            $this->setParam('global_moderation', array(
                    'name' => 'album',
                    'ajax' => 'photo.albumModeration',
                    'menu' => array(
                        array(
                            'phrase' => _p('delete'),
                            'action' => 'delete'
                        )
                    )
                )
            );
            $bShowModerator = true;
        }

        $iProfileId = 0;
        if ($bIsUserProfile && !empty($aUser)) {
            $sView = 'profile';
            $iProfileId = $aUser['user_id'];
        }
        $this->template()
            ->clearBreadCrumb()
            ->setBreadCrumb($sView == 'myalbums' ? _p('my_albums') : _p('all_albums'), ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'].'photo.albums') : ($aParentModule === null ? $this->url()->makeUrl('photo.albums').($sView == 'myalbums' ? 'view_myalbums' : '') : $aParentModule['url'] . 'photo/albums')))
            ->assign(array(
                    'aAlbums' => $aAlbums,
                    'bShowModerator' => $bShowModerator,
                    'sView' => $sView,
                    'iProfileId' => $iProfileId,
                )
            );

        if ($aParentModule === null && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            Phpfox::getService('photo')->buildMenu();
            if (Phpfox::getUserParam('photo.can_upload_photos')) {
                sectionMenu(' ' . _p('share_photos'), url('/photo/add'));
            }
        }

        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            $sTitle = Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']);
            $this->template()
                ->clearBreadCrumb()
                ->setBreadCrumb($sTitle, $aParentModule['url'])
                ->setBreadCrumb(_p('all_albums'), $sPhotoUrl)
                ->setTitle(_p('photo_albums') . ' &raquo; ' . $sTitle, true);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_albums_clean')) ? eval($sPlugin) : false);
    }
}
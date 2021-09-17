<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class IndexController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('photo.can_view_photos', true);
        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $aUser = (!defined('PHPFOX_IS_PAGES_VIEW')) ? $this->getParam('aUser') : $this->getParam('aPage');
            $bShowPhotos = $this->request()->get('req3') != 'albums' || $this->request()->get('req4') != 'albums';

            if ($this->request()->get('req3') == '' || $this->request()->get('req4') == '') {
                $bShowPhotos = Phpfox::getParam('photo.in_main_photo_section_show', 'photos') != 'albums';
            }

            if (defined('PHPFOX_IS_PAGES_VIEW')) {
                if (empty($aUser['vanity_url'])) {
                    $sStyle = defined('PHPFOX_PAGES_ITEM_TYPE') ? PHPFOX_PAGES_ITEM_TYPE : 'pages';
                    $aUser['user_name'] = $sStyle . '.' . $aUser['page_id'];
                } else {
                    $aUser['user_name'] = $aUser['vanity_url'];
                }
                $aUser['profile_page_id'] = 0;

                $aInfo = array(
                    'total_albums' => Phpfox::callback('pages.getAlbumCount', $aUser['page_id']),
                    'total_photos' => Phpfox::callback('pages.getPhotoCount', $aUser['page_id'])
                );
            } else {
                $aInfo = array(
                    'total_albums' => Phpfox::getService('photo.album')->getAlbumCount($aUser['user_id']),
                    'total_photos' => $aUser['total_photo']
                );
            }

            $bSpecialMenu = (!defined('PHPFOX_IS_AJAX_CONTROLLER')) && Phpfox::getUserParam('photo.can_view_photo_albums');
            $this->template()->assign(array(
                    'bSpecialMenu' => $bSpecialMenu,
                    'aInfo' => $aInfo,
                    'bShowPhotos' => $bShowPhotos,
                    'sLinkPhotos' => $this->url()->makeUrl($aUser['user_name'] . '.photo.photos'),
                    'sLinkAlbums' => $this->url()->makeUrl($aUser['user_name'] . '.photo.albums')
                )
            );
        } else {
            $this->template()->assign(array('bSpecialMenu' => false));
        }

        if (!$this->request()->get('delete') && defined('PHPFOX_IS_PAGES_VIEW') && ($this->request()->get('req3') == 'albums' || $this->request()->get('req4') == 'albums')) {
            Phpfox::getComponent('photo.albums', array('bNoTemplate' => true), 'controller');
            return null;
        }

        if (
            ((defined('PHPFOX_IS_USER_PROFILE'))
                || !defined('PHPFOX_IS_USER_PROFILE'))
            && $this->request()->get('req3') != 'photos' && !in_array($this->request()->get('view'),
                array('my', 'photos', 'pending')) && !is_numeric($this->request()->get('req2'))
            && Phpfox::getParam('photo.in_main_photo_section_show', 'photos') == 'albums'
            && !$this->request()->get('delete')
            && !$this->request()->get('search-id')
        ) {

            Phpfox::getComponent('photo.albums', array('bNoTemplate' => true), 'controller');
            return null;
        }

        $sAssert = $this->request()->get('req4', false);
        if (($this->request()->get('req3') == 'photos' || $this->request()->get('req3') == 'albums')
            && $sAssert == false
        ) {

        } else {
        }
        if ($this->request()->get('req2') == 'category') {
            $_SESSION['photo_category'] = $this->request()->get('req3');
            $this->template()->setHeader(array('<script type="text/javascript"> var sPhotoCategory = "' . $this->request()->get('req3') . '"; </script>'))
                ->assign(array('sPhotoCategory' => $this->request()->get('req3')));
        } else {
            $_SESSION['photo_category'] = '';
        }
        $aParentModule = $this->getParam('aParentModule');

        if (($iRedirectId = $this->request()->getInt('redirect')) && ($aPhoto = Phpfox::getService('photo')->getForEdit($iRedirectId))) {
            if ($aPhoto['group_id']) {
                $aGroup = Phpfox::getService('groups')->getGroup($aPhoto['group_id'], true);
                $this->url()->send('groups', array($aGroup['title_url'], 'photo', 'view', $aPhoto['title_url']));
            } else {
                $this->url()->send($aPhoto['user_name'],
                    array('photo', ($aPhoto['album_id'] ? $aPhoto['album_url'] : 'view'), $aPhoto['title_url']));
            }
        }

        if (($iRedirectAlbumId = $this->request()->getInt('aredirect')) && ($aAlbum = Phpfox::getService('photo.album')->getForEdit($iRedirectAlbumId))) {
            $this->url()->send($aAlbum['user_name'], array('photo'));
        }

        if (empty($aParentModule) && ($this->request()->get('req1') == 'pages')) {
            $aParentModule = array(
                'module_id' => 'pages',
                'item_id' => $this->request()->get('req2'),
                'url' => Phpfox::getService('pages')->getUrl($this->request()->get('req2'))
            );
            define('PHPFOX_IS_PAGES_VIEW', true);
            define('PHPFOX_IS_PAGES_ITEM_TYPE', 'pages');
        }

        if ($aParentModule === null && $this->request()->getInt('req2') > 0) {
            return Phpfox_Module::instance()->setController('photo.view');
        }

        if (($sLegacyTitle = $this->request()->get('req2')) && !empty($sLegacyTitle) && !is_numeric($sLegacyTitle)) {
            if ((defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) && $sLegacyTitle == 'photo') {

            } else {
            }
        }

        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER') || defined('PHPFOX_LOADING_DELAYED')) {
            if ($this->request()->get('profile_id', null) !== null) {
                $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
                $bIsUserProfile = true;
                $this->setParam('aUser', $aUser);
            } else {
                if ($this->request()->get('req1', null) !== null) {
                    if (($aUser = Phpfox::getService('user')->get($this->request()->get('req1'), false))) {
                        $bIsUserProfile = true;
                        $this->setParam('aUser', $aUser);
                    }
                }
            }
        }

        // Used to control privacy
        $bNoAccess = false;
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsUserProfile = true;
            $aUser = $this->getParam('aUser');
            if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'photo.display_on_profile')) {
                $bNoAccess = true;
            }
        }

        if (isset($aUser) && $aUser['profile_page_id'] != 0) {
            $bIsUserProfile = false;

            $aParentModule = array(
                'module_id' => 'pages',
                'item_id' => $aUser['profile_page_id'],
                'url' => Phpfox::getService('pages')->getUrl($aUser['profile_page_id'])
            );
            if (!defined('PHPFOX_IS_PAGES_VIEW')) {
                define('PHPFOX_IS_PAGES_VIEW', true);
                define('PHPFOX_PAGES_ITEM_TYPE', 'pages');
            }
        }

        if (!isset($aUser) && defined('PHPFOX_IS_PAGES_VIEW')) {
            $aUser = $this->getParam('aUser');
        }

        $sView = $this->request()->get('view', false);
        $sCategory = null;
        $sPhotoUrl = ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
            'photo') : ($aParentModule === null ? $this->url()->makeUrl('photo',
            ['view' => $sView]) : $aParentModule['url'] . 'photo/'));
        $this->setParam('sTagType', 'photo');

        if ($iDeleteId = $this->request()->get('delete')) {
            $iUserId = $this->request()->getInt('user_id', 0);
            if ($sParentReturn = Phpfox::getService('photo.process')->delete($iDeleteId, false, $sView, $iUserId)) {
                if (is_bool($sParentReturn)) {
                    $this->url()->send('photo', [], _p('photo_successfully_deleted'));
                } else {
                    $this->url()->forward($sParentReturn, _p('photo_successfully_deleted'));
                }
            }
        }

        $aSort = array(
            'latest' => array('photo.photo_id', _p('latest')),
            'most-viewed' => array('photo.total_view', _p('most_viewed')),
            'most-talked' => array('photo.total_comment', _p('most_discussed'))
        );

        $aPhotoDisplays = Phpfox::getUserParam('photo.total_photos_displays');
        $aSearchParam = array(
            'type' => 'photo',
            'field' => 'photo.photo_id',
            'ignore_blocked' => true,
            'search_tool' => array(
                'table_alias' => 'photo',
                'search' => array(
                    'action' => $sPhotoUrl,
                    'default_value' => _p('search_photos'),
                    'name' => 'search',
                    'field' => 'photo.title'
                ),
                'sort' => $aSort,
                'show' => (array)$aPhotoDisplays
            )
        );
        if (!Phpfox::getUserParam('photo.can_search_for_photos')) {
            unset($aSearchParam['search_tool']['search']);
        }
        $this->search()->set($aSearchParam);
        $aBrowseParams = array(
            'module_id' => 'photo',
            'alias' => 'photo',
            'field' => 'photo_id',
            'table' => Phpfox::getT('photo'),
            'hide_view' => array('pending', 'my')
        );

        $bIsMassEditUpload = false;
        $bRunPlugin = false;
        if (($sPlugin = Phpfox_Plugin::get('photo.component_controller_index_brunplugin1')) && (eval($sPlugin) === false)) {
            return false;
        }

        switch ($sView) {
            case 'pending':
                Phpfox::getUserParam('photo.can_approve_photos', true);
                $sCondition = 'AND photo.view_id = 1';
                $aModules = [];
                if (!Phpfox::isModule('groups')) {
                    $aModules[] = 'groups';
                }
                if (!Phpfox::isModule('pages')) {
                    $aModules[] = 'pages';
                }
                if (count($aModules)) {
                    $sCondition .= ' AND (photo.module_id NOT IN ("' . implode('","',
                            $aModules) . '") OR photo.module_id IS NULL)';
                }
                $this->search()->setCondition($sCondition);
                $this->template()->assign('bIsInApproveMode', true);
                break;
            case 'my':
                Phpfox::isUser(true);
                $sCondition = 'AND (photo.type_id = 0 OR (photo.type_id = 1 AND (photo.parent_user_id = 0 OR photo.group_id != 0))) AND photo.user_id = ' . Phpfox::getUserId();
                $aModules = [];
                if (!Phpfox::isModule('groups')) {
                    $aModules[] = 'groups';
                }
                if (!Phpfox::isModule('pages')) {
                    $aModules[] = 'pages';
                }
                if (count($aModules)) {
                    $sCondition .= ' AND (photo.module_id NOT IN ("' . implode('","',
                            $aModules) . '") OR photo.module_id IS NULL)';
                }

                $this->search()->setCondition($sCondition);
                if ($this->request()->get('mode') == 'edit') {
                    if (!Phpfox::getUserParam('photo.can_edit_own_photo') && !Phpfox::getUserParam('photo.can_edit_other_photo')) {
                        if ($this->request()->get('module') != '' && $this->request()->get('item') != 0) {
                            $sUrl = $this->url()->makeUrl($this->request()->get('module'), $this->request()->get('item')) . 'photo';
                        } else {
                            $sUrl =  $this->url()->makeUrl('photo');
                        }
                        $this->url()->send($sUrl);
                        return false;
                    }
                    $sConds = 'pa.user_id = ' . Phpfox::getUserId();
                    if(!empty($aParentModule))
                    {
                        $sConds .= ' AND pa.module_id = \''.$aParentModule['module_id'].'\' AND pa.group_id = '.(int)$aParentModule['item_id'];
                    }
                    // get list album for move photo
                    list(, $aAlbums) = Phpfox::getService('photo.album')->get($sConds);
                    $bIsMassEdit = $this->request()->get('massedit') ? $this->request()->get('massedit') : 0;
                    $this->template()->assign('bIsMassEdit', $bIsMassEdit);
                    $this->template()->assign('bIsEditMode', true);
                    foreach ($aAlbums as $iAlbumKey => $aAlbum) {
                        if ($aAlbum['profile_id'] > 0) {
                            unset($aAlbums[$iAlbumKey]);
                        }
                        if ($aAlbum['cover_id'] > 0) {
                            unset($aAlbums[$iAlbumKey]);
                        }
                        if ($aAlbum['timeline_id'] > 0) {
                            unset($aAlbums[$iAlbumKey]);
                        }
                    }
                    $this->template()->assign('aAlbums', $aAlbums);

                    if (($aEditPhotos = $this->request()->get('photos'))) {
                        $sPhotoList = '';
                        foreach ($aEditPhotos as $iPhotoId) {
                            $iPhotoId = rtrim($iPhotoId, ',');
                            if (empty($iPhotoId)) {
                                continue;
                            }
                            $sPhotoList .= (int)$iPhotoId . ',';
                        }
                        $sPhotoList = rtrim($sPhotoList, ',');
                        if (!empty($sPhotoList)) {
                            $bIsMassEditUpload = true;
                            $this->search()->setCondition('AND photo.photo_id IN(' . $sPhotoList . ')');
                        }
                    }
                }
                break;
            default:
                if ($bRunPlugin) {
                    (($sPlugin = Phpfox_Plugin::get('photo.component_controller_index_plugin1')) ? eval($sPlugin) : false);
                } elseif ($bIsUserProfile) {
                    $this->search()->setCondition('AND photo.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,1)' : '= 0') . ' AND photo.group_id = 0 AND photo.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND photo.user_id = ' . (int)$aUser['user_id']);
                } else {
                    $sCondition = 'AND photo.view_id = 0';
                    if (defined('PHPFOX_IS_PAGES_VIEW')) {
                        $sCondition .= ' AND photo.module_id = \'' . db()->escape($aParentModule['module_id']) . '\' AND photo.group_id = ' . (int)$aParentModule['item_id'];
                        if (!Phpfox::getUserParam('privacy.can_view_all_items')) {
                            $sCondition .= ' AND photo.privacy IN(%PRIVACY%)';
                        }

                        // support new pages setting "Display pages profile photo within gallery" and "Display pages cover photo within gallery" (gallery of pages)
                        $aHiddenAlbums = [];
                        if (isset($aParentModule['module_id']) && Phpfox::hasCallback($aParentModule['module_id'], 'getHiddenAlbums')) {
                            $aHiddenAlbums = Phpfox::callback($aParentModule['module_id'] . '.getHiddenAlbums', $aParentModule['item_id']);
                        }
                        if (count($aHiddenAlbums)) {
                            $sCondition .= ' AND photo.album_id NOT IN ('. implode(',', $aHiddenAlbums) .')';
                        }
                    } else {
                        $sCondition .= Phpfox::getService('photo')->getConditionsForSettingPageGroup('photo');
                        if (!Phpfox::getUserParam('privacy.can_view_all_items')) {
                            $sCondition .= ' AND photo.privacy IN(%PRIVACY%)';
                        }
                    }
                    $this->search()->setCondition($sCondition);
                }
                break;
        }

        if ($this->request()->get('req2') == 'category') {
            $sCategory = $iCategory = $this->request()->getInt('req3');
            $sWhere = ' AND pcd.category_id = ' . (int)$sCategory;

            if (!is_int($iCategory)) {
                $iCategory = Phpfox::getService('photo.category')->getCategoryId($sCategory);

            }

            // Get sub-categories
            $aSubCategories = Phpfox::getService('photo.category')->getForBrowse($iCategory);

            if (!empty($aSubCategories) && is_array($aSubCategories)) {
                $aSubIds = Phpfox::getService('photo.category')->extractCategories($aSubCategories);
                if (!empty($aSubIds)) {
                    $sWhere = 'AND pcd.category_id IN (' . (int)$sCategory . ',' . join(',', $aSubIds) . ')';
                }
            }

            $this->search()->setCondition($sWhere);
            $this->setParam('hasSubCategories', true);
        }

        if ($this->request()->get('req2') == 'tag') {
            if (!defined('PHPFOX_GET_FORCE_REQ')) {
                define('PHPFOX_GET_FORCE_REQ', true);
            }
            if (($aTag = Phpfox::getService('tag')->getTagInfo('photo', urldecode($this->request()->get('req3'))))) {
                $this->template()->setBreadCrumb(_p('topic') . ': ' . $aTag['tag_text'] . '',
                    $this->url()->makeUrl('current'), true);

                $this->search()->setCondition('AND tag.tag_text = \'' . urldecode(db()->escape($aTag['tag_text'])) . '\'');
            }
        }

        Phpfox::getService('photo.browse')->category($sCategory);

        if ($sView != 'pending') {
            // not use this setting in pages view, because pages have seperate settings about this.
            if (!defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getParam('photo.display_profile_photo_within_gallery')) {
                $this->search()->setCondition('AND photo.is_profile_photo IN (0)');
            }
            if (!defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getParam('photo.display_cover_photo_within_gallery')) {
                $this->search()->setCondition('AND photo.is_cover_photo IN (0)');
            }
            if (!defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getParam('photo.display_timeline_photo_within_gallery')) {
                $this->search()->setCondition('AND (photo.type_id = 0 OR (photo.type_id = 1 AND photo.group_id != 0))');
            }
        }
        $this->search()->setContinueSearch(true);
        $this->search()->browse()
            ->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('photo.photo_paging_mode', 'loadmore'))
            ->execute();
        if ($bNoAccess == false) {
            $aPhotos = $this->search()->browse()->getRows();
            $iCnt = $this->search()->browse()->getCount();
        } else {
            $aPhotos = array();
            $iCnt = 0;
        }
        $aPager = array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        );
        \Phpfox_Pager::instance()->set($aPager);

        foreach ($aPhotos as $aPhoto) {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aPhoto['title']));
        }

        $this->template()->setTitle(($bIsUserProfile ? _p('full_name_s_photos',
            array('full_name' => $aUser['full_name'])) : _p('photos')))
            ->setBreadCrumb(_p('all_photos'), ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                'photo') : ($aParentModule === null ? $this->url()->makeUrl('photo') : $aParentModule['url'] . 'photo/')))
            ->setMeta('keywords', Phpfox::getParam('photo.photo_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('photo.photo_meta_description'));

        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $this->template()->setMeta('description', _p('site_title_has_a_total_of_total_photo_s',
                array('site_title' => $aUser['full_name'], 'total' => $iCnt)));
        } else {
            $this->template()->setMeta('description', _p('site_title_has_a_total_of_total_photo_s',
                array('site_title' => Phpfox::getParam('core.site_title'), 'total' => $iCnt)));
        }
        /**
         * Check owner of page/group that blogs belong to.
         */
        $bIsAdmin = false;
        if (!empty($aParentModule) && Phpfox::hasCallback($aParentModule['module_id'], 'isAdmin')) {
            $bIsAdmin = Phpfox::callback($aParentModule['module_id'] . '.isAdmin', $aParentModule['item_id']);
        }
        $aModerationMenu = [];
        $bShowModerator = false;
        if ($sView == 'my' && Phpfox::getUserParam('photo.can_edit_own_photo')) {
            $aModerationMenu[] = array(
                'phrase' => _p('edit'),
                'action' => 'edit'
            );
        }
        if ($sView == 'pending' && Phpfox::getUserParam('photo.can_approve_photos')) {
            $aModerationMenu[] = array(
                'phrase' => _p('approve'),
                'action' => 'approve'
            );
        }
        if ($sView != 'pending' && Phpfox::getUserParam('photo.can_feature_photo')) {
            $aModerationMenu[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenu[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }
        if (Phpfox::getUserParam('photo.can_delete_other_photos') || $bIsAdmin) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }
        if (count($aModerationMenu) && $this->request()->get('mode') != 'edit') {
            $this->setParam('global_moderation', array(
                    'name' => 'photo',
                    'ajax' => 'photo.moderation',
                    'menu' => $aModerationMenu
                )
            );
            $bShowModerator = true;
        }
        $iProfileId = 0;
        if ($bIsUserProfile && !empty($aUser)) {
            $sView = 'profile';
            $iProfileId = $aUser['user_id'];
        }

        $iAvatarId = ((Phpfox::isUser()) ? storage()->get('user/avatar/' . Phpfox::getUserId()) : null);
        if ($iAvatarId) {
            $iAvatarId = $iAvatarId->value;
        }
        $iCover = storage()->get('user/cover/' . Phpfox::getUserId());
        if ($iCover) {
            $iCover = $iCover->value;
        }

        $this->template()->setPhrase(array(
                'loading'
            )
        )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.mosaicflow.min.js' => 'static_script',
                )
            )
            ->assign(array(
                    'aPhotos' => $aPhotos,
                    'bIsAjax' => PHPFOX_IS_AJAX,
                    'sPhotoUrl' => $sPhotoUrl,
                    'sView' => $sView,
                    'bNotShowOwner' => ($sView == 'my' || defined('PHPFOX_IS_USER_PROFILE')),
                    'bIsMassEditUpload' => $bIsMassEditUpload,
                    'iPhotosPerRow' => 3,
                    'bShowModerator' => $bShowModerator,
                    'iProfileId' => $iProfileId,
                    'iAvatarId' => $iAvatarId,
                    'iCover' => $iCover
                )
            );

        if ($aParentModule === null && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            Phpfox::getService('photo')->buildMenu();
            if (Phpfox::getUserParam('photo.can_upload_photos') && Phpfox::getUserParam('photo.max_images_per_upload') > 0) {
                sectionMenu(' ' . _p('share_photos'), url('/photo/add'));
            }
        }
        if (!empty($sCategory)) {
            $aCategories = Phpfox::getService('photo.category')->getParentBreadcrumb($sCategory);
            $iCnt = 0;
            foreach ($aCategories as $aCategory) {
                $iCnt++;

                $this->template()->setTitle($aCategory[0]);
                $this->template()->setBreadCrumb($aCategory[0], $aCategory[1],
                    ($iCnt === count($aCategories) ? true : false));
            }
            if ($iCnt > 1) {
                $this->setParam('iParentCategoryId',$aCategories[0]['category_id']);
            }
        } else {
            if ($this->request()->get('req2') == 'category' && isset($aPhoto) && isset($aPhoto['category_name']) && isset($aPhoto['category_id'])) {
                $sCatUrl = str_replace(' ', '-', strtolower($aPhoto['category_name']));
                $this->template()->setBreadCrumb($aPhoto['category_name'],
                    $this->url()->makeUrl('photo.category.' . $aPhoto['category_id'] . '.') . $sCatUrl . '/');
            }
        }

        $this->setParam('sCurrentCategory', $sCategory);

        $iStartCheck = 0;
        if (!empty($sCategory)) {
            $iStartCheck = 5;
        }

        if (!defined('PHPFOX_ALLOW_ID_404_CHECK')) {
            $iAllowIds = uniqid();
            define('PHPFOX_ALLOW_ID_404_CHECK', $iAllowIds);
        } else {
            $iAllowIds = PHPFOX_ALLOW_ID_404_CHECK;
        }

        $aRediAllow = array('category', $iAllowIds);
        if (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE) {
            $aRediAllow[] = 'photo';
        }
        $aCheckParams = array(
            'url' => $this->url()->makeUrl('photo'),
            'start' => $iStartCheck,
            'reqs' => array(
                '2' => $aRediAllow,
                '3' => $aRediAllow
            ),
            'reserved' => array('mode', 'photos')
        );

        if (Phpfox::getParam('core.force_404_check') && !Phpfox::getService('core.redirect')->check404($aCheckParams)) {
            return Phpfox_Module::instance()->setController('error.404');
        }

        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE,
                    'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission',
                    $aParentModule['item_id'], 'photo.view_browse_photos')
            ) {
                $this->template()->assign(['aSearchTool' => []]);
                return Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
            }
            $sTitle = Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']);
            $this->template()
                ->clearBreadCrumb()
                ->setBreadCrumb($sTitle, $aParentModule['url'])
                ->setBreadCrumb(_p('all_photos'), $sPhotoUrl)
                ->setTitle(_p('photos') . ' &raquo; ' . $sTitle, true);
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
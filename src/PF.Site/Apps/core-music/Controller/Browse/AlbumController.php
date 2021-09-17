<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Controller\Browse;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class AlbumController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('music.can_access_music', true);

        if (($iDeleteAlbum = $this->request()->getInt('id')) && $mDeleteReturn = Phpfox::getService('music.album.process')->delete($iDeleteAlbum)) {
            if (is_bool($mDeleteReturn)) {
                if ($mDeleteReturn) {
                    $this->url()->send('music.browse.album', null, _p('album_successfully_deleted'));
                }
                else{
                    $this->url()->send('music.browse.album', null, _p('not_allowed_to_delete_this_album'));
                }
            } else {
                $this->url()->forward($mDeleteReturn, _p('album_successfully_deleted'));
            }
        }

        $sView = $this->request()->get('view');

        $this->template()->setTitle(_p('music_albums'))
            ->setBreadCrumb($sView =='my-album' ? _p('my_albums') :_p('all_albums'), $sView =='my-album' ? $this->url()->makeUrl('music.browse.album',['view' => $sView]) : $this->url()->makeUrl('music.browse.album'));
        $aParentModule = $this->getParam('aParentModule');
        if ($aParentModule === null) {
            \Phpfox::getService('music')->getSectionMenu();
        }

        $aUser = array();

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = \Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }
        $aParentModule = $this->getParam('aParentModule');

        $this->search()->set(array(
                'type' => 'music_album',
                'field' => 'm.album_id',
                'ignore_blocked' => true,
                'search_tool' => array(
                    'table_alias' => 'm',
                    'search' => array(
                        'action' => ($aParentModule !== null ? $aParentModule['url'] . 'music/album' : ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                            array(
                                'music/album',
                                'view' => $this->request()->get('view')
                            )) : $this->url()->makeUrl('music.browse.album',
                            array('view' => $this->request()->get('view'))))),
                        'default_value' => _p('search_albums'),
                        'name' => 'search',
                        'field' => 'm.name'
                    ),
                    'sort' => array(
                        'latest' => array('m.time_stamp', _p('latest')),
                        'most-liked' => array('m.total_like', _p('most_liked')),
                        'most-talked' => array('m.total_comment', _p('most_discussed'))
                    ),
                    'show' => array(10, 20, 30)
                )
            )
        );

        $aBrowseParams = array(
            'module_id' => 'music.album',
            'alias' => 'm',
            'field' => 'album_id',
            'table' => Phpfox::getT('music_album'),
            'hide_view' => array('pending', 'my', 'my-album')
        );

        switch ($sView) {
            case 'my-album':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
                break;
            default:
                if ($bIsProfile === true) {
                    $this->search()->setCondition("AND m.view_id IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ") AND m.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : \Phpfox::getService('core')->getForBrowse($aUser)) . ") AND m.user_id = " . $aUser['user_id'] . "");
                } else {
                    $this->search()->setCondition("AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)");
                    if ($sView == 'featured') {
                        $this->search()->setCondition('AND m.is_featured = 1');
                    }
                }
                break;
        }
        $iFromUser = $this->request()->get('user');
        if ($iFromUser) {
            $this->search()->setCondition(' AND m.user_id=' . intval($iFromUser));
        }
        if ($aParentModule !== null) {
            $this->search()->setCondition("AND m.module_id = '" . Phpfox::getLib('database')->escape($aParentModule['module_id']) . "' AND m.item_id = " . (int)$aParentModule['item_id']);
        } else {
            if ($sView != 'my-album') {
                if ((Phpfox::getParam('music.music_display_music_created_in_group') || Phpfox::getParam('music.music_display_music_created_in_page')) && $bIsProfile !== true) {
                    $aModules = [];
                    if (Phpfox::getParam('music.music_display_music_created_in_group') && Phpfox::isModule('groups')) {
                        $aModules[] = 'groups';
                    }
                    if (Phpfox::getParam('music.music_display_music_created_in_page') && Phpfox::isModule('pages')) {
                        $aModules[] = 'pages';
                    }
                    if (count($aModules)) {
                        $this->search()->setCondition('AND (m.module_id IN ("' . implode('","',
                                $aModules) . '") OR m.module_id is NULL)');
                    } else {
                        $this->search()->setCondition('AND m.module_id is NULL');
                    }
                } else {
                    $this->search()->setCondition('AND m.item_id = 0');
                }
            }
        }
        $this->search()->browse()->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('music.music_paging_mode', 'loadmore'))
            ->execute();

        \Phpfox_Pager::instance()->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));

        $albums = $this->search()->browse()->getRows();
        foreach ($albums as $key => $album) {
            $albums[$key]['songs'] = \Phpfox::getService('music')->getSongs($album['user_id'], $album['album_id']);
        }
        if ($aParentModule === null && Phpfox::getUserParam('music.can_add_music_album') && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            sectionMenu(_p('add_an_album'), url('/music/album/add'));
        }
        $this->template()
            ->assign(array(
                    'aAlbums' => $albums,
                    'sDefaultThumbnail' => Phpfox::getParam('music.default_album_photo')
                )

            )
            ->setMeta('keywords', Phpfox::getParam('music.music_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('music.music_meta_description'));

        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $aTplParam = array('bSpecialMenu' => true);
            if (defined('PHPFOX_IS_PAGES_VIEW')) {
                $aTplParam['bShowSongs'] = false;
            }
            $this->template()->assign($aTplParam);
        } else {
            $this->template()->assign(array('bSpecialMenu' => false));
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
        if (Phpfox::getUserParam('music.can_feature_music_albums')) {
            $aModerationMenu[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenu[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }
        if (Phpfox::getUserParam('music.can_delete_other_music_albums') || $bIsAdmin) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }
        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', array(
                    'name' => 'musicalbum',
                    'ajax' => 'music.moderationAlbum',
                    'menu' => $aModerationMenu
                )
            );
            $bShowModerator = true;
        }
        $this->template()->assign(['bShowModerator' => $bShowModerator]);
        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE,
                    'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission',
                    $aParentModule['item_id'], 'music.view_browse_music')
            ) {
                $this->template()->assign(['aSearchTool' => []]);
                return \Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
            }
            $sTitle = Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']);
            $this->template()
                ->clearBreadCrumb()
                ->setBreadCrumb($sTitle, $aParentModule['url'])
                ->setBreadCrumb(_p('all_albums'), $aParentModule['url'] . 'music/album')
                ->setTitle(_p('music_albums') . ' &raquo; ' . $sTitle, true);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_browse_album_clean')) ? eval($sPlugin) : false);
    }
}

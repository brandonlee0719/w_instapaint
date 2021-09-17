<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;

class IndexController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req4')) && !empty($sLegacyTitle)) {
            \Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('song_id', 'title'),
                    'table' => 'music_song',
                    'redirect' => 'music',
                    'title' => $sLegacyTitle
                )
            );
        }
        Phpfox::getUserParam('music.can_access_music', true);

        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $aUser = (!defined('PHPFOX_IS_PAGES_VIEW')) ? $this->getParam('aUser') : $this->getParam('aPage');
            $bShowSongs = $this->request()->get('req3') != 'album' || $this->request()->get('req4') != 'album';
            if (defined('PHPFOX_IS_PAGES_VIEW')) {
                if (empty($aUser['vanity_url'])) {
                    $sStyle = defined('PHPFOX_PAGES_ITEM_TYPE') ? PHPFOX_PAGES_ITEM_TYPE : 'pages';
                    $aUser['user_name'] = $sStyle . '.' . $aUser['page_id'];
                } else {
                    $aUser['user_name'] = $aUser['vanity_url'];
                }
                $aUser['profile_page_id'] = 0;
            }
            $bSpecialMenu = (!defined('PHPFOX_IS_AJAX_CONTROLLER'));
            $this->template()->assign(array(
                'bSpecialMenu' => $bSpecialMenu,
                'bShowSongs' => $bShowSongs,
                'sSongLink' => $this->url()->makeUrl($aUser['user_name'] . '.music'),
                'sAlbumLink' => $this->url()->makeUrl($aUser['user_name'] . '.music.album')
            ));
        } else {
            $this->template()->assign(array('bSpecialMenu' => false));
        }

        if (!$this->request()->get('delete') && defined('PHPFOX_IS_PAGES_VIEW') && ($this->request()->get('req3') == 'album' || $this->request()->get('req4') == 'album')) {
            Phpfox::getComponent('music.browse.album', array('bNoTemplate' => true), 'controller');
            return null;
        }
        $aParentModule = $this->getParam('aParentModule');

        if ($aParentModule === null && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            if (Phpfox::getUserParam('music.can_upload_music_public')) {
                sectionMenu(_p('share_songs'), url('/music/upload'));
            }
        }
        if ($this->request()->get('req2') == 'delete' && ($iDeleteId = $this->request()->getInt('id'))) {
            $mDeleteReturn = \Phpfox::getService('music.process')->delete($iDeleteId);
            if ($iAlbumId = $this->request()->getInt('album', 0)) {
                $this->url()->send('music.album.manage', ['id' => $iAlbumId], _p('song_successfully_deleted'));
            }
            if (is_bool($mDeleteReturn)) {
                if ($mDeleteReturn) {
                    $this->url()->send('music', null, _p('song_successfully_deleted'));
                }
                else{
                    $this->url()->send('music', null, _p('you_do_not_have_permission_to_delete_this_song'));
                }
            } else {
                $this->url()->forward($mDeleteReturn, _p('song_successfully_deleted'));
            }
        }

        $sView = $this->request()->get('view');

        if (($sRedirect = $this->request()->getInt('redirect')) && ($aSong = \Phpfox::getService('music')->getSong(Phpfox::getUserId(),
                $sRedirect, true))
        ) {
            $this->url()->send($aSong['user_name'],
                array('music', ($aSong['album_id'] ? $aSong['album_url'] : 'view'), $aSong['title_url']));
        }

        if ($aParentModule === null && $this->request()->getInt('req2')) {
            return \Phpfox_Module::instance()->setController('music.view');
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

        $this->template()->setTitle(($bIsProfile ? _p('fullname_s_songs',
            array('full_name' => $aUser['full_name'])) : _p('music')))->setBreadCrumb(_p('all_songs'),
            ($bIsProfile ? $this->url()->makeUrl($aUser['user_name'], 'music') : $this->url()->makeUrl('music')))
            ->setMeta('keywords', Phpfox::getParam('music.music_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('music.music_meta_description'));

        if ($aParentModule === null) {
            \Phpfox::getService('music')->getSectionMenu();
        }

        $this->search()->set(array(
                'type' => 'music_song',
                'field' => 'm.song_id',
                'ignore_blocked' => true,
                'search_tool' => array(
                    'table_alias' => 'm',
                    'search' => array(
                        'action' => (defined('PHPFOX_IS_PAGES_VIEW') ? $aParentModule['url'] . 'music/' : ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                            array('music', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('music',
                            array('view' => $this->request()->get('view'))))),
                        'default_value' => _p('search_songs'),
                        'name' => 'search',
                        'field' => 'm.title'
                    ),
                    'sort' => array(
                        'latest' => array('m.time_stamp', _p('latest')),
                        'most-viewed' => array('m.total_view', _p('most_viewed')),
                        'most-played' => array('m.total_play', _p('most_played')),
                        'most-liked' => array('m.total_like', _p('most_liked')),
                        'most-talked' => array('m.total_comment', _p('most_discussed'))
                    ),
                    'show' => array(10, 20, 30)
                )
            )
        );

        $aBrowseParams = array(
            'module_id' => 'music.song',
            'alias' => 'm',
            'field' => 'song_id',
            'table' => Phpfox::getT('music_song'),
            'hide_view' => array('pending', 'my')
        );

        $iGenre = $this->request()->getInt('req3');

        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
                break;
            case 'pending':
                Phpfox::isUser(true);
                Phpfox::getUserParam('music.can_approve_songs', true);
                $this->search()->setCondition('AND m.view_id = 1');
                $this->template()->assign('bIsInPendingMode', true);
                break;
            default:
                if ($bIsProfile === true) {
                    $this->search()->setCondition("AND m.item_id = 0 AND m.view_id IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ") AND m.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : \Phpfox::getService('core')->getForBrowse($aUser)) . ") AND m.user_id = " . $aUser['user_id'] . "");
                } else {
                    $this->search()->setCondition("AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)");
                    if ($sView == 'featured') {
                        $this->search()->setCondition('AND m.is_featured = 1');
                    }
                }
                break;
        }

        if ($iGenre && ($aGenre = \Phpfox::getService('music.genre')->getGenre($iGenre))) {
            $this->setParam('sGenre',$iGenre);
            $this->search()->setCondition('AND mgd.genre_id = ' . (int)$iGenre);
            $this->template()->setBreadCrumb(Phpfox::getSoftPhrase($aGenre['name']),
                $this->url()->permalink('music.genre', $aGenre['genre_id'], Phpfox::getSoftPhrase($aGenre['name'])),
                true);
        }
        $iFromUser = $this->request()->get('user');
        if ($iFromUser) {
            $this->search()->setCondition(' AND m.user_id=' . intval($iFromUser));
        }

        if ($aParentModule !== null) {
            $this->search()->setCondition("AND m.module_id = '" . Phpfox::getLib('database')->escape($aParentModule['module_id']) . "' AND m.item_id = " . (int)$aParentModule['item_id']);
        } else {
            if ($sView != 'pending' && $sView != 'my' && !$bIsProfile) {
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

        $this->search()->setContinueSearch(true);
        $this->search()->browse()
            ->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('music.music_paging_mode', 'loadmore'))
            ->execute();

        $aSongs = $this->search()->browse()->getRows();
        \Phpfox_Pager::instance()->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));

        if ($sPlugin = \Phpfox_Plugin::get('music.component_controller_music_index')) {
            eval($sPlugin);
        }

        $this->template()
            ->assign(array(
                    'aSongs' => $aSongs,
                    'sMusicView' => $sView,
                )
            );
        /**
         * Check owner of page/group that blogs belong to.
         */
        $bIsAdmin = false;
        if (!empty($aParentModule) && Phpfox::hasCallback($aParentModule['module_id'], 'isAdmin')) {
            $bIsAdmin = Phpfox::callback($aParentModule['module_id'] . '.isAdmin', $aParentModule['item_id']);
        }

        $aModerationMenu = [];
        $bShowModerator = false;
        if ($sView == 'pending') {
            if (Phpfox::getUserParam('music.can_approve_songs')) {
                $aModerationMenu[] = array(
                    'phrase' => _p('approve'),
                    'action' => 'approve'
                );
            }
        } elseif (Phpfox::getUserParam('music.can_feature_songs')) {
            $aModerationMenu[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenu[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }
        if (Phpfox::getUserParam('music.can_delete_other_tracks') || $bIsAdmin) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }
        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', array(
                    'name' => 'musicsong',
                    'ajax' => 'music.moderation',
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
                ->setBreadCrumb(_p('all_songs'), $aParentModule['url'] . 'music/')
                ->setTitle(_p('music') . ' &raquo; ' . $sTitle, true);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}

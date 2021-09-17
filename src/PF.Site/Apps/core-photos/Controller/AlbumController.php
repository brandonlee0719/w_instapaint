<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AlbumController extends Phpfox_Component
{
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_album__1')) ? eval($sPlugin) : false);

        Phpfox::getUserParam('photo.can_view_photo_albums', true);
        Phpfox::getUserParam('photo.can_view_photos', true);
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_start')) ? eval($sPlugin) : false);

        $bIsProfilePictureAlbum = $bIsCoverPhotoAlbum = $bIsTimelinePhotoAlbum = false;
        if ($this->request()->get('req3') == 'profile') {
            $bIsProfilePictureAlbum = true;
            $aAlbum = Phpfox::getService('photo.album')->getForProfileView($this->request()->getInt('req4'));
        } elseif ($this->request()->get('req3') == 'cover') {
            $bIsCoverPhotoAlbum = true;
            $aAlbum = Phpfox::getService('photo.album')->getForCoverView($this->request()->getInt('req4'));
        } else {
            // Get the current album we are trying to view
            $aAlbum = Phpfox::getService('photo.album')->getForView($this->request()->getInt('req3'));
            if ($aAlbum['profile_id'] > 0) {
                $bIsProfilePictureAlbum = true;
            } elseif ($aAlbum['cover_id'] > 0) {
                $bIsCoverPhotoAlbum = true;
            } elseif ($aAlbum['timeline_id'] > 0) {
                $bIsTimelinePhotoAlbum = true;
            }
        }

        // Make sure this is a valid album
        if ($aAlbum === false) {
            return Phpfox_Error::display(_p('invalid_photo_album'));
        }

        Phpfox::getService('photo.album')->getPermissions($aAlbum);

        if ($bIsProfilePictureAlbum) {
            $aAlbum['name'] = _p('user_profile_pictures', ['full_name' => $aAlbum['full_name']]);
        } elseif ($bIsCoverPhotoAlbum) {
            $aAlbum['name'] = _p('user_cover_photo', ['full_name' => $aAlbum['full_name']]);
        } elseif ($bIsTimelinePhotoAlbum) {
            $aAlbum['name'] = _p('user_timeline_photos', ['full_name' => $aAlbum['full_name']]);
        }

        $aAlbum['name'] = Phpfox::getLib('locale')->convert($aAlbum['name']);
        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aAlbum['user_id'])) {
            return \Phpfox_Module::instance()->setController('error.invalid');
        }

        $aCallback = null;
        if (!empty($aAlbum['module_id'])) {
            if ($aCallback = Phpfox::callback($aAlbum['module_id'] . '.getPhotoDetails', $aAlbum)) {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
            }
        }

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('photo_album', $aAlbum['album_id'], $aAlbum['user_id'],
                $aAlbum['privacy'], $aAlbum['is_friend']);
        }

        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_album')) ? eval($sPlugin) : false);

        // Store the album details so we can use it in a block later on
        $this->setParam('aAlbum', $aAlbum);

        // Setup the page data
        $iPage = $this->request()->getInt('page');
        $iPageSize = 10;

        // Create the SQL condition array
        $aConditions = array();
        $aConditions[] = 'p.album_id = ' . $aAlbum['album_id'] . '';
        if ($aAlbum['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('photo.can_approve_photos')) {
            $aConditions[] = 'AND p.view_id = 0';
        }

        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_conditions')) ? eval($sPlugin) : false);

        // Get the photos based on the conditions
        list($iCnt, $aPhotos) = Phpfox::getService('photo')->get($aConditions, 'p.photo_id DESC', $iPage, $iPageSize);

        // Set the pager for the photos
        \Phpfox_Pager::instance()->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'paging_mode' => Phpfox::getParam('photo.photo_paging_mode', 'loadmore')
        ));

        foreach ($aPhotos as $iKey => $aPhoto) {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aPhoto['title']));
            if ($aPhoto['is_cover']) {
                $this->template()->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aPhoto['server_id'],
                        'path' => 'photo.url_photo',
                        'file' => $aPhoto['destination'],
                        'suffix' => '_500',
                        'return_url' => true
                    )
                )
                );
            }
            $aPhotos[$iKey]['link'] .= 'albumid_' . $aAlbum['album_id'] . '/';
        }

        if (Phpfox::getUserBy('profile_page_id')) {
            Phpfox::getService('pages')->setIsInPage();
        }
        $aParamFeed = [
            'comment_type_id' => 'photo_album',
            'privacy' => $aAlbum['privacy'],
            'comment_privacy' => Phpfox::getUserParam('photo.can_post_on_albums') ? 0 : 3,
            'like_type_id' => 'photo_album',
            'feed_is_liked' => $aAlbum['is_liked'],
            'feed_is_friend' => $aAlbum['is_friend'],
            'item_id' => $aAlbum['album_id'],
            'user_id' => $aAlbum['user_id'],
            'total_comment' => $aAlbum['total_comment'],
            'total_like' => $aAlbum['total_like'],
            'feed_link' => $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name']),
            'feed_title' => $aAlbum['name'],
            'feed_display' => 'view',
            'feed_total_like' => $aAlbum['total_like'],
            'report_module' => 'photo_album',
            'report_phrase' => _p('report_this_photo_album')
        ];
        //Disable like and comment if non-friend view profile|cover album
        if ($aAlbum['profile_id'] || $aAlbum['cover_id']) {
            if (!Phpfox::getService('user.privacy')->hasAccess($aAlbum['user_id'], 'feed.share_on_wall')) {
                unset($aParamFeed['comment_type_id']);
                $aParamFeed['disable_like_function'] = true;
            }
        }
        $this->setParam('aFeed', $aParamFeed);
        $iAvatarId = ((Phpfox::isUser()) ? storage()->get('user/avatar/' . Phpfox::getUserId()) : null);
        if ($iAvatarId) {
            $iAvatarId = $iAvatarId->value;
        }
        $iCover = storage()->get('user/cover/' . Phpfox::getUserId());
        if ($iCover) {
            $iCover = $iCover->value;
        }

        // Assign the template vars
        $this->template()->setTitle($aAlbum['name'])
            ->setBreadCrumb(_p('photos'),
                ($aCallback === null ? $this->url()->makeUrl('photo') : $this->url()->makeUrl($aCallback['url_home_photo'])))
            ->setBreadCrumb($aAlbum['name'],
                $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name']), true)
            ->setMeta('description', (empty($aAlbum['description']) ? $aAlbum['name'] : $aAlbum['description']))
            ->setMeta('keywords', $this->template()->getKeywords($aAlbum['name']))
            ->setMeta('keywords', Phpfox::getParam('photo.photo_meta_keywords'))
            ->setPhrase(array(
                    'updating_album',
                    'none_of_your_files_were_uploaded_please_make_sure_you_upload_either_a_jpg_gif_or_png_file'
                )
            )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.mosaicflow.min.js' => 'static_script',
                )
            )
            ->setEditor()
            ->assign(array(
                    'aPhotos' => $aPhotos,
                    'aForms' => $aAlbum,
                    'aAlbum' => $aAlbum,
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aAlbum['description']),
                    'aCallback' => null,
                    'bIsInAlbumMode' => true,
                    'iPhotosPerRow' => 5,
                    'sView' => 'view',
                    'iAvatarId' => $iAvatarId,
                    'iCover' => $iCover,
                    'bIsDetail' => true,
                    'bIsAlbumDetail' => true,
                )
            );

        $bShowModerator = false;
        $aModerationMenu = [];

        if (Phpfox::getUserParam('photo.can_delete_other_photos')) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }

        if (Phpfox::getService('photo.album')->getTotalPendingInAlbum($aAlbum['album_id']) && Phpfox::getUserParam('photo.can_approve_photos')) {
            $aModerationMenu[] = array(
                'phrase' => _p('approve'),
                'action' => 'approve'
            );
        }

        if (Phpfox::getUserParam('photo.can_feature_photo')) {
            $aModerationMenu[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenu[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }
        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', array(
                    'name' => 'photo',
                    'ajax' => 'photo.moderation',
                    'menu' => $aModerationMenu
                )
            );
            $bShowModerator = true;
        }
        $this->template()->assign(array(
                'bShowModerator' => $bShowModerator
            )
        );
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_process_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_album_clean')) ? eval($sPlugin) : false);
    }
}
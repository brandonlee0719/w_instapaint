<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddController extends Phpfox_Component
{
    public function process()
    {
        if ($this->request()->get('picup') == '1') {
            // This redirects the user when Picup has finished uploading the photo
            if ($this->request()->isIOS()) {
                die("<script type='text/javascript'>window.location.href = '" . $this->url()->makeUrl('photo.converting') . "'; </script> ");
            } else {
                die("<script type='text/javascript'>window.open('" . $this->url()->makeUrl('photo.converting') . "', 'my_form'); </script> ");
            }
        }
        // Make sure the user is allowed to upload an image
        Phpfox::isUser(true);
        Phpfox::getUserParam('photo.can_upload_photos', true);
        if (!Phpfox::getUserParam('photo.max_images_per_upload')) {
            Phpfox_Error::display(_p('you_can_not_share_photos_because').'</br>'._p('maximum_number_of_images_you_can_upload_each_time_is').' '.Phpfox::getUserParam('photo.max_images_per_upload'));
        }
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        $bCantUploadMore = false;

        $this->template()->setPhrase(array(
            'select_a_file_to_upload'
        ));

        $this->template()
            ->setPhrase(['maximum_number_of_images_you_can_upload_each_time_is'])
            ->setHeader('<script type="text/javascript">$Behavior.photoProgressBarSettings = function(){ if ($Core.exists(\'#js_photo_form_holder\')) { oProgressBar = {html5upload: true, holder: \'#js_photo_form_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_photo_upload_input\', add_more: ' . ($bCantUploadMore ? 'false' : 'true') . ', max_upload: ' . Phpfox::getUserParam('photo.max_images_per_upload') . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\', valid_file_ext: new Array(\'gif\', \'png\', \'jpg\', \'jpeg\')}; $Core.progressBarInit(); } }</script>');

        $aCallback = false;
        if ($sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getPhotoDetails')) {
            if (($aCallback = Phpfox::callback($sModule . '.getPhotoDetails', array('group_id' => $iItem)))) {
                if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'photo.share_photos')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                }
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                    ->setBreadCrumb($aCallback['title'], $aCallback['url_home'])
                    ->setBreadCrumb(_p('photos'), $aCallback['url_home_photo']);
            } else {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
        } else {
            if ($sModule && $iItem && $aCallback === false) {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
            $this->template()->setBreadCrumb(_p('photos'), $this->url()->makeUrl('photo'));
        }

        $aPhotoAlbums = Phpfox::getService('photo.album')->getAll(Phpfox::getUserId(), $sModule, $iItem);
        foreach ($aPhotoAlbums as $iAlbumKey => $aPhotoAlbum) {
            if ($aPhotoAlbum['profile_id'] > 0) {
                unset($aPhotoAlbums[$iAlbumKey]);
            }
            if ($aPhotoAlbum['cover_id'] > 0) {
                unset($aPhotoAlbums[$iAlbumKey]);
            }
            if ($aPhotoAlbum['timeline_id'] > 0) {
                unset($aPhotoAlbums[$iAlbumKey]);
            }
        }

        $this->template()->setTitle(_p('share_photos'))
            ->setBreadCrumb(_p('share_photos'), $this->url()->current(), true)
            ->setHeader('cache', array(
                    'progress.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.onLoadFormPhotoComplete = function(){ $Core.Photo.sModule = \''.$sModule.'\';$Core.Photo.iItemId = '.$iItem.';$Core.Photo.iAlbumId = '.$this->request()->getInt('album').';$Core.Photo.bMassEdit = '.(Phpfox::getParam('photo.photo_upload_process') ? 1 : 0).'}</script>'
                )
            )
            ->setPhrase(array(
                    'not_a_valid_file_extension_we_only_allow_ext',
                    'photo_uploads',
                    'upload_complete_we_are_currently_processing_the_photos',
                    'edit_photos',
                    'upload_more',
                    'go_to_detail',
                    'upload_again',
                    'dict_file_too_big',
                    'dict_invalid_file_type',
                    'upload_failed_please_remove_all_error_files_and_try_again',
                    'continue',
                    'done',
                    'notice'
                )
            )
            ->assign(array(
                    'iMaxFileSize' => Phpfox::getUserParam('photo.photo_max_upload_size'),
                    'iMaxImagesPerUpload' => Phpfox::getUserParam('photo.max_images_per_upload'),
                    'iAlbumId' => $this->request()->getInt('album'),
                    'aAlbums' => $aPhotoAlbums, // Get all the photo albums for this specific user
                    'sModuleContainer' => $sModule,
                    'iItem' => $iItem,
                    'sCategories' => Phpfox::getService('photo.category')->get(false, true),
                    'iTimestamp' => PHPFOX_TIME
                )
            );

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}
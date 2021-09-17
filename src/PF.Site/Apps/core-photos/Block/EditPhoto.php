<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class EditPhoto extends Phpfox_Component
{
    public function process()
    {
        if (($iPhotoId = $this->getParam('ajax_photo_id'))) {
            $aPhoto = Phpfox::getService('photo')->getForEdit($this->request()->get('photo_id'));
            if ($aPhoto['user_id'] != Phpfox::getUserId()) {
                //do not allow move photos of other user
                $aAlbums = [];
            }
            else {
                list(, $aAlbums) = Phpfox::getService('photo.album')->get('pa.user_id = ' . Phpfox::getUserId());
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
                    if ($aPhoto['album_id'] == $aAlbum['album_id']) {
                        unset($aAlbums[$iAlbumKey]);
                    }
                    if ($aPhoto['module_id'] !== $aAlbum['module_id'] || $aPhoto['group_id'] !== $aAlbum['group_id']) {
                        unset($aAlbums[$iAlbumKey]);
                    }
                }
            }
            $this->template()->assign(array(
                    'aForms' => $aPhoto,
                    'aAlbums' => $aAlbums,
                    'bSingleMode' => true,
                    'bIsInline' => $this->request()->get('inline', false),
                    'bIsEditMode' => true
                )
            );
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_edit_photo_clean')) ? eval($sPlugin) : false);
    }
}
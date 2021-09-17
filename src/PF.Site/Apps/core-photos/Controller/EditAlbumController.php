<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class EditAlbumController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);

        if (Phpfox::getUserBy('profile_page_id')) {
            Phpfox::getService('pages')->setIsInPage();
        }

        if (!($aAlbum = Phpfox::getService('photo.album')->getForEdit($this->request()->getInt('id')))) {
            return Phpfox_Error::display(_p('photo_album_not_found'));
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($this->request()->get('req3') == 'photo') {
                if (Phpfox::getService('photo.process')->massProcess($aAlbum, $aVals)) {
                    $this->url()->send('photo.edit-album.photo', array('id' => $aAlbum['album_id']),
                        _p('photo_s_successfully_updated'));
                }
            } else {
                if (Phpfox::getService('photo.album.process')->update($aAlbum['album_id'], $aVals)) {
                    $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name'], true,
                        _p('album_successfully_updated'));
                }
            }
        }

        $aMenus = array(
            'detail' => _p('album_info'),
            'photo' => _p('photos')
        );

        $sAlbumName = Phpfox::getLib('locale')->convert($aAlbum['name']);
        $this->template()->buildPageMenu('js_photo_block',
            $aMenus,
            array(
                'link' => $this->url()->permalink('photo.album', $aAlbum['album_id'], $sAlbumName),
                'phrase' => _p('view_this_album_uppercase')
            )
        );

        list(, $aPhotos) = Phpfox::getService('photo')->get('p.album_id = ' . (int)$aAlbum['album_id']);
        if ($aAlbum['user_id'] != Phpfox::getUserId()) {
            $aAlbums = [];
        }
        else {
            list(, $aAlbums) = Phpfox::getService('photo.album')->get('pa.user_id = ' . Phpfox::getUserId());
            foreach ($aAlbums as $iAlbumKey => $album) {
                if ($album['profile_id'] > 0) {
                    unset($aAlbums[$iAlbumKey]);
                }
                if ($album['cover_id'] > 0) {
                    unset($aAlbums[$iAlbumKey]);
                }
                if ($album['timeline_id'] > 0) {
                    unset($aAlbums[$iAlbumKey]);
                }
                if ($aAlbum['album_id'] == $album['album_id']) {
                    unset($aAlbums[$iAlbumKey]);
                }
                if ($aAlbum['module_id'] !== $album['module_id'] || $aAlbum['group_id'] !== $album['group_id']) {
                    unset($aAlbums[$iAlbumKey]);
                }
            }
        }
        $this->template()->setTitle(_p('editing_album') . ': ' . $sAlbumName)
            ->setBreadCrumb(_p('photos'), $this->url()->makeUrl('photo'))
            ->setBreadCrumb(_p('editing_album') . ':  ' . $aAlbum['name'],
                $this->url()->makeUrl('photo.edit-album', array('id' => $aAlbum['album_id'])), true)
            ->assign(array(
                    'aForms' => $aAlbum,
                    'sAlbumName' => $sAlbumName,
                    'aPhotos' => $aPhotos,
                    'aAlbums' => $aAlbums,
                    'sModule' => $aAlbum['module_id'],
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
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_edit_album_clean')) ? eval($sPlugin) : false);
    }
}
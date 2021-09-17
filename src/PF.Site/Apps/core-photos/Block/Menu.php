<?php

namespace Apps\Core_Photos\Block;

use Phpfox_Component;
use Phpfox_Plugin;

class Menu extends Phpfox_Component
{
    public function process()
    {
        // Not a valid image lets get out of here
        $aPhoto = $this->getParam('aPhoto');

        if (empty($aPhoto)) {
            return false;
        }

        $aUser = $this->getParam('aUser');

        // Assign the template vars
        $this->template()->assign(array(
                'sPhotoUrl' => '',
                'sAlbumUrl' => (empty($aPhoto['album_url']) ? 'view' : $aPhoto['album_url']),
                'iAlbumId' => $aPhoto['album_id'],
                'sUserName' => $aUser['user_name'],
                'sPhotoTitle' => $aPhoto['title'],
                'sBookmarkUrl' => $aPhoto['bookmark_url']
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
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_menu_clean')) ? eval($sPlugin) : false);

        $this->template()->assign(array(
                'sPhotoUrl',
                'sAlbumUrl',
                'iAlbumId',
                'sUserName',
                'sPhotoTitle',
                'sBookmarkUrl'
            )
        );
    }
}
<?php

namespace Apps\Core_Photos\Block;

use Phpfox_Component;
use Phpfox_Plugin;

class MenuAlbum extends Phpfox_Component
{
    public function process()
    {
        $aAlbum = $this->getParam('aAlbum');
        $this->template()->assign(array(
                'sBookmarkUrl' => $this->url()->permalink('photo.album', $aAlbum['album_id'], $aAlbum['name']),
                'aAlbum' => $aAlbum
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_menu_album_clean')) ? eval($sPlugin) : false);
    }
}
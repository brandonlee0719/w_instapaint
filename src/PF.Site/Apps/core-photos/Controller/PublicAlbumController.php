<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class PublicAlbumController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('photo.can_view_photos', true);

        $aCond = array();
        $aCond[] = 'AND pa.total_photo > 0';

        $iPage = $this->request()->getInt('page');
        $iPageSize = 8;

        list($iCnt, $aAlbums) = Phpfox::getService('photo.album')->get($aCond, 'pa.time_stamp DESC', $iPage,
            $iPageSize);
        Phpfox::getService('photo.album.browse')->processRows($aAlbums);

        \Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

        $this->template()
            ->setBreadCrumb(_p('photos'), $this->url()->makeUrl('photo'))
            ->setBreadCrumb(_p('albums'), $this->url()->makeUrl('photo.public-album'))
            ->assign(array(
                    'aAlbums' => $aAlbums
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_public_album_clean')) ? eval($sPlugin) : false);
    }
}
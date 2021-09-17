<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ProfileController extends Phpfox_Component
{
    public function process()
    {
        $this->setParam('bIsProfile', true);

        if ($sPlugin = Phpfox_Plugin::get('photo.component_controller_profile_1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }

        $aUser = $this->getParam('aUser');
        $aTotalAlbums = Phpfox::getService('photo.album')->getAlbumCount($aUser['user_id']);
        $aInfo = array(
            'total_albums' => $aTotalAlbums,
            'total_photos' => $aUser['total_photo']
        );

        $bShowPhotos = $this->request()->get('req3') != 'albums';

        if ($this->request()->get('req3') == '') {
            $bShowPhotos = Phpfox::getParam('photo.in_main_photo_section_show', 'photos') != 'albums';
        }


        $this->template()->assign(array(
            'aInfo' => $aInfo,
            'bShowPhotos' => $bShowPhotos,
            'sLinkPhotos' => $this->url()->makeUrl($aUser['user_name'] . '.photo.photos'),
            'sLinkAlbums' => $this->url()->makeUrl($aUser['user_name'] . '.photo.albums'),
        ));

        if ($this->request()->get('req3') == 'albums') {
            $this->template()->assign('sReq3', 'albums');
            Phpfox::getComponent('photo.albums', array('bNoTemplate' => true), 'controller');
        } else {
            $this->template()->assign('sReq3', 'photo');
            Phpfox::getComponent('photo.index', array('bNoTemplate' => true), 'controller');
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}
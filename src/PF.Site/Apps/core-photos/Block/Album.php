<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Validator;

class Album extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);

        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        // Get the total number of albums this user has
        $iTotalAlbums = Phpfox::getService('photo.album')->getAlbumCount(Phpfox::getUserId());
        // Check if they are allowed to create new albums
        $bAllowedAlbums = (Phpfox::getUserParam('photo.max_number_of_albums') == '' ? true : (!Phpfox::getUserParam('photo.max_number_of_albums') ? false : (Phpfox::getUserParam('photo.max_number_of_albums') <= $iTotalAlbums ? false : true)));
        // Check if we have set a session storage for the form.
        if ($aSessionVals = Phpfox::getLib('session')->get('photo_album_form')) {
            // We have stored the form in a session, lets destroy it now.
            Phpfox::getLib('session')->remove('photo_album_form');
            // Lets assign the past form data so we can reuse it.
            $this->template()->assign(array(
                    'aForms' => $aSessionVals
                )
            );
        }

        $aValidation = array(
            'name' => _p('provide_a_name_for_your_album'),
            'privacy' => _p('select_a_privacy_setting_for_your_album')
        );

        $oValid = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_create_new_album',
                'aParams' => $aValidation
            )
        );

        $this->template()->assign(array(
                'bAllowedAlbums' => $bAllowedAlbums,
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(false),
                'sModule' => $sModule,
                'iItem' => $iItem
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_album_clean')) ? eval($sPlugin) : false);
    }
}
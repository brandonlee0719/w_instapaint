<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class MyPhoto extends Phpfox_Component
{
    public function process()
    {
        $aUser = $this->getParam('aUser');
        $iLimit = $this->getParam('limit',3);
        if (!(int)$iLimit) {
            return false;
        }
        $aPhotos = Phpfox::getService('photo')->getForProfile($aUser['user_id'], $iLimit);
        $iTotal = count($aPhotos);
        if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'photo.display_on_profile')) {
            return false;
        }
        if ($iTotal) {
            $this->template()->assign([
                'aFooter' => [
                    _p('view_more_photos') => $this->url()->makeUrl($aUser['user_name'], 'photo')
                ]
            ]);
        } elseif (Phpfox::getUserId() == $aUser['user_id']) {
            $this->template()->assign([
                'aFooter' => [
                    _p('share_photos') => $this->url()->makeUrl('photo.add')
                ]
            ]);
        } else {
            return false;
        }
        $this->template()->assign([
            'sHeader' => _p('recent_photos'),
            'aPhotos' => $aPhotos,
            'iCount' => $iTotal,
            'aUser' => $aUser
        ]);


        if (Phpfox::getUserId() == $aUser['user_id']) {
            $this->template()->assign('sDeleteBlock', 'profile');
        }

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Recent Photos Limit'),
                'description' => _p('Define the limit of how many recent photos can be displayed when viewing user profile. Set 0 will hide this block'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Recent Photos Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_my_photo_clean')) ? eval($sPlugin) : false);
    }
}
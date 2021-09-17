<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class GroupPhoto extends Phpfox_Component
{
    public function process()
    {
        if (!defined('PHPFOX_IS_PAGES_VIEW')) {
            return false;
        }

        $aGroup = $this->getParam('aPage');

        if (empty($aGroup['page_id'])) {
            return false;
        }

        $aCoverPhoto = ($aGroup['cover_photo_id'] ? Phpfox::getService('photo')->getCoverPhoto($aGroup['cover_photo_id']) : false);
        $iCoverPhotoPosition = $aGroup['cover_photo_position'];

        $aGroupMenus = Phpfox::getService('groups')->getMenu($aGroup);

        if ($oProfileImage = storage()->get('user/avatar/' . $aGroup['page_user_id'])) {
            $aProfileImage = Phpfox::getService('photo')->getPhoto($oProfileImage->value);
            $this->template()->assign('aProfileImage', $aProfileImage);
        }

        $bCanEdit = Phpfox::getService('groups')->isAdmin($aGroup) || Phpfox::getUserParam('groups.can_edit_all_groups');
        $bCanDelete = Phpfox::getService('groups')->isAdmin($aGroup) || Phpfox::getUserParam('groups.can_delete_all_groups');
        $this->template()->assign([
            'aCoverPhoto' => $aCoverPhoto,
            'iConverPhotoPosition' => $iCoverPhotoPosition,
            'aGroupMenus' => $aGroupMenus,
            'bCanChangePhoto' => Phpfox::getService('groups')->isAdmin($aGroup) || Phpfox::getUserParam('groups.can_edit_all_groups'),
            'sDefaultCoverPath' => Phpfox::getParam('groups.default_cover_photo'),
            'bCanEdit' => $bCanEdit,
            'bCanDelete' => $bCanDelete
        ]);

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_photo_clean')) ? eval($sPlugin) : false);
    }
}

<?php

namespace Apps\PHPfox_Groups\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_File;
use Phpfox_Plugin;

class PhotoController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $iPageId = $this->request()->get('page_id');
        if (!$iPageId) {
            \Phpfox_Error::display(_p('page_not_found'));
        }

        $aPage = Phpfox::getService('groups')->getPage($iPageId);
        if (!$aPage) {
            \Phpfox_Error::display(_p('page_not_found'));
        }

        header('Content-Type: application/json');
        $iUserId = Phpfox::getService('groups')->getUserId($iPageId);
        $oFile = Phpfox_File::instance();
        $oFile->load('ajax_upload', array('jpg', 'gif', 'png'),
            (Phpfox::getUserParam('groups.pf_group_max_upload_size') == 0 ? null : (Phpfox::getUserParam('groups.pf_group_max_upload_size') / 1024)));

        if (!\Phpfox_Error::isPassed()) {
            $sErrorMessages = implode('<br/>', \Phpfox_Error::get());
            echo json_encode([
                'run' => 'window.parent.sCustomMessageString = \'' . $sErrorMessages .'\';tb_show(\'' . _p('error') . '\', $.ajaxBox(\'core.message\', \'height=150&width=300\'));'
            ]);
            exit;
        }

        if (!empty($aPage['image_path'])) {
            Phpfox::getService('groups.process')->deleteImage($aPage);
        }

        $oImage = \Phpfox_Image::instance();
        $sFileName = Phpfox_File::instance()->upload('ajax_upload', Phpfox::getParam('pages.dir_image'), $aPage['page_id']);
        $iFileSizes = filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));

        foreach (Phpfox::getService('groups')->getPhotoPicSizes() as $iSize) {
            if (Phpfox::getParam('core.keep_non_square_images')) {
                $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
                    Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
            }
            $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
                Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
        }

        //Crop max width
        if (Phpfox::isModule('photo')) {
            Phpfox::getService('photo')->cropMaxWidth(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));
        }

        define('PHPFOX_PAGES_IS_IN_UPDATE', true);
        Phpfox::getService('user.process')->uploadImage($iUserId, true,
            Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));

        db()->update(':pages', [
            'image_path' => $sFileName,
            'image_server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ], ['page_id' => $iPageId]);

        // Update user space usage
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'groups', $iFileSizes);

        // add feed after updating group's profile image
        $iPageUserId = Phpfox::getService('groups')->getUserId($aPage['page_id']);
        if ($oProfileImage = storage()->get('user/avatar/' . $iPageUserId, null)) {
            Phpfox::getService('feed.process')->callback([
                'table_prefix' => 'pages_',
                'module' => 'groups',
                'add_to_main_feed' => true,
                'has_content' => true
            ])->add('groups_photo', $oProfileImage->value, 0, 0, $aPage['page_id'], $iPageUserId);
        }

        // refresh
        echo json_encode([
            'run' => 'window.location.reload();'
        ]);
        exit;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_photo_clean')) ? eval($sPlugin) : false);
    }
}

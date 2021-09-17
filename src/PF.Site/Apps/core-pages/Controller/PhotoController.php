<?php

namespace Apps\Core_Pages\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_File;

class PhotoController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);

        $iPageId = $this->request()->get('page_id');
        if (!$iPageId) {
            \Phpfox_Error::display(_p('page_not_found'));
        }

        $aPage = Phpfox::getService('pages')->getPage($iPageId);
        if (!$aPage) {
            \Phpfox_Error::display(_p('page_not_found'));
        }

        header('Content-Type: application/json');
        $iUserId = Phpfox::getService('pages')->getUserId($iPageId);
        $oFile = Phpfox_File::instance();
        $oFile->load('ajax_upload', array('jpg', 'gif', 'png'),
            (Phpfox::getUserParam('pages.max_upload_size_pages') == 0 ? null : (Phpfox::getUserParam('pages.max_upload_size_pages') / 1024)));

        if (!\Phpfox_Error::isPassed()) {
            $sErrorMessages = implode('<br/>', \Phpfox_Error::get());
            echo json_encode([
                'run' => 'window.parent.sCustomMessageString = \'' . $sErrorMessages .'\';tb_show(\'' . _p('error') . '\', $.ajaxBox(\'core.message\', \'height=150&width=300\'));'
            ]);
            exit;
        }

        if (!empty($aPage['image_path'])) {
            Phpfox::getService('pages.process')->deleteImage($aPage);
        }

        $oImage = \Phpfox_Image::instance();
        $sFileName = Phpfox_File::instance()->upload('ajax_upload', Phpfox::getParam('pages.dir_image'), $aPage['page_id']);
        $iFileSizes = filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));

        foreach (Phpfox::getService('pages')->getPhotoPicSizes() as $iSize) {
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
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'pages', $iFileSizes);

        // add feed after updating page's profile image
        $iPageUserId = Phpfox::getService('pages')->getUserId($aPage['page_id']);
        if ($oProfileImage = storage()->get('user/avatar/' . $iPageUserId, null)) {
            Phpfox::getService('feed.process')->callback([
                'table_prefix' => 'pages_',
                'module' => 'pages',
                'add_to_main_feed' => true,
                'has_content' => true
            ])->add('pages_photo', $oProfileImage->value, 0, 0, $aPage['page_id'], $iPageUserId);
        }

        // redirect to page
        echo json_encode([
            'run' => 'window.location.reload();'
        ]);
        exit;
    }
}

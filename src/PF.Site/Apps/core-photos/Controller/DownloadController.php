<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class DownloadController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('photo.can_view_photos', true);

        // Check if we want to download a specific photo size
        $iDownloadSize = $this->request()->get('size');

        // Get photo array
        $aPhoto = $this->getParam('aPhoto');
        if ($aPhoto['user_id'] != Phpfox::getUserId()) {
            // Make sure the user group can download this photo
            Phpfox::getUserParam('photo.can_download_user_photos', true);
        }

        if (!$aPhoto['allow_download'] && $aPhoto['user_id'] != Phpfox::getUserId()) {
            return Phpfox_Error::display(_p('not_allowed_to_download_this_image'));
        }

        // Prepare the image path
        $sPath = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['original_destination'],
                (is_numeric($iDownloadSize) ? '_' . $iDownloadSize : ''));
        //Make sure download file exist
        if (!file_exists($sPath) && !Phpfox::getParam('core.allow_cdn')) {
            $aSize = Phpfox::getService('photo')->getPhotoPicSizes();
            rsort($aSize);
            foreach ($aSize as $size) {
                $sPath = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['original_destination'],
                        (is_numeric($size) ? '_' . $size : ''));
                if (file_exists($sPath)) {
                    break;
                }
            }
        }
        if (!file_exists($sPath) && Phpfox::getParam('core.allow_cdn')) {
            //Get temporary file to download
            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aPhoto['server_id'],
                    'path' => 'photo.url_photo',
                    'file' => $aPhoto['destination'],
                    'suffix' => '',
                    'return_url' => true
                )
            );
            file_put_contents($sPath, fox_get_contents($sActualFile));
            //Delete file in local server
            register_shutdown_function(function () use ($sPath) {
                @unlink($sPath);
            });
        }
        // Increment the download counter
        Phpfox::getService('photo.process')->updateCounter($aPhoto['photo_id'], 'total_download');

        // Download the photo
        \Phpfox_File::instance()->forceDownload($sPath, $aPhoto['file_name'], $aPhoto['mime_type'],
            $aPhoto['file_size'], $aPhoto['server_id']);

        // We are done, lets get out of here
        exit;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_download_clean')) ? eval($sPlugin) : false);
    }
}
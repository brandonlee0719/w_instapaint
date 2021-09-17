<?php

namespace Apps\Core_Pages\Job;

use Core\Queue\JobAbstract;
use Phpfox;

class GenerateMissingThumbnails extends JobAbstract
{
    /**
     * Perform a job item
     */
    public function perform()
    {
        $aLocalImages = db()->select('image_path')->from(':pages')->where(['image_server_id' => 0])->executeRows();
        foreach ($aLocalImages['image_path'] as $sImage) {
            if (file_exists(Phpfox::getParam('pages.dir_image') . sprintf($sImage, '_200_square'))) {
                continue;
            }

            $this->_generateMissingThumbnailsInLocalServer($sImage);
        }

        if (Phpfox::getParam('core.allow_cdn')) {
            $aCdnImages = db()->select('image_path, image_server_id')->from(':pages')->where('image_server_id != 0')->executeRows();
            foreach ($aCdnImages as $aImage) {
                $sCheckFile = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('pages.url_image') . sprintf($aImage['image_path'], "_200_square"), $aImage['image_server_id']);
                if (!Phpfox::getLib('image.helper')->checkRemoteFileExists($sCheckFile)) {
                    $this->_generateMissingThumbnailsInCdn($aImage['image_path'], $aImage['image_server_id']);
                }
            }
        }

        $this->delete();
    }

    private function _generateMissingThumbnailsInLocalServer($sImagePath)
    {
        $oImage = \Phpfox_Image::instance();
        $sImageSrcPath = Phpfox::getParam('pages.dir_image') . sprintf($sImagePath, '');
        foreach (Phpfox::getService('pages')->getPhotoPicSizes() as $iSize) {
            $oImage->createThumbnail($sImageSrcPath, Phpfox::getParam('pages.dir_image') . sprintf($sImagePath, "_{$iSize}_square"), $iSize, $iSize, false);
        }
    }

    private function _generateMissingThumbnailsInCdn($sImagePath, $iServerId)
    {
        foreach (Phpfox::getService('pages')->getPhotoPicSizes() as $iSize) {
            $sRemoteFile = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('pages.url_image') . sprintf($sImagePath, "_{$iSize}_square"), $iServerId);
            $sRemoteOriginalFile = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('pages.url_image') . sprintf($sImagePath, ''), $iServerId);
            if (Phpfox::getLib('image.helper')->checkRemoteFileExists($sRemoteFile) ||
                !Phpfox::getLib('image.helper')->checkRemoteFileExists($sRemoteOriginalFile)
            ) {
                continue;
            }

            $sTempFile = Phpfox::getParam('pages.dir_image') . sprintf($sImagePath, '');
            // save remote original file to server
            file_put_contents($sTempFile, fox_get_contents($sRemoteOriginalFile));
            // create thumbnail and put to cdn
            \Phpfox_Image::instance()->createThumbnail($sTempFile, Phpfox::getParam('pages.dir_image') . sprintf($sImagePath, "_{$iSize}_square"), $iSize, $iSize, false);
        }
    }
}

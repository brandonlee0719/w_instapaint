<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Image
 */
class Ad_Component_Controller_Image extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isUser()) {
            exit;
        }

        $aImage = Phpfox_File::instance()->load('image', array('jpg', 'gif', 'png'));
        if ($aImage === false) {
            return j('#js_image_error')->show();
        }

        $sFileName = Phpfox_File::instance()->upload('image', Phpfox::getParam('ad.dir_image'), Phpfox::getUserId() . uniqid());
        if ($sFileName) {
            $sFileDir = Phpfox::getParam('ad.dir_image') . sprintf($sFileName, '');
            $sImageUrl = Phpfox::getParam('ad.url_image') . sprintf($sFileName, '_500');
            $oImage = \Phpfox_Image::instance();;
            foreach (Phpfox::getService('ad')->getPhotoPicSizes() as $iPhotoPicSize) {
                if (Phpfox::getParam('core.keep_non_square_images')) {
                    $oImage->createThumbnail($sFileDir, Phpfox::getParam('ad.dir_image') . sprintf($sFileName, '_' . $iPhotoPicSize), $iPhotoPicSize, $iPhotoPicSize);
                }
                $oImage->createThumbnail($sFileDir, Phpfox::getParam('ad.dir_image') . sprintf($sFileName, '_' . $iPhotoPicSize . '_square'), $iPhotoPicSize, $iPhotoPicSize, false);
            }

            return [
                'run' => '$(\'.js_ad_image\', \'#js_sample_multi_ad_holder\').html(\'<span class="ad-media" style="background-image: url(' .  $sImageUrl . ')"></span>\').show();window.parent.$(\'#js_image_id\').val(\'' . sprintf($sFileName, '') . '\');'
            ];
        }

        exit;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_image_clean')) ? eval($sPlugin) : false);
    }
}

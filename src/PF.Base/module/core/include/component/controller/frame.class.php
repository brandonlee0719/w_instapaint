<?php
defined('PHPFOX') or exit('NO DICE!');

class Core_Component_Controller_Frame extends Phpfox_Component
{
    public function process()
    {
        $oFile = Phpfox::getLib('file');
        $aAcceptImage = [
            'jpg',
            'gif',
            'png'
        ];
        $aIds = [];
        $iMaxUploadSize = Phpfox::getUserParam('photo.photo_max_upload_size') === 0 ? null : (Phpfox::getUserParam('photo.photo_max_upload_size') / 1024);
        foreach ($_FILES['image']['error'] as $iKey => $sError) {
            if ($sError != UPLOAD_ERR_OK) {
                continue;
            }
            if (!is_dir(Phpfox::getParam('core.dir_file_temp'))) {
                @mkdir(Phpfox::getParam('core.dir_file_temp'), 0777, true);
            }
            if ($aImage = $oFile->load('image[' . $iKey . ']', $aAcceptImage, $iMaxUploadSize)) {
                $aImage['destination'] = $oFile->upload('image[' . $iKey . ']', Phpfox::getParam('core.dir_file_temp'),
                    $aImage['name'], true, 0644, false, false);
                $aIds[] = [
                    'id' => Phpfox::getService('core.upload')->upload($aImage),
                    'destination' => $aImage['destination']
                ];
            } else {
                //show error here
            }
        }
        if (count($aIds)) {
            echo '<script type="text/javascript">';
            foreach ($aIds as $iIdKey => $aIdValue) {
                $sImageLink = Phpfox::getLib('image.helper')->display([
                    'server_id' => 0,
                    'path' => 'core.url_file_temp',
                    'file' => $aIdValue['destination'],
                    'return_url' => true
                ]);
                $sImage = '<div class="item"><img src="' . $sImageLink . '" width=100 height=100></div>';
                echo 'window.parent.$("#main_upload_page_upload_test_photo").prepend(\'' . $sImage . '\');';
            }
            echo '</script>';
            exit;
        }
        //show photo in processing page
        $this->template()->assign([
            'sUploadName' => 'upload_test_photo'
        ]);
    }
}
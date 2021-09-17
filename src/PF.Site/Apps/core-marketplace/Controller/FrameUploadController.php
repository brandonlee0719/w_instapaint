<?php
namespace Apps\Core_Marketplace\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Request;

defined('PHPFOX') or exit('NO DICE!');

class FrameUploadController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iId = $_REQUEST['id'];
        $aListing = Phpfox::getService('marketplace')->getForEdit($iId);
        if (!$aListing) {
            echo json_encode([
                'errors' => [_p('the_listing_you_are_looking_for_either_does_not_exist_or_has_been_removed')]
            ]);
            exit;
        }
        $aParams = Phpfox::getService('marketplace')->getUploadParams(['id' => $iId]);
        $aParams['user_id'] = $aListing['user_id'];
        $aParams['type'] = 'marketplace';

        $aImage = Phpfox::getService('user.file')->load('file', $aParams);
        if (!$aImage) {
            echo json_encode([
                'errors' => [_p('cannot_find_the_uploaded_photo_please_try_again')]
            ]);
            exit;
        }

        if (!empty($aImage['error'])) {
            echo json_encode([
                'errors' => [$aImage['error']]
            ]);
            exit;
        }
        $aFile = Phpfox::getService('user.file')->upload('file', $aParams, true);
        if (empty($aFile) || !empty($aFile['error'])) {
            if (empty($aFile)) {
                echo json_encode([
                    'errors' => [_p('cannot_find_the_uploaded_file_please_try_again')]
                ]);
                exit;
            }

            if (!empty($aFile['error'])) {
                echo json_encode([
                    'errors' => [$aFile['error']]
                ]);
                exit;
            }
        }
        $iImageId = db()->insert(Phpfox::getT('marketplace_image'), array(
            'listing_id' => $iId,
            'image_path' => $aFile['name'],
            'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ));
        if (empty($aListing['image_path']) && $iImageId) {
            Phpfox::getService('marketplace.process')->setDefault($iImageId);
        }
        echo json_encode([
            'id' => $iImageId,
        ]);
        exit;
    }
}
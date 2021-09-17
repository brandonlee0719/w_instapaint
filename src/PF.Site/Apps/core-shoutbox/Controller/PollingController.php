<?php

namespace Apps\phpFox_Shoutbox\Controller;

define('PHPFOX_AJAX_CALL_PROCESS', true);

use Phpfox;
use Apps\phpFox_Shoutbox\Service;

/**
 * Class PollingController
 * @author Neil J. <neil@phpfox.com>
 * @package Apps\phpFox_Shoutbox\Controller
 */
class PollingController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        $type = Phpfox::getLib('request')->get('type');

        if ($type == 'pull') {
            $iLastShoutboxId = Phpfox::getLib('request')->get('timestamp');
            if ($iLastShoutboxId == 0) {
                $iLastShoutboxId = Phpfox::getCookie('last_shoutbox_id');
            }
            if (empty($iLastShoutboxId)) {
                $iLastShoutboxId = PHPFOX_TIME;
            }
            $parent_module_id = Phpfox::getLib('request')->get('parent_module_id');
            $parent_item_id = Phpfox::getLib('request')->get('parent_item_id');
            $aData = (new Service\Get())->check($iLastShoutboxId, $parent_module_id,
                $parent_item_id);
            $aJsonData = [
                'timestamp' => isset($aData['timestamp']) ? $aData['timestamp'] : 0
            ];
            if (isset($aData['shoutbox_id'])) {
                $aJsonData['shoutbox_id'] = $aData['shoutbox_id'];
                $aJsonData['text'] = $aData['text'];
                $aJsonData['user_avatar'] = Phpfox::getLib('phpfox.image.helper')
                    ->display([
                        'user' => $aData,
                        'suffix' => '_50_square',
                        'width' => 40,
                        'height' => 40
                    ]);
                $aJsonData['user_type'] = Phpfox::isAdmin() ? 'a' : 'u';
                $aJsonData['user_profile_link'] = Phpfox::getLib('url')->makeUrl($aData['user_name']);
                $aJsonData['user_full_name'] = $aData['full_name'];
            }

            if (function_exists('ob_get_level')) {
                while (ob_get_level()) {
                    ob_get_clean();
                }
            }
            echo json_encode($aJsonData);
        } elseif ($type == 'push') {
            $aVals = [
                'parent_module_id' => Phpfox::getLib('request')->get('parent_module_id'),
                'parent_item_id' => Phpfox::getLib('request')->get('parent_item_id'),
                'text' => Phpfox::getLib('request')->get('text'),
            ];
            $iShoutboxId = (new Service\Process())->add($aVals);
            if (function_exists('ob_get_level')) {
                while (ob_get_level()) {
                    ob_get_clean();
                }
            }
            exit (json_encode([
                'id' => $iShoutboxId
            ]));
        } elseif ($type == 'more') {
            $iLastShoutboxId = Phpfox::getLib('request')->get('last');
            $parent_module_id = Phpfox::getLib('request')->get('parent_module_id');
            $parent_item_id = Phpfox::getLib('request')->get('parent_item_id');

            $aShoutboxes = (new Service\Get())->getLast($iLastShoutboxId, $parent_module_id,
                $parent_item_id);
            $aJsonData = [];
            foreach ($aShoutboxes as $aShoutbox) {
                $aJson = [
                    'timestamp' => isset($aShoutbox['timestamp']) ? $aShoutbox['timestamp'] : 0,
                    'parsed_time' => isset($aShoutbox['timestamp']) ? Phpfox::getLib('date')->convertTime($aShoutbox['timestamp']) : ''
                ];
                if (isset($aShoutbox['shoutbox_id'])) {
                    $aJson['shoutbox_id'] = $aShoutbox['shoutbox_id'];
                }
                if (isset($aShoutbox['shoutbox_id'])) {
                    $aJson['text'] = $aShoutbox['text'];
                    $userAvatar = Phpfox::getLib('phpfox.image.helper')->display([
                        'user' => $aShoutbox,
                        'suffix' => '_50_square',
                        'width' => 40,
                        'height' => 40
                    ]);
                    $aJson['user_avatar'] = $userAvatar;
                    $aJson['user_profile_link'] = Phpfox::getLib('url')->makeUrl($aShoutbox['user_name']);
                    $aJson['user_full_name'] = $aShoutbox['full_name'];
                    $aJson['user_type'] = Phpfox::isAdmin() ? 'a' : 'u';
                    $aJson['type'] = Phpfox::getUserId() == $aShoutbox['user_id'] ? 's' : 'r';
                }
                $aJsonData[] = $aJson;
            }

            if (function_exists('ob_get_level')) {
                while (ob_get_level()) {
                    ob_get_clean();
                }
            }
            if (count($aJsonData)) {
                echo json_encode($aJsonData);
            } else {
                echo json_encode(['empty' => true]);
            }
        }
        exit();
    }
}
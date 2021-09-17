<?php
namespace Apps\PHPfox_IM\Controller;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AdminManageSoundController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $aSoundCustom = ($cache = storage()->get('core-im/sound')) ? (array) $cache->value : array();
        $sSoundPath = PHPFOX_DIR_FILE . 'core-im-sounds' . PHPFOX_DS;

        if (request()->get('noti-sound-type')) {
            $file = request()->get('noti-sound-file');
            // upload custom sound
            if ($file['error'] === UPLOAD_ERR_OK && \Phpfox_File::instance()->load('noti-sound-file', array('mp3', 'wav', 'ogg'))) {
                if (!file_exists($sSoundPath)) {
                    mkdir($sSoundPath);
                }
                $sFileName = \Phpfox_File::instance()->upload('noti-sound-file', $sSoundPath, Phpfox::getUserId() . uniqid(), false);
                $oCdn = Phpfox::getLib('cdn');
                $sFilePath = $oCdn->getServerId() ? $oCdn->getUrl('file/core-im-sounds/' . $sFileName, $oCdn->getServerId()) : 'PF.Base/file/core-im-sounds/' . $sFileName;
            }


            if (count($aSoundCustom)) {
                storage()->del('core-im/sound');
            }
            \Phpfox_Cache::instance()->remove('template');

            $aSoundCustom['option'] = request()->get('noti-sound-type');
            $aSoundCustom['custom_file'] = isset($sFilePath) ? $sFilePath : '';
            storage()->set('core-im/sound', $aSoundCustom);
        }
        if (isset($aSoundCustom['custom_file']) && $aSoundCustom['custom_file'] && strpos($aSoundCustom['custom_file'], 'http') === false) {
            $aSoundCustom['custom_file'] = str_replace('/index.php', '', url('/')) . $aSoundCustom['custom_file'];
        }
        $this->template()->assign(array_merge([
            'file_path' => PHPFOX_PARENT_DIR
        ], $aSoundCustom));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('im.component_controller_admincp_manage_sound_clean')) ? eval($sPlugin) : false);
    }
}
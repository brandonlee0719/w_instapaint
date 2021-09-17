<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @package 		Phpfox_Component
 */
class Core_Component_Controller_Upload_Temp extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
	    $sType = $this->request()->get('type', '');
	    if (empty($sType)) {
	        return false;
        }

        if (!Phpfox::hasCallback($sType, 'getUploadParams')) {
            return false;
        }

        $aParams = [
            'list_type' => [],
            'max_size' => null,
            'upload_dir' => Phpfox::getParam('core.dir_pic'),
            'thumbnail_sizes' => [],
            'user_id' => Phpfox::getUserId(),
            'type' => $sType,
            'param_name' => 'file',
            'field_name' => 'temp_file'

        ];

        $aParams = array_merge($aParams, Phpfox::callback($sType . '.getUploadParams', ['id' => $this->request()->get('id')]));

        $aParams['update_space'] = false;

        if (isset($aParams['type'])) {
            $sType = $aParams['type'];
        }
        $aFile = Phpfox::getService('user.file')->upload($aParams['param_name'], $aParams);

        if (!$aFile) {
            echo json_encode([
                'type' => $sType,
                'error' => _p('upload_fail_please_try_again_later'),
                'field_name' => $aParams['field_name']
            ]);
            exit;
        }

        if (!empty($aFile['error'])) {
            echo json_encode([
                'type' => $sType,
                'error' => $aFile['error'],
                'field_name' => $aParams['field_name']
            ]);
            exit;
        }

        $iId = phpFox::getService('core.temp-file')->add([
            'type' => $sType,
            'size' => $aFile['size'],
            'path' => $aFile['name'],
            'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ]);

        echo json_encode([
            'file' => $iId,
            'type' => $sType,
            'field_name' => $aParams['field_name']
        ]);
        exit;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_upload_temp_clean')) ? eval($sPlugin) : false);
	}
}
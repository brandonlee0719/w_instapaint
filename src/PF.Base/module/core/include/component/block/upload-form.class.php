<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package 		Phpfox_Component

 */
class Core_Component_Block_Upload_Form extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{

	    $sType = $this->getParam('type');
        $iId = $this->getParam('id', '');
        $aExtraParam = $this->getParam('params');

	    if (empty($sType)) {
	        return false;
        }

        $aCallback = [
            'max_file' => 1,
            'first_description' => _p('drag_and_drop_file_here'),
            'type_description' => _p('you_can_upload_a_jpg_gif_or_png_file'),
            'type_list_string' => 'image/jpeg,image/png,image/gif',
            'upload_now' => "true",
            'label' => _p('photo'),
            'upload_icon' => 'ico ico-upload-cloud',
            'param_name' => 'file',
            'remove_field_name' => 'remove_photo'
        ];

	    if (!Phpfox::hasCallback($sType, 'getUploadParams')) {
	        return false;
        }

        $aParams = Phpfox::callback($sType . '.getUploadParams', $aExtraParam);

        $aCallback = array_merge($aCallback, $aParams);

        if (empty($aCallback['upload_url'])) {
            $aCallback['upload_url'] = Phpfox::getLib('phpfox.url')->makeUrl('core.upload-temp', ['type' => $sType, 'id' => $iId]);
            $aCallback['on_remove'] = 'core.removeTempFile';
        }

        if (!empty($aCallback['max_size'])) {
            if (!isset($aCallback['max_size_description'])) {
                $aCallback['max_size_description'] = _p('the_file_size_limit_is_file_size_if_your_upload_does_not_work_try_uploading_a_smaller_picture',
                    ['file_size' => Phpfox_File::instance()->filesize($aCallback['max_size'] * 1048576)]);
            }
            $aCallback['max_size'] = round($aCallback['max_size'], 2);
        }

        if ($aCallback['max_file'] == 1 && !isset($aCallback['style'])) {
            $aCallback['style'] = 'mini';
        }

		$this->template()->assign([
		        'sType' => isset($aCallback['type']) ? $aCallback['type'] : $sType,
                'sRemoveField' => $aCallback['remove_field_name'],
				'iId' => $iId,
				'sCurrentPhoto' => !empty($this->getParam('current_photo')) ? $this->getParam('current_photo') : '',
                'bImageClickable' => $this->getParam('photo_clickable', false) == 'true',
                'aUploadCallback' => $aCallback
			]
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_block_upload_form_clean')) ? eval($sPlugin) : false);
	}
}
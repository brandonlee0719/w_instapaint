<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package  		Module_Attachment
 *
 */
class Attachment_Service_Callback extends Phpfox_Service 
{
    private $_aAllowedTypes;

	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('attachment');
		$this->_aAllowedTypes = Phpfox::getService('attachment.type')->getTypes();
	}

	/**
     * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		$aAttachments = $this->database()
			->select('attachment_id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		foreach ($aAttachments as $aAttach)
		{
            Phpfox::getService('attachment.process')->delete($iUser, $aAttach['attachment_id']);
		}
	}
    
    /**
     * @return array
     */
	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
		
		return array(
			_p('attachment_activity') => $aUser['activity_attachment']
		);
	}
    
    /**
     * @return array
     */
	public function getActivityPointField()
	{
		return array(
			_p('attachments_activity') => 'activity_attachment'
		);
	}
    
    /**
     * @return string a string to parse url
     */
    public function getProfileLink()
    {
        return 'profile.attachment';
    }
    
    /**
     * @param array $aUser
     *
     * @return array|bool
     */
    public function getProfileMenu($aUser)
    {
        //Can view my attachments only
        if ($aUser['user_id'] != Phpfox::getUserId()){
            return false;
        }
        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['activity_attachment'])) {
                return false;
            }

            if (isset($aUser['activity_attachment']) && (int)$aUser['activity_attachment'] === 0) {
                return false;
            }
        }

        $aMenus[] = [
            'phrase' => _p('attachments_activity'),
            'url'    => 'profile.attachment',
            'total'  => (int)(isset($aUser['activity_attachment']) ? $aUser['activity_attachment'] : 0),
            'icon'   => 'feed/attachment.png'
        ];

        return $aMenus;
    }

    /**
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }
        $iTotal = db()->select('COUNT(*)')
                        ->from(':attachment')
                        ->where('user_id ='.(int)$iUserId)
                        ->execute('getField');
        return [
            'total_name' => _p('attachments'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }

    public function getUploadParams()
    {
        $iMaxFileSize = Phpfox::getUserParam('attachment.item_max_upload_size');
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $sMaxFileSize = Phpfox_File::instance()->filesize($iMaxFileSize * 1048576);
        $sPreviewTemplate = '<div class="dz-preview">
                                <div>
                                    <div class="dz-image"><img data-dz-thumbnail /></div>
                                    <span class="dz-error-icon hide"><i class="ico ico-info-circle-alt"></i></span>
                                </div>
                                <div class="dz-attachment-info">
                                    <div class="dz-filename"><span data-dz-name ></span></div>
                                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                                    <span data-dz-remove-file></span>
                                </div>
                                <input class="dz-form-file-id" type="hidden" id="js_upload_form_file_attachment" />
                                <div class="dz-btn-progress">
                                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                                    <div class="btn btn-primary btn-gradient dz-attachment-upload-again">' . _p('browse_three_dot') . '</div>                                    
                                </div>
                            </div>';

        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => $this->_aAllowedTypes,
            'style' => '',
            'label' => '',
            'first_description' => _p('drag_and_drop_file_here_to_upload'),
            'type_description' => _p('you_can_upload_a_extensions_file', ['extensions' => implode(', ', $this->_aAllowedTypes)]),
            'max_size_description' => $iMaxFileSize ? _p('maximum_file_size_is_file_size', ['file_size' => $sMaxFileSize]) : '',
            'upload_url' => Phpfox_Url::instance()->makeUrl('attachment.frame'),
            'param_name' => 'file[]',
            'type_list_string' => '',
            'upload_icon' => '',
            'keep_form' => true,
            'use_browse_button' => true,
            'preview_template' => $sPreviewTemplate,
            'js_events' => [
                'sending' => '$Core.Attachment.dropzoneOnSending',
                'success' => '$Core.Attachment.dropzoneOnSuccess',
            ],
            'extra_data' => [
                'not-show-remove-icon' => 'true',
                'remove-button-action' => '',
                'single-mode' => 'true',
                'error-message-outside' => 'true'
            ]
        ];
    }

    /**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
     * @return null
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('attachment.service_callback__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}
<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Space
 */
class User_Service_Space extends Phpfox_Service 
{
	private $_iTotalUploadSpace = 0;
	
	private $_iTotalSpaceUsed = 0;

	private $_iCurrentUserId = 0;

	protected $_sTable = '';
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('user_space');
		
		(($sPlugin = Phpfox_Plugin::get('user.service_space___construct')) ? eval($sPlugin) : false);
	}
	
	public function update($iUserId, $sType, $iTotal, $sMethod = '+')
	{
		if ($sMethod != '+' && $sMethod != '-')
		{
			return Phpfox_Error::trigger('Invalid space method: ' . $sMethod);
		}
		
		$aRow = $this->database()->select("space_" . $sType . ", space_total")
			->from($this->_sTable)
			->where('user_id = ' . (int) $iUserId)
			->execute('getSlaveRow');
		
		if ($sMethod == '+')
		{
			$iItemTotal = ($aRow['space_' . $sType] + $iTotal);
			$iSpaceTotal = ($aRow['space_total'] + $iTotal);
		}
		else 
		{
			$iItemTotal = ($aRow['space_' . $sType] - $iTotal);
			$iSpaceTotal = ($aRow['space_total'] - $iTotal);	
			
			if ($iItemTotal < 0)
			{
				$iItemTotal = 0;
			}
			
			if ($iSpaceTotal < 0)
			{
				$iSpaceTotal = 0;	
			}			
		}

        $this->database()->update($this->_sTable, ['space_' . $sType => $iItemTotal, 'space_total' => $iSpaceTotal], ['user_id' => (int) $iUserId]);
		
		(($sPlugin = Phpfox_Plugin::get('user.service_space_update')) ? eval($sPlugin) : false);
		
		return true;
	}

	public function isAllowedToUpload($iUserId, $iUploaded = null)
	{
		$this->_build($iUserId);		
		
		if ($this->_iTotalUploadSpace === 0)
		{
			return true;
		}
		
		if ($this->_iTotalSpaceUsed > $this->_iTotalUploadSpace)
		{
			return Phpfox_Error::set(_p('unable_to_upload_you_have_reached_your_limit_of_current_you_are_currently_using_total', array(
						'current' => Phpfox_File::instance()->filesize($this->_iTotalUploadSpace),
						'total' => Phpfox_File::instance()->filesize($this->_iTotalSpaceUsed)
					)
				)
			);
		}
		
		if ($iUploaded !== null && ($this->_iTotalSpaceUsed + $iUploaded) > $this->_iTotalUploadSpace)
		{
			return Phpfox_Error::set(_p('unable_to_upload_you_have_reached_your_limit_of_limit_with_this_upload_you_will_be_using_total', array(
						'limit' => Phpfox_File::instance()->filesize($this->_iTotalUploadSpace),
						'total' => Phpfox_File::instance()->filesize(($this->_iTotalSpaceUsed + $iUploaded))
					)
				)
			);
		}
		
		return true;
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
     *
     * @return  null
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('user.service_space__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
	
	private function _build($iUserId)
	{
		static $mIsChecked = null;

		if (!$this->_iCurrentUserId || (!$this->_iCurrentUserId != $iUserId)) {
            $mIsChecked = null;
            $this->_iCurrentUserId = $iUserId;
        }
		
		if ($mIsChecked === null)
		{
		    $iUserGroupId = Phpfox::getService('user')->getUserGroupId($iUserId);
		    if ($iUserGroupId) {
                $this->_iTotalUploadSpace = (Phpfox::getService('user.group.setting')->getGroupParam($iUserGroupId, 'user.total_upload_space', 0) * 1048576);
                $this->_iTotalSpaceUsed = (int) $this->database()->select('space_total')->from($this->_sTable)->where('user_id = ' . (int) $iUserId)->execute('getSlaveField');
            }
			
			$mIsChecked = true;
		}
	}
}

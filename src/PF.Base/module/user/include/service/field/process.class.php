<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Field_Process
 */
class User_Service_Field_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('user_field');
	}
	
	/**
     * @param int $iId
     * @param bool $bMinus
	 * @todo Move this out of here already!!!
	 */
	public function updateCommentCounter($iId, $bMinus = false)
	{
        $this->database()->update($this->_sTable, ['total_comment', 'total_comment ' . ($bMinus ? "-" : "+") . ' 1'], ['user_id' => (int) $iId], false);
	}
	
	/**
     * @param int $iUserId
     * @param string $sField
     * @param string $sValue
	 * @todo This may not be needed. Use instead "updateCounter()"
	 */
	public function update($iUserId, $sField, $sValue)
	{
		$this->database()->update($this->_sTable, array($sField => $sValue), 'user_id = ' . (int) $iUserId);	
	}
	
	public function updateCounter($iId, $sCounter, $bMinus = false)
	{		
		$this->database()->update($this->_sTable, array(
				$sCounter => array('= ' . $sCounter . ' ' . ($bMinus ? '-' : '+'), 1)
			), 'user_id = ' . (int) $iId
		);
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
		if ($sPlugin = Phpfox_Plugin::get('user.service_field_process__call'))
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

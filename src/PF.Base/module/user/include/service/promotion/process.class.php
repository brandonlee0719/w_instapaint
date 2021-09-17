<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Promotion_Process
 */
class User_Service_Promotion_Process extends Phpfox_Service 
{
    /**
     * @var string
     */
    protected $_sTable = '';

	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('user_promotion');	
	}
	
	public function add($aVals)
	{
        if ($aVals['user_group_id'] == $aVals['upgrade_user_group_id']) {
            return false;
        }
        $this->database()->insert(Phpfox::getT('user_promotion'), array(
                'user_group_id'         => (int)$aVals['user_group_id'],
                'upgrade_user_group_id' => (int)$aVals['upgrade_user_group_id'],
                'total_activity'        => (int)$aVals['total_activity'],
                'total_day'             => (int)$aVals['total_day'],
                'rule'                  => (int)$aVals['rule'],
                'time_stamp'            => PHPFOX_TIME
            )
        );
		
        Phpfox::getLib('cache')->removeGroup('promotion');
		
		return true;
	}
	
	public function update($iId, $aVals)
	{
        if ($aVals['user_group_id'] == $aVals['upgrade_user_group_id']) {
            return false;
        }
        $this->database()->update(Phpfox::getT('user_promotion'), array(
            'user_group_id'         => (int)$aVals['user_group_id'],
            'upgrade_user_group_id' => (int)$aVals['upgrade_user_group_id'],
            'total_activity'        => (int)$aVals['total_activity'],
            'total_day'             => (int)$aVals['total_day'],
            'rule'                  => (int)$aVals['rule']
        ), 'promotion_id = ' . (int)$iId
        );

        Phpfox::getLib('cache')->removeGroup('promotion');
		
		return true;
	}	
	
	public function delete($iId)
	{
		$this->database()->delete($this->_sTable, 'promotion_id = ' . (int) $iId);

        Phpfox::getLib('cache')->removeGroup('promotion');
		
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
     * @return null
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('user.service_promotion_process__call'))
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

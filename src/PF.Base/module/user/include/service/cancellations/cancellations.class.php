<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Cancellations_Cancellations
 */
class User_Service_Cancellations_Cancellations extends Phpfox_Service
{
    /**
     * @var string
     */
    protected $_sTable = '';

	public function __construct()
	{
		$this->_sTable = Phpfox::getT('user_delete');
	}


	/**
	 * Gets the cancellation options. 
	 * @param int $iId	if defined this function gets only this `user_delete`.`delete_id`
	 * @param bool $bGetAll If defined this function filters true => none; false => (is_active == 1)
	 * @return array
	 */
	public function get($iId = null, $bGetAll = false)
	{
	    $sWhere = '';
		if ($iId !== null)
		{
			$sWhere .= 'delete_id = ' . (int) $iId;
		}

		if (!$bGetAll) {
            $sWhere .= ' AND is_active = 1';
        }
		$aReasons = $this->database()->select('*')
				->from($this->_sTable)
				->order('ordering ASC')
				->where($sWhere)
				->execute('getSlaveRows');
		
		return $aReasons;
	}

    /**
     * Gets feedback from the users who have cancelled their account
     *
     * @param string $sOrder
     * @param null   $optionId
     *
     * @return array
     */
	public function getFeedback($sOrder = '', $optionId = null)
	{
	    $sOrder && $this->database()->order($sOrder);

		$oQuery = $this->database()
			->select('*, ug.title as user_group_title')
			->from(Phpfox::getT('user_delete_feedback'), 'udf')
			->leftJoin(Phpfox::getT('user_group'), 'ug', 'ug.user_group_id = udf.user_group_id') // although user groups should not change that often its safer like this
			->order('time_stamp DESC');

		if($optionId){
            $oQuery->where("udf.reasons_given like '%$optionId\"%'");
        }

        $aFeedbacks =  $oQuery->execute('getSlaveRows');

		if (empty($aFeedbacks))
		{
			return $aFeedbacks;
		}
				
		foreach ($aFeedbacks as $iKey => $aFeedback)
		{
			// parse the reasons array
			if (isset($aFeedback['reasons_given']) && strlen($aFeedback['reasons_given']) > 4)
			{
				$aReasons = unserialize($aFeedback['reasons_given']);
				if (empty($aReasons))
				{
					continue;
				}
				foreach ($aReasons as $aReason)
				{				
					$aFeedbacks[$iKey]['reasons'][] = $aReason['phrase_var'];
				}				
			}
		}
		
		return $aFeedbacks;
	}
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('user.service_featured__call'))
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

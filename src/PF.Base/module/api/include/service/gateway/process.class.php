<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api_Service_Gateway_Process
 */
class Api_Service_Gateway_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('api_gateway');
	}
    
    /**
     * @param string $sGateway
     * @param array $aLogData
     */
	public function addLog($sGateway, $aLogData)
	{
		$this->database()->insert(Phpfox::getT('api_gateway_log'), array(
				'gateway_id' => $sGateway,
				'log_data' => json_encode($aLogData),
				'ip_address' => Phpfox::getIp(),
				'time_stamp' => PHPFOX_TIME
			)
		);
	}
    
    /**
     * @param string $sId
     * @param array  $aVals
     *
     * @return bool
     */
	public function update($sId, $aVals)
	{
        $aForm = [
            'title'       => [
                'message' => _p('provide_a_name'),
                'type'    => 'string:required',
                'convert' => true
            ],
            'description' => [
                'message' => _p('provide_a_description'),
                'type'    => 'string',
                'convert' => true
            ],
            'is_active'   => [
                'message' => _p('select_if_the_gateway_is_active_or_not'),
                'type'    => 'int:required'
            ],
            'is_test'     => [
                'message' => _p('select_if_the_gateway_is_in_test_mode'),
                'type'    => 'int:required'
            ],
            'setting'     => [
                'type' => 'array'
            ]
        ];
        
        $aVals = $this->validator()->process($aForm, $aVals);
        
        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        
        if (isset($aVals['setting'])) {
            $aVals['setting'] = (empty($aVals['setting']) ? null : serialize($aVals['setting']));
        }
		
		$this->database()->update($this->_sTable, $aVals, 'gateway_id = \'' . $this->database()->escape($sId) . '\'');
        $this->cache()->removeGroup('api');
		return true;
	}
    
    /**
     * @param int $iId
     * @param int $iType
     */
	public function updateActivity($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
	
		$this->database()->update($this->_sTable, array('is_active' => (int) ($iType == '1' ? 1 : 0)), 'gateway_id = \'' . $this->database()->escape($iId) . '\'');
        $this->cache()->removeGroup('api');

	}
    
    /**
     * @param int $iId
     * @param int $iType
     */
	public function updateTest($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
	
		$this->database()->update($this->_sTable, array('is_test' => (int) ($iType == '1' ? 1 : 0)), 'gateway_id = \'' . $this->database()->escape($iId) . '\'');
        $this->cache()->remove('api_gateway_admin');
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
		if ($sPlugin = Phpfox_Plugin::get('api.service_gateway_process__call'))
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

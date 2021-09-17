<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api_Service_Gateway_Gateway
 */
class Api_Service_Gateway_Gateway extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('api_gateway');	
	}
    
    /**
     * Get active payment gateways
     * @return array
     */
	public function getActive()
	{
	    $sCacheId = $this->cache()->set('api_gateway_active');

        if (!$aGateways = $this->cache()->get($sCacheId)) {
            if ($sPlugin = Phpfox_Plugin::get('api.service_gateway_gateway_getactive_1')) {
                eval($sPlugin);
                if (isset($mReturnPlugin)) {
                    return $mReturnPlugin;
                }
            }
    
            $aGateways = $this->database()
                ->select('ag.*')
                ->from($this->_sTable, 'ag')
                ->where('ag.is_active = 1')
                ->execute('getSlaveRows');
    
            foreach ($aGateways as $iKey => $aGateway) {
                $oGateway = Phpfox_Gateway::instance()
                    ->load($aGateway['gateway_id'], $aGateway);
        
                if ($oGateway === false) {
                    continue;
                }
        
                $aGateways[$iKey]['custom'] = $oGateway->getEditForm();
            }
            $this->cache()->save($sCacheId, $aGateways);
            Phpfox::getLib('cache')->group('api', $sCacheId);
        }
		return $aGateways;	
	}
    
    /**
     * Get payment gateway of user
     * @param int $iUserId
     *
     * @return array
     */
	public function getUserGateways($iUserId)
	{
        $sCacheId = $this->cache()->set('api_gateway_user_' . (int) $iUserId);

        if (!$aGateways = $this->cache()->get($sCacheId)) {
            $aRows = $this->database()
                ->select('*')
                ->from(Phpfox::getT('user_gateway'))
                ->where('user_id = ' . (int)$iUserId)
                ->execute('getSlaveRows');
    
            $aGateways = [];
            foreach ($aRows as $iKey => $mValue) {
                $aCache = unserialize($mValue['gateway_detail']);
                $bSkip = false;
                foreach ($aCache as $sSettingKey => $sSettingValue) {
                    if (empty($sSettingValue)) {
                        $bSkip = true;
                    }
                }
        
                if ($bSkip === true) {
                    $aGateways[$mValue['gateway_id']]['gateway'] = null;
            
                    continue;
                }
        
                $aCache['seller_id'] = $mValue['user_id'];
                $aGateways[$mValue['gateway_id']]['gateway'] = $aCache;
            }
            $this->cache()->save($sCacheId, $aGateways);
            Phpfox::getLib('cache')->group('api', $sCacheId);
        }
        return $aGateways;
	}
    
    /**
     * @param array $aGatewayData
     *
     * @return array
     */
	public function get($aGatewayData = array())
	{
        $sCacheId = $this->cache()->set('api_gateway_data_' . md5(serialize($aGatewayData)));

        if (!$aGateways = $this->cache()->get($sCacheId)) {
            $aGateways = $this->database()
                ->select('ag.*')
                ->from($this->_sTable, 'ag')
                ->where('ag.is_active = 1')
                ->execute('getSlaveRows');
    
            foreach ($aGateways as $iKey => $aGateway) {
                if (isset($aGatewayData['fail_' . $aGateway['gateway_id']]) && $aGatewayData['fail_' . $aGateway['gateway_id']] === true) {
                    unset($aGateways[$iKey]);
                    continue;
                }
        
                if (!($oGateway = Phpfox_Gateway::instance()->load($aGateway['gateway_id'], array_merge($aGateway, $aGatewayData)))
                ) {
                    unset($aGateways[$iKey]);
                    continue;
                }
        
                if (($aGateways[$iKey]['form'] = $oGateway->getForm()) === false) {
                    unset($aGateways[$iKey]);
                }
            }

            if (!isset($aGatewayData['no_purchase_with_points']) && Phpfox::getParam('user.can_purchase_with_points') && Phpfox::getUserParam('user.can_purchase_with_points')) {
                $iTotalPoints = (int)$this->database()
                    ->select('activity_points')
                    ->from(Phpfox::getT('user_activity'))
                    ->where('user_id = ' . (int)Phpfox::getUserId())
                    ->execute('getSlaveField');
        
                $sCurreny = $aGatewayData['currency_code'];
                $aSetting = Phpfox::getParam('user.points_conversion_rate');
                if (isset($aSetting[$sCurreny])) {
                    // Avoid division by zero
                    $iConversion = ($aSetting[$sCurreny] != 0 ? ($aGatewayData['amount'] / $aSetting[$sCurreny]) : 0);
                    if ($iTotalPoints >= $iConversion) {
                        if (isset($aGatewayData['setting']) && is_array($aGatewayData['setting'])) {
                            $sParam = serialize($aGatewayData['setting']);
                            unset($aGatewayData['setting']);
                        }
                
                        $aPointsGateway = [
                            'yourpoints'  => $iTotalPoints,
                            'yourcost'    => $iConversion,
                            'gateway_id'  => 'activitypoints',
                            'title'       => _p('activity_points'),
                            'description' => _p('you_can_purchase_this_with_your_activity_points'),
                            'is_active'   => '1',
                            'form'        => [
                                'url'   => '#',
                                'param' => $aGatewayData
                            ]
                        ];
                        if (isset($sParam) && !empty($sParam)) {
                            $aPointsGateway['setting'] = $sParam;
                        }
                
                        $aGateways[] = $aPointsGateway;
                    } else {
                        Phpfox_Error::display(_p('not_enough_points', ['total' => $iTotalPoints]));
                    }
                }
            }
            $this->cache()->save($sCacheId, $aGateways);
            Phpfox::getLib('cache')->group('api', $sCacheId);
        }
        return $aGateways;
	}
    
    /**
     * @param array $sGateway
     *
     * @return bool|null
     */
	public function callback($sGateway)
	{
		Phpfox::startLog('Callback started.');
		Phpfox::log('Request: ' . var_export($_REQUEST, true));
		
		if (empty($sGateway))
		{
			Phpfox::log('Gateway is empty.');
            Phpfox::getService('api.gateway.process')->addLog(null, Phpfox::endLog());
			
			return false;
		}
		
		$aGateway = $this->database()->select('ag.*')
			->from($this->_sTable, 'ag')
			->where('ag.gateway_id = \'' . $this->database()->escape($sGateway) . '\' AND ag.is_active = 1')
			->execute('getSlaveRow');
		
		if($sGateway == 'activitypoints' && Phpfox::getParam('user.can_purchase_with_points') && Phpfox::getUserParam('user.can_purchase_with_points'))
		{
			Phpfox::log('Gateway successfully loaded.');
			Phpfox::log('Callback complete');
            Phpfox::getService('api.gateway.process')->addLog($this->database()->escape($sGateway), Phpfox::endLog());
			return true;
		}
			
		if (!isset($aGateway['gateway_id']))
		{
			Phpfox::log('"' . $sGateway . '" is not a valid gateway.');
            Phpfox::getService('api.gateway.process')->addLog(null, Phpfox::endLog());
			
			return false;
		}

		Phpfox::log('Attempting to load gateway: ' . $aGateway['gateway_id']);
		
		if (!($oGateway = Phpfox_Gateway::instance()->load($aGateway['gateway_id'], array_merge($_REQUEST, $aGateway))))
		{
			Phpfox::log('Unable to load gateway.');
            Phpfox::getService('api.gateway.process')->addLog($aGateway['gateway_id'], Phpfox::endLog());
			
			return false;
		}
		
		Phpfox::log('Gateway successfully loaded.');
		
		$mReturn = $oGateway->callback();
		
		Phpfox::log('Callback complete');
        
        Phpfox::getService('api.gateway.process')->addLog($aGateway['gateway_id'], Phpfox::endLog());
		
		if ($mReturn == 'redirect')
		{
			Phpfox_Url::instance()->send('');
		}
        return null;
	}
    
    /**
     * @param string $sPeriod
     * @param string $sRecurring
     * @param string       $sInitialFee
     * @param string $sCurrencyId
     *
     * @return null|string
     */
	public function getPeriodPhrase($sPeriod, $sRecurring, $sInitialFee, $sCurrencyId = '')
	{
		// recurring price = 0 then, no recurring!
        if (empty($sRecurring)) {
            return null;
        }

		// $sRecurring = `recurring` 
		// $sInitialFee = `cost` = initial fee
		$aValues = array(
			'period' => $sPeriod,
			'recurring_fee' => Phpfox::getService('core.currency')->getCurrency($sRecurring,$sCurrencyId),
			'cost' => Phpfox::getService('core.currency')->getCurrency($sInitialFee,$sCurrencyId),
			'initial_fee' => Phpfox::getService('core.currency')->getCurrency($sInitialFee,$sCurrencyId),
			'currency_symbol' => ''
		);
        switch ($sPeriod) {
            case '0': // no recurring
                if ($sInitialFee > 0) {
                    $sPhrase = _p('initial_fee_one_time', $aValues);
                } else {
                    $sPhrase = _p('free');
                }
                break;
            case '1':
                // monthly
                if ($sRecurring > 0 && $sInitialFee > 0) {
                    $sPhrase = _p('initial_fee_then_cost_per_month', $aValues);
                } else {
                    if ($sRecurring > 0 && $sInitialFee == 0) {
                        $sPhrase = _p('no_initial_then_cost_per_month', $aValues);
                    }
                }
                break;
            case '2':
                // quarterly
                if ($sRecurring > 0 && $sInitialFee > 0) {
                    $sPhrase = _p('initial_fee_then_cost_per_quarter', $aValues);
                } else {
                    if ($sRecurring > 0 && $sInitialFee == 0) {
                        $sPhrase = _p('no_initial_then_cost_per_quarter', $aValues);
                    }
                }
                break;
            case '3':
                // biannually
                if ($sRecurring > 0 && $sInitialFee > 0) {
                    $sPhrase = _p('initial_fee_then_cost_biannually', $aValues);
                } else {
                    if ($sRecurring > 0 && $sInitialFee == 0) {
                        $sPhrase = _p('no_initial_then_cost_biannually', $aValues);
                    }
                }
                break;
            case '4':
                // yearly
                if ($sRecurring > 0 && $sInitialFee > 0) {
                    $sPhrase = _p('initial_fee_then_cost_yearly', $aValues);
                } else {
                    if ($sRecurring > 0 && $sInitialFee == 0) {
                        $sPhrase = _p('no_initial_then_cost_yearly', $aValues);
                    }
                }
                break;
        }

		return isset($sPhrase) ? $sPhrase : '';
	}
    
    /**
     * Get all api gateways for admin
     * @return array
     */
	public function getForAdmin()
	{
	    $sCacheId = $this->cache()->set('api_gateway_admin');

        if (!$aApiGateways = $this->cache()->get($sCacheId)) {
            $aApiGateways = $this->database()
                ->select('ag.*')
                ->from($this->_sTable, 'ag')
                ->order('ag.title ASC')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aApiGateways);
            Phpfox::getLib('cache')->group('api', $sCacheId);
        }
        return $aApiGateways;
	}
    
    /**
     * Get an api gateway for edit
     * @param string $sGateway
     *
     * @return array|bool
     */
	public function getForEdit($sGateway)
	{
        $aGateway = $this->database()
            ->select('*')
            ->from($this->_sTable)
            ->where('gateway_id = \'' . $this->database()->escape($sGateway) . '\'')
            ->execute('getSlaveRow');

        if (!isset($aGateway['gateway_id'])) {
            return false;
        }

        $oGateway = Phpfox_Gateway::instance()->load($aGateway['gateway_id'], $aGateway);

        if ($oGateway === false) {
            return false;
        }

        $aGateway['custom'] = $oGateway->getEditForm();
        return $aGateway;
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
		if ($sPlugin = Phpfox_Plugin::get('api.service_gateway_gateway__call'))
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

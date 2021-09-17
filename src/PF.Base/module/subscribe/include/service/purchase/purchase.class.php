<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 * @version 		$Id: purchase.class.php 6750 2013-10-08 13:58:53Z Miguel_Espinoza $
 */
class Subscribe_Service_Purchase_Purchase extends Phpfox_Service 
{
	private static $_iRedirectId = null;
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('subscribe_purchase');	
	}
	
	public function setRedirectId($iRedirectId)
	{
		self::$_iRedirectId = $iRedirectId;
	}
	
	public function getRedirectId()
	{
		return self::$_iRedirectId;
	}
	
	public function getSearch($aConditions, $sSort = null, $iPage = null, $iPageSize)
	{
		$aPurchases = array();
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'sp')
			->where($aConditions)
			->execute('getSlaveField');
		
		if ($iCnt)
		{
			$aPurchases = $this->database()->select('spack.*, sp.*, ugs.title AS s_title, ugf.title AS f_title, ' . Phpfox::getUserField())
				->from($this->_sTable, 'sp')
				->join(Phpfox::getT('subscribe_package'), 'spack', 'spack.package_id = sp.package_id')
				->join(Phpfox::getT('user'), 'u', 'u.user_id = sp.user_id')
                ->join(':user_group','ugs', 'ugs.user_group_id=spack.user_group_id')
                ->join(':user_group','ugf', 'ugf.user_group_id=spack.fail_user_group')
				->where($aConditions)
				->limit($iPage, $iPageSize, $iCnt)			
				->order($sSort)
				->execute('getSlaveRows');		
				
			$this->_build($aPurchases);						
		}
		
		return array($iCnt, $aPurchases);
	}
	
	public function get($iUserId, $iLimit = null)
	{
		$aPurchases = $this->database()->select('spack.*, sp.*, ugs.title AS s_title, ugf.title AS f_title')
			->from($this->_sTable, 'sp')
			->join(Phpfox::getT('subscribe_package'), 'spack', 'spack.package_id = sp.package_id')
            ->join(':user_group','ugs', 'ugs.user_group_id=spack.user_group_id')
            ->join(':user_group','ugf', 'ugf.user_group_id=spack.fail_user_group')
			->where('sp.user_id = ' . $iUserId)
			->limit($iLimit)
			->order('sp.time_stamp DESC')
			->execute('getSlaveRows');
			
		$this->_build($aPurchases);
		
		return $aPurchases;
	}
	
	public function getPurchase($iId)
	{
		$aPurchase = $this->database()->select('sp.*, spack.user_group_id, ugs.title AS s_title, ugf.title AS f_title, spack.fail_user_group')
			->from($this->_sTable, 'sp')
			->join(Phpfox::getT('subscribe_package'), 'spack', 'spack.package_id = sp.package_id')
            ->join(':user_group','ugs', 'ugs.user_group_id=spack.user_group_id')
            ->join(':user_group','ugf', 'ugf.user_group_id=spack.fail_user_group')
			->where('sp.purchase_id = ' . (int) $iId)
			->execute('getSlaveRow');
			
		if (!isset($aPurchase['purchase_id']))
		{
			return false;
		}
			
		return $aPurchase;
	}	
	
	public function getInvoice($iId, $bIsOrder = false, $sCacheUserId = null)
	{
		$aPurchase = $this->database()->select('spack.*, sp.*, ugs.title AS s_title, ugf.title AS f_title, sp.time_stamp AS time_purchased')
			->from($this->_sTable, 'sp')
			->join(Phpfox::getT('subscribe_package'), 'spack', 'spack.package_id = sp.package_id')
            ->join(':user_group','ugs', 'ugs.user_group_id=spack.user_group_id')
            ->join(':user_group','ugf', 'ugf.user_group_id=spack.fail_user_group')
			->where('sp.purchase_id = ' . (int) $iId . ' AND sp.user_id = ' . ($sCacheUserId === null ? Phpfox::getUserId() : $sCacheUserId))
			->execute('getSlaveRow');
			
		if (!isset($aPurchase['purchase_id']))
		{
			return false;
		}
		
		if (!empty($aPurchase['cost']) && Phpfox::getLib('parse.format')->isSerialized($aPurchase['cost']))
		{
			$aCosts = unserialize($aPurchase['cost']);	
			foreach ($aCosts as $sKey => $iCost)
			{
				if (Phpfox::getService('core.currency')->getDefault() == $sKey)
				{
					$aPurchase['default_cost'] = $iCost;
					$aPurchase['default_currency_id'] = $sKey;
				}
			}
		}		
		
		if ($aPurchase['recurring_period'] > 0 && Phpfox::getLib('parse.format')->isSerialized($aPurchase['recurring_cost']))
		{
			$aRecurringCosts = unserialize($aPurchase['recurring_cost']);	
			foreach ($aRecurringCosts as $sKey => $iCost)
			{
				if (Phpfox::getService('core.currency')->getDefault() == $sKey)
				{
					$aPurchase['default_recurring_cost'] = ($bIsOrder ? $iCost : Phpfox::getService('api.gateway')->getPeriodPhrase($aPurchase['recurring_period'], $iCost, $aPurchase['default_cost'], $sKey));
					$aPurchase['default_recurring_currency_id'] = $sKey;
				}
			}					
		}
        switch ($aPurchase['recurring_period']){
            case 0:
                $aPurchase['type'] = _p('one_time');
                $aPurchase['expiry_date'] = 0;
                break;
            case 1:
                $aPurchase['type'] = _p('monthly');
                $aPurchase['expiry_date'] = strtotime("+1 month", $aPurchase['time_purchased']);
                break;
            case 2:
                $aPurchase['type'] = _p('quarterly');
                $aPurchase['expiry_date'] = strtotime("+3 month", $aPurchase['time_purchased']);
                break;
            case 3:
                $aPurchase['type'] = _p('biannualy');
                $aPurchase['expiry_date'] = strtotime("+6 month", $aPurchase['time_purchased']);
                break;
            case 4:
                $aPurchase['type'] = _p('annually');
                $aPurchase['expiry_date'] = strtotime("+1 year", $aPurchase['time_purchased']);
                break;
            default:
                $aPurchase['type'] = _p('other');
                break;
        }
		return $aPurchase;
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
		if ($sPlugin = Phpfox_Plugin::get('subscribe.service_purchase_purchase__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
	
	private function &_build(&$aPurchases)
	{
		foreach ($aPurchases as $iKey => $aPurchase)
		{
            $aPurchase['time_purchased'] = $aPurchase['time_stamp'];
            $aPurchases[$iKey]['time_purchased'] = $aPurchase['time_stamp'];
			if (!empty($aPurchase['cost']) && Phpfox::getLib('parse.format')->isSerialized($aPurchase['cost']))
			{
				$aCosts = unserialize($aPurchase['cost']);	
				foreach ($aCosts as $sKey => $iCost)
				{
					if (Phpfox::getService('core.currency')->getDefault() == $sKey)
					{
						$aPurchases[$iKey]['default_cost'] = $iCost;
						$aPurchases[$iKey]['default_currency_id'] = $sKey;
					}
				}
			}		
			
			if ($aPurchase['recurring_period'] > 0 && Phpfox::getLib('parse.format')->isSerialized($aPurchase['recurring_cost']))
			{
				$aRecurringCosts = unserialize($aPurchase['recurring_cost']);	
				foreach ($aRecurringCosts as $sKey => $iCost)
				{
					if (Phpfox::getService('core.currency')->getDefault() == $sKey)
					{
						$aPurchases[$iKey]['default_recurring_cost'] = Phpfox::getService('api.gateway')->getPeriodPhrase($aPurchase['recurring_period'], $iCost, $aPurchases[$iKey]['default_cost'], $sKey);
						$aPurchases[$iKey]['default_recurring_currency_id'] = $sKey;
					}
				}					
			}
            switch ($aPurchase['recurring_period']){
                case 0:
                    $aPurchases[$iKey]['type'] = _p('one_time');
                    $aPurchases[$iKey]['expiry_date'] = 0;
                    break;
                case 1:
                    $aPurchases[$iKey]['type'] = _p('monthly');
                    $aPurchases[$iKey]['expiry_date'] = strtotime("+1 month", $aPurchase['time_purchased']);
                    break;
                case 2:
                    $aPurchases[$iKey]['type'] = _p('quarterly');
                    $aPurchases[$iKey]['expiry_date'] = strtotime("+3 month", $aPurchase['time_purchased']);
                    break;
                case 3:
                    $aPurchases[$iKey]['type'] = _p('biannualy');
                    $aPurchases[$iKey]['expiry_date'] = strtotime("+6 month", $aPurchase['time_purchased']);
                    break;
                case 4:
                    $aPurchases[$iKey]['type'] = _p('annually');
                    $aPurchases[$iKey]['expiry_date'] = strtotime("+1 year", $aPurchase['time_purchased']);
                    break;
                default:
                    $aPurchases[$iKey]['type'] = _p('other');
                    break;
            }
		}		
		
		return $aPurchases;
	}
	
	/* This function tells when will a purchased subscription expire */
	public function getExpireTime($iPurchaseId)
	{
		$aPurchase = $this->database()->select('sp.time_stamp as time_of_purchase, sk.recurring_period')
			->from(Phpfox::getT('subscribe_purchase'), 'sp')
			->join(Phpfox::getT('subscribe_package'), 'sk', 'sk.package_id = sp.package_id')
			->where('sp.purchase_id = ' . (int)$iPurchaseId . ' AND sp.status = "completed"')
			->order('sp.purchase_id DESC')
			->execute('getSlaveRow');
			
		if (empty($aPurchase))
		{
			return false;
		}
		
		switch ($aPurchase['recurring_period'])
		{
			case 1:
                $TimeExpire = strtotime("+1 month", $aPurchase['time_purchased']);
				return $TimeExpire;
			case 2:
                $TimeExpire = strtotime("+3 month", $aPurchase['time_purchased']);
				return $TimeExpire;
			case 3:
                $TimeExpire = strtotime("+4 month", $aPurchase['time_purchased']);
				return $TimeExpire;
			case 4:
                $TimeExpire = strtotime("+1 year", $aPurchase['time_purchased']);
				return $TimeExpire;
		}
		
		return false;
	}

    public function isCompleteSubscribe(){
        $sReq1 = Phpfox_Request::instance()->get('req1');
        $return = true;
        if (!in_array($sReq1, ['subscribe', 'api', 'core'])) {
            $key = 'subscribe_purchase_' . user()->id;
            if (redis()->enabled() && redis()->exists($key)) {
                $aStatus = redis()->get_as_array($key);
            } else {
                $aStatus = $this->database()->select('sp.*')
                    ->from(':subscribe_purchase', 'sp')
                    ->join(':user_field', 'uf', 'uf.subscribe_id=sp.purchase_id')
                    ->where('sp.user_id=' . (int)Phpfox::getUserId())
                    ->execute('getSlaveRow');

                if (redis()->enabled()) {
                    redis()->set($key, $aStatus);
                    redis()->expire($key, 3600);
                }
            }

            if (!isset($aStatus['purchase_id'])){
                $return = true;//No using subscribe
            } elseif (!isset($aStatus['status'])){
                $return = $aStatus['purchase_id'];
            } else {
                $return = true;//Status not null
            }
        }

        return $return;
    }
}
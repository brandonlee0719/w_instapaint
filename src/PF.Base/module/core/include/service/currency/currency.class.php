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
 * @version 		$Id: currency.class.php 6621 2013-09-11 12:45:56Z Miguel_Espinoza $
 */
class Core_Service_Currency_Currency extends Phpfox_Service 
{

    const PREG_FORMATER = '/#([^#]*)([#]*)([^0]*)([0]*)/u';

	/**
	 * array of all the currencies
	 *
	 * @var array
	 */
	private $_aCurrencies = array();

    
    /**
     * @var null|string
     */
	private $_sDefault = null;
    
    /**
     * Class constructor
     *
     * @return mixed|null
     */
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('currency');
        
        if ($sPlugin = Phpfox_Plugin::get('core.service_currency_contruct__1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }
		
		$sCacheId = $this->cache()->set('currency');

		if (!($this->_aCurrencies = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('*')
				->from(Phpfox::getT('currency'))
				->where('is_active = 1')
				->order('ordering ASC')
				->execute('getSlaveRows');
				
			foreach ($aRows as $aRow)
			{
				$this->_aCurrencies[$aRow['currency_id']] = array(
					'symbol' => $aRow['symbol'],
					'name' => $aRow['phrase_var'],
                    'format' => $aRow['format'],
                    'is_default' => $aRow['is_default'],
				);
			}
			
			$this->cache()->save($sCacheId, $this->_aCurrencies);
            Phpfox::getLib('cache')->group( 'currency', $sCacheId);
		}
		
		if ($sPlugin = Phpfox_Plugin::get('core.service_currency__construct'))
		{
			eval($sPlugin);
		}
		return null;
	}
    
    /**
     * @param string $sCurrency
     *
     * @return string
     */
	public function getSymbol($sCurrency)
	{
		return (isset($this->_aCurrencies[$sCurrency]['symbol']) ? $this->_aCurrencies[$sCurrency]['symbol'] : '');
	}
    
    /**
     * @return string
     */
	public function getDefault()
	{
		static $sUserDefault = null;
		
		if ($this->_sDefault === null)
		{
			foreach ((array) $this->_aCurrencies as $sKey => $aCurrency)
			{
				if ($aCurrency['is_default'] == '1')
				{
					$this->_sDefault = $sKey;
					break;
				}
			}
		}
		
		if ($sUserDefault === null && Phpfox::isUser())
		{
			$sCurrency = Phpfox::getService('user')->getCurrency();
			if (!empty($sCurrency))
			{
				$this->_sDefault = $sCurrency;	
			}			
		}
		
		return $this->_sDefault;
	}
    
    /**
     * @param string $sId
     *
     * @return bool|array
     */
	public function getForEdit($sId)
	{
		if ($sPlugin = Phpfox_Plugin::get('core.service_currency_getforedit__1')){eval($sPlugin); if (isset($mReturnFromPlugin)){ return $mReturnFromPlugin; }}
		$aCurrency = $this->database()->select('*')
			->from($this->_sTable)
			->where('currency_id = \'' . $this->database()->escape($sId) . '\'')
			->execute('getSlaveRow');
			
		return (isset($aCurrency['currency_id']) ? $aCurrency : false);
	}
    
    /**
     * @return array
     */
	public function get()
	{
		return $this->_aCurrencies;
	}
    
    /**
     * @return array|mixed
     */
	public function getForBrowse()
	{
        if ($sPlugin = Phpfox_Plugin::get('core.service_currency_getforbrowse__1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }
		$aCurrencies = $this->database()->select('*')	
			->from(Phpfox::getT('currency'))
			->order('ordering ASC')
			->execute('getSlaveRows');
			
		return $aCurrencies;
	}

    /**
     * @param mixed  $sPrice
     * @param string $sCurrencyId currency code
     * @param int  $iPrecision number optional, default null mean 2
     * @return string
     */

	public function getCurrency($sPrice, $sCurrencyId = null, $iPrecision = null)
	{
        if(!$sCurrencyId){
            $sCurrencyId =  $this->getDefault();
        }

        $pattern =  '{0} #,###.00 {1}';
        $form = '{0} {3}';
        $sDecimalPoint = '.';
        $sThousandSeparator =  ',';
        $symbol =  '$';

        if (isset($this->_aCurrencies[$sCurrencyId])) {
            $symbol =  $this->_aCurrencies[$sCurrencyId]['symbol'];
            $pattern =  $this->_aCurrencies[$sCurrencyId]['format'];
        }

        if (preg_match(self::PREG_FORMATER, $pattern, $result)) {
            $sDecimalPoint = $result[3];
            if (is_null($iPrecision)) {
                $iPrecision = strlen($result[4]);
            }
            $sThousandSeparator = $result[1];
            $form = str_replace($result[0], '{3}', $pattern);
        }

		(($sPlugin = Phpfox_Plugin::get('core.service_currency_getcurrency')) ? eval($sPlugin) : false);

        return strtr($form, [
            '{0}' => $symbol,
            '{1}' => $sCurrencyId,
            '{3}' => number_format($sPrice, $iPrecision, $sDecimalPoint, $sThousandSeparator),
        ]);
	}

    /**
     * @param string $sCurrency
     * @param int    $iPrice
     *
     * @return bool|string
     */
	public function getXrate($sCurrency, $iPrice)
	{
		$sKey = Phpfox::getParam('core.exchange_rate_api_key');
		
		if (empty($sKey))
		{
			return false;
		}
		
		$sAmount = fox_get_contents('http://www.exchangerate-api.com/' . $sCurrency . '/' . $this->getDefault() . '/' . $iPrice . '?k='.$sKey);
		
		return ($sAmount > 0 ? $sAmount : false);
	}

	public function getFieldPrice($sCurrencyName = 'currency_id', $sPriceName = 'price')
    {
        if (!($aParams = Phpfox_Request::instance()->getArray('val'))) {
            $aParams = Phpfox_Template::instance()->getVar('aForms');
        }
        $aCurrencies = Phpfox::getService('core.currency')->get();
        $sReturn = '<div class="form-inline">';
        $sReturn .= '<select class="form-control" id="' . $sCurrencyName . '" name="val[' . $sCurrencyName . ']">';
        foreach ($aCurrencies as $sCurrency => $aCurrency) {
            if (isset($aParams[$sCurrencyName]) && $aParams[$sCurrencyName] == $sCurrency) {
                $sSelected = 'selected="selected"';
            } elseif (empty($aParams[$sCurrencyName]) && $aCurrency['is_default']) {
                $sSelected = 'selected="selected"';
            } else {
                $sSelected = '';
            }
            $sReturn .= '<option ' . $sSelected . ' value="'.$sCurrency.'">' . _p($aCurrency['name']) . '</option>';
        }
        $sReturn .= '</select> ';
        $sPriceDefault = isset($aParams[$sPriceName]) ? $aParams[$sPriceName] : 0;
        $sReturn .= '<input class="form-control" type="text" name="val[' . $sPriceName . ']" value="' . $sPriceDefault . '" id="' . $sPriceName . '" size="10" maxlength="100" onfocus="this.select();" />';
        $sReturn .= '</div>';
        return $sReturn;
    }
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('core.service_currency__call'))
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
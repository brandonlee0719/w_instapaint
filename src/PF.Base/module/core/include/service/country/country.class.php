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
 * @package  		Module_Core
 * @version 		$Id: country.class.php 7031 2014-01-08 17:53:30Z Fern $
 */
class Core_Service_Country_Country extends Phpfox_Service 
{
    /**
     * @var string
     */
    protected $_sTable = '';
    /**
     * Save all country
     *
     * @var array
     */
	private $_aCountries = array();
    
    /**
     * Save all children country
     *
     * @var array
     */
	private $_aChildren = array();
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('country');
		
		$sCachedId = $this->cache()->set('country_' . Phpfox_Locale::instance()->getLangId());

		if (!($this->_aCountries = $this->cache()->get($sCachedId)))
		{
			$aRows = $this->database()->select('c.country_iso, c.name')
				->from($this->_sTable, 'c')				
				->order('c.ordering ASC, c.name ASC')
				->execute('getSlaveRows');
			foreach ($aRows as $aRow)
			{
				$this->_aCountries[$aRow['country_iso']] = (Phpfox_Locale::instance()->isPhrase('core.translate_country_iso_' . strtolower($aRow['country_iso'])) ? _p('translate_country_iso_' . strtolower($aRow['country_iso'])) : $aRow['name']);
			}					
			
			$this->cache()->save($sCachedId, $this->_aCountries);
            Phpfox::getLib('cache')->group( 'country', $sCachedId);
		}
	}
    
    /**
     * @param string $sIso
     *
     * @return bool|string
     */
	public function getCountry($sIso)
	{		
		return (isset($this->_aCountries[$sIso]) ? $this->_aCountries[$sIso] : false);
	}
    
    /**
     * @return array
     */
	public function get()
	{	
		return $this->_aCountries;
	}
    
    /**
     * @param string $sIso
     *
     * @return array|bool
     */
	public function export($sIso)
	{
		$aCountry = $this->database()->select('*')
			->from(Phpfox::getT('country'))
			->where('country_iso = \'' . $this->database()->escape($sIso) . '\'')
			->execute('getSlaveRow');
			
		if (!isset($aCountry['country_iso']))
		{
			return false;
		}
		
		$aChildren = $this->database()->select('*')
			->from(Phpfox::getT('country_child'))
			->where('country_iso = \'' . $this->database()->escape($sIso) . '\'')
			->execute('getSlaveRows');
			
		if (!count($aChildren))
		{
			return false;
		}
		
		$oXmlBuilder = Phpfox::getLib('xml.builder');
		$oXmlBuilder->addGroup('country');
		$oXmlBuilder->addGroup('info');
		$oXmlBuilder->addTag('iso', $aCountry['country_iso']);
		$oXmlBuilder->addTag('name', $aCountry['name']);
		$oXmlBuilder->closeGroup();
		
		if (count($aChildren))
		{
			$oXmlBuilder->addGroup('children');			
			foreach ($aChildren as $aChild)
			{
				$oXmlBuilder->addTag('child', $aChild['name']);					
			}			
			$oXmlBuilder->closeGroup();
		}
		$oXmlBuilder->closeGroup();
				
		$sCacheName = 'country_export_cache_' . md5($aCountry['country_iso'] . PHPFOX_TIME) . '.xml';
		
		Phpfox_File::instance()->writeToCache($sCacheName, $oXmlBuilder->output());
        
        return [
            'name' => $aCountry['country_iso'],
            'file' => $sCacheName
        ];
    }
    
    /**
     * @param int $iChildId
     *
     * @return string
     */
    public function getChild($iChildId)
	{
		static $bIsChecked = false;
		
		if ($bIsChecked === false)
		{
			$sCacheId = $this->cache()->set('country_child_list_' . Phpfox_Locale::instance()->getLangId());

			if (!($this->_aChildren = $this->cache()->get($sCacheId)))
			{
				$aRows = $this->database()->select('child_id, name')
					->from(Phpfox::getT('country_child'))					
					->execute('getSlaveRows');
					
				foreach ($aRows as $aRow)
				{
					$this->_aChildren[$aRow['child_id']] = (Core\Lib::phrase()->isPhrase('core.translate_country_child_' . strtolower($aRow['child_id'])) ? _p('translate_country_child_' . strtolower($aRow['child_id'])) : $aRow['name']);
				}
				
				$this->cache()->save($sCacheId, $this->_aChildren);
                Phpfox::getLib('cache')->group( 'country', $sCacheId);
			}
			
			$bIsChecked = true;
		}
		
		return (isset($this->_aChildren[$iChildId]) ? Phpfox::getPhraseT($this->_aChildren[$iChildId], 'country_child') : '');
	}
    
    /**
     * @param string $sCountry
     * @param int    $iChild
     *
     * @return int
     */
	public function getValidChildId($sCountry, $iChild)
	{
		$aChildren = $this->getChildren($sCountry);
        if (!count($aChildren)) {
            return 0;
        }
		
		return $iChild;
	}
    
    /**
     * @return array
     */
	public function getCountriesAndChildren()
	{
		$sCacheId = $this->cache()->set('countries_and_children_' . Phpfox_Locale::instance()->getLangId());

		if (!($aCountries = $this->cache()->get($sCacheId)))
		{
			$aAll = $this->database()->select('cc.child_id, cc.name as child_name, c.country_iso, c.name as country_name')
				->from(Phpfox::getT('country'), 'c')
				->leftJoin(Phpfox::getT('country_child'), 'cc', 'cc.country_iso = c.country_iso')
				->order('c.name ASC')
				->execute('getSlaveRows');
			$aCountries = array();
			foreach ($aAll as $aItem)
			{
				if (!isset($aCountries[$aItem['country_iso']]))
				{
					if(!preg_match('/&#[A-F0-9]+/i', $aItem['country_name']))
					{
						// Means, it does not contains unicode, therefore, it was not processed or added through phpFox
						$aItem['country_name'] = htmlspecialchars(htmlentities($aItem['country_name'], ENT_QUOTES), ENT_QUOTES);
					}
					// END
					
					$aCountries[$aItem['country_iso']] =  array(
						'name' => $aItem['country_name'],
						'country_iso' => $aItem['country_iso'],
						'children' => array()
					);
				}
				
				if (isset($aItem['child_id']) && !empty($aItem['child_id']))
				{
                    $aItem['child_name_decoded'] = htmlspecialchars($aItem['child_name'], ENT_QUOTES);
					if(!preg_match('/&#[A-F0-9]+/i', $aItem['child_name']))
					{
						// Means, it does not contains unicode, therefore, it was not processed or added through PHPFox
						$aItem['child_name'] = htmlspecialchars(htmlentities($aItem['child_name'], ENT_QUOTES), ENT_QUOTES);
					}
					// END
					
					$aCountries[$aItem['country_iso']]['children'][$aItem['child_id']] = array(
						'name' => $aItem['child_name'],
						'name_decoded' => htmlspecialchars($aItem['child_name_decoded'], ENT_QUOTES),
						'child_id' => $aItem['child_id']
					);
				}
			}
			
			$this->cache()->save($sCacheId, $aCountries);
            Phpfox::getLib('cache')->group( 'country', $sCacheId);
		}
		
		return $aCountries;
	}
    
    /**
     * @param $sCountry
     *
     * @return array
     */
	public function getChildren($sCountry)
	{
	    if (empty($sCountry)) {
	        return [];
        }
		$sCacheId = $this->cache()->set('country_child_' . $sCountry . '_' . Phpfox_Locale::instance()->getLangId());

		if (!($aChildrenData = $this->cache()->get($sCacheId)))
		{
			$aChildren = $this->database()->select('child_id, name')
				->from(Phpfox::getT('country_child'))
				->where('country_iso = \'' . $this->database()->escape($sCountry) . '\'')
				->order('ordering ASC, name ASC')	
				->execute('getSlaveRows');
				
			$aChildrenData = array();
			foreach ($aChildren as $aChild)
			{
				$aChildrenData[$aChild['child_id']] = (Core\Lib::phrase()->isPhrase('core.translate_country_child_' . strtolower($aChild['child_id'])) ? _p('translate_country_child_' . strtolower($aChild['child_id'])) : $aChild['name']);
			}	
			
			$this->cache()->save($sCacheId, $aChildrenData);
            Phpfox::getLib('cache')->group( 'country', $sCacheId);
		}
        
        if (!is_array($aChildrenData)) {
            $aChildrenData = [];
        }
        
        return $aChildrenData;
	}
    
    /**
     * @param null|string $sIso
     *
     * @return array
     */
	public function getForEdit($sIso = null)
	{
		if ($sIso !== null)
		{
			$this->database()->where('c.country_iso = \'' . $this->database()->escape($sIso) . '\'');
		}
		return $this->database()->select('c.*, COUNT(cc.child_id) AS total_children')
			->from(Phpfox::getT('country'), 'c')
			->leftJoin(Phpfox::getT('country_child'), 'cc', 'cc.country_iso = c.country_iso')
			->group('c.country_iso')
			->order('c.ordering ASC, c.name ASC')
			->execute(($sIso == null ? 'getSlaveRows' : 'getSlaveRow'));
	}
    
    /**
     * @param string $sIso
     *
     * @return array
     */
	public function getChildForEdit($sIso)
	{
		$aChildCountries =  $this->database()->select('cc.*')
			->from(Phpfox::getT('country_child'), 'cc')		
			->where('cc.country_iso = \'' . $this->database()->escape($sIso) . '\'')	
			->order('cc.ordering ASC, cc.name ASC')
			->execute('getSlaveRows');
        return $aChildCountries;
	}
    
    /**
     * @param int $iId
     *
     * @return array
     */
	public function getChildEdit($iId)
	{
		$aChildCountry = $this->database()->select('cc.*')
			->from(Phpfox::getT('country_child'), 'cc')
			->where('cc.child_id = ' . (int) $iId)
			->execute('getSlaveRow');
        return $aChildCountry;
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
		if ($sPlugin = Phpfox_Plugin::get('core.service_country_country__call'))
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
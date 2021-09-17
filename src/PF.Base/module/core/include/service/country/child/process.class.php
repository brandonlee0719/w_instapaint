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
 * @version 		$Id: process.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Core_Service_Country_Child_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('country_child');
	}
    
    /**
     * @param array $aVals
     *
     * @return bool
     */
	public function add($aVals)
	{
		if (empty($aVals['name']))
		{
			return Phpfox_Error::set(_p('provide_a_name'));
		}
		
		if (empty($aVals['country_iso']))
		{
			return Phpfox_Error::set(_p('select_a_country'));
		}
		
		$iIsChild = $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('country_iso = \'' . $aVals['country_iso'] . '\' AND name = \'' . $this->database()->escape($this->preParse()->clean($aVals['name'])) . '\'')
			->execute('getSlaveField');
			
		if ($iIsChild)
		{
			return Phpfox_Error::set(_p('the_state_province_name_already_exists', array('name' => $this->preParse()->clean($aVals['name']))));
		}
		
		$this->database()->insert($this->_sTable, array(
				'country_iso' => $aVals['country_iso'],
				'name' => $this->preParse()->clean($aVals['name'])
			)
		);

        $this->cache()->remove();
		
		return true;
	}
    
    /**
     * @param int   $iId
     * @param array $aVals
     *
     * @return bool
     */
	public function update($iId, $aVals)
	{
		if (empty($aVals['name']))
		{
			return Phpfox_Error::set(_p('provide_a_name'));
		}
		
		$this->database()->update($this->_sTable, array(				
				'name' => $this->preParse()->clean($aVals['name'])
			), 'child_id = ' . (int) $iId
		);

        $this->cache()->removeGroup('country');
		
		return true;
	}
    
    /**
     * @param int $iId
     *
     * @return bool
     */
	public function delete($iId)
	{
		$this->database()->delete($this->_sTable, 'child_id = ' . (int) $iId);

        $this->cache()->removeGroup('country');
		
		return true;
	}
    
    /**
     * @param string $sCountry
     *
     * @return bool
     */
	public function deleteAll($sCountry)
	{
		$this->database()->delete($this->_sTable, 'country_iso = \'' . $this->database()->escape($sCountry) . '\'');

        $this->cache()->removeGroup('country');
		
		return true;
	}
    
    /**
     * @param array $aVals
     *
     * @return bool
     */
	public function translate($aVals)
	{
		$sPhraseName = 'translate_country_child_' . strtolower($aVals['child_id']);
		
		$bUpdate = false;
		$aNewData = array('text' => array());
		foreach ($aVals['text'] as $aData)
		{
			if (is_array($aData))
			{
				$bUpdate = true;
				foreach ($aData as $sLang => $sData)
				{
					$aNewData['text'][$sLang] = $sData;	
				}
			}
		}
        
        Phpfox::getService('language.phrase.process')->delete('core.' . $sPhraseName, true);
		
		$sFinalPhrase = Phpfox::getService('language.phrase.process')->add(array(
				'var_name' => $sPhraseName,
				'text' => ($bUpdate ? $aNewData['text'] : $aVals['text'])
			)
		);
		
		$this->database()->update(Phpfox::getT('country_child'), array(
				'phrase_var_name' => $sFinalPhrase
			), 'child_id = ' . (int) $aVals['child_id']
		);

        $this->cache()->removeGroup('country');
		
		return true;
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
		if ($sPlugin = Phpfox_Plugin::get('core.service_country_child_process__call'))
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
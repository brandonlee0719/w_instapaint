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
 * @version 		$Id: process.class.php 1572 2010-05-06 12:37:24Z Raymond_Benc $
 */
class Core_Service_Country_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('country');
	}
    
    /**
     * @param array $aVals
     *
     * @return bool
     */
	public function add($aVals)
	{
		if (empty($aVals['country_iso']) || empty($aVals['name']))
		{
			return Phpfox_Error::set(_p('all_fields_are_required'));
		}
		
		if (strlen($aVals['country_iso']) > 2)
		{
			return Phpfox_Error::set(_p('iso_can_only_contain_2_characters'));
		}
		
		$iIsCountry = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('country'))
			->where('country_iso = \'' . $this->database()->escape($aVals['country_iso']) . '\'')
			->execute('getSlaveField');
			
		if ($iIsCountry)
		{
			return Phpfox_Error::set(_p('the_iso_is_already_in_use'));
		}
		
		$iOrdering = $this->database()->select('COUNT(*)')->from(Phpfox::getT('country'))->execute('getSlaveField');
		$this->database()->insert(Phpfox::getT('country'), array(
				'country_iso' => $aVals['country_iso'],
				'name' => $this->preParse()->clean($aVals['name']),
				'ordering' => ($iOrdering + 1)
			)
		);
		
		$this->cache()->removeGroup('country');
		
		return true;		
	}
    
    /**
     * @param string $sIso
     *
     * @return bool
     */
	public function delete($sIso)
	{
		$this->database()->delete(Phpfox::getT('country'), 'country_iso = \'' . $this->database()->escape($sIso) . '\'');
		$this->database()->delete(Phpfox::getT('country_child'), 'country_iso = \'' . $this->database()->escape($sIso) . '\'');

        $this->cache()->removeGroup('country');
		
		return true;
	}
    
    /**
     * @param string $sIso
     * @param array  $aVals
     *
     * @return bool
     */
	public function update($sIso, $aVals)
	{
		if (!isset($aVals['country_iso']) || empty($aVals['country_iso']) || !isset($aVals['name']) || empty($aVals['name']))
		{
			return Phpfox_Error::set(_p('all_fields_are_required'));
		}
		
		if (strlen($aVals['country_iso']) > 2)
		{
			return Phpfox_Error::set(_p('iso_can_only_contain_2_characters'));
		}		

		if ($sIso != $aVals['country_iso']) {
            $iIsCountry = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('country'))
                ->where('country_iso = \'' . $this->database()->escape($aVals['country_iso']) . '\'')
                ->execute('getSlaveField');

            if ($iIsCountry) {
                return Phpfox_Error::set(_p('the_iso_is_already_in_use'));
            }
        }

		$this->database()->update(Phpfox::getT('country'), array(
				'country_iso' => $aVals['country_iso'],
				'name' => $this->preParse()->clean($aVals['name'])
			), 'country_iso = \'' . $this->database()->escape($sIso) . '\''
		);
		
		$this->cache()->remove();
		
		return true;
	}
    
    /**
     * @param array $aVals
     * @param array $aFile
     *
     * @return array
     */
	public function importFromText($aVals, $aFile)
	{	
		Phpfox::getLib('cache')->lock();	
				
		$sLines = file_get_contents($aFile['tmp_name']);
		if (isset($aVals['utf_encoding']) && $aVals['utf_encoding'])
		{
			$sLines = utf8_encode($sLines);			
		}		
		
		$aLines = explode("\n", $sLines);
        $aLog = [
            'completed' => 0,
            'failed'    => 0
        ];
        foreach ($aLines as $sLine)
		{
			$sLine = trim($sLine);
			
			if (empty($sLine))
			{
				continue;
			}
			
			(Phpfox::getService('core.country.child.process')->add(array('country_iso' => $aVals['country_iso'], 'name' => $this->preParse()->clean($sLine))) ? $aLog['completed']++ : $aLog['failed']++);
		}	
		
		Phpfox::getLib('cache')->unlock();

        $this->cache()->removeGroup('country');
			
		return $aLog;
	}
    
    /**
     * @param array $aFiles
     * @param bool  $bOverwrite
     *
     * @return bool
     */
	public function import($aFiles, $bOverwrite = false)
	{
		if (empty($aFiles['name']))
		{
			return Phpfox_Error::set(_p('not_a_valid_country_package'));
		}
		
		if (!preg_match('/phpfox-country-(.*)\.xml/i', $aFiles['name']))
		{
			return Phpfox_Error::set(_p('not_a_valid_country_package_must_be_an_xml_file'));
		}
		
		$sContent = file_get_contents($aFiles['tmp_name']);
		
		if (!preg_match('/<country>(.*?)<\/country>/ise', $sContent))
		{
			return Phpfox_Error::set(_p('not_a_valid_country_package_must_be_an_xml_file'));
		}
		
		$aData = Phpfox::getLib('xml.parser')->parse($sContent);		
				
		if (!isset($aData['info']['iso']))
		{
			return Phpfox_Error::set(_p('not_a_valid_country_package'));
		}
		
		Phpfox::getLib('cache')->lock();
		
		if ($bOverwrite)
		{
			$this->delete($aData['info']['iso']);
		}
        
        if (!$this->add([
                'country_iso' => $aData['info']['iso'],
                'name'        => $aData['info']['name']
            ])
        ) {
            return false;
        }
        
        if (isset($aData['children']['child']))
		{
			if (is_array($aData['children']['child']))
			{
				foreach ($aData['children']['child'] as $sChild)
				{
					Phpfox::getService('core.country.child.process')->add(array('country_iso' => $aData['info']['iso'], 'name' => $sChild));
				}
			}
		}
				
		Phpfox::getLib('cache')->unlock();

        $this->cache()->removeGroup('country');
		
		return true;
	}
    
    /**
     * @param array $aVals
     * @param bool $bMissingOnly
     */
	public function importForInstall($aVals, $bMissingOnly = false)
	{
		$aSql = array();
		foreach ($aVals['country'] as $aValue)
		{
			$aSql[] = array(
				$aValue['iso'],
				$aValue['name']
			);
		}		
		
		if (!$bMissingOnly){
			$this->database()->multiInsert($this->_sTable, array(
				'country_iso',
				'name'
			), $aSql);	
		}
	}
    
    /**
     * @param array $aVals
     *
     * @return bool
     */
	public function translate($aVals)
	{
		$sPhraseName = 'translate_country_iso_' . strtolower($aVals['country_iso']);
		
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
		
		$this->database()->update(Phpfox::getT('country'), array(
				'phrase_var_name' => $sFinalPhrase
			), 'country_iso = \'' . $this->database()->escape($aVals['country_iso']) . '\''
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
		if ($sPlugin = Phpfox_Plugin::get('core.service_country_process__call'))
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
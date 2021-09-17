<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Language_Service_Phrase_Process
 */
class Language_Service_Phrase_Process extends Phpfox_Service 
{
    /**
     * @var string
     */
    protected $_sTable = '';

	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('language_phrase');
		(($sPlugin = Phpfox_Plugin::get('language.service_phrase_process___construct')) ? eval($sPlugin) : false);
	}
    
    /**
     * Clean a var_name for phrase
     *
     * @param $sText
     *
     * @return string
     */
	public function prepare($sText)
	{
		static $aCache = array();
        
        if (isset($aCache[$sText])) {
            return $aCache[$sText];
        }
		
		$sText = trim($sText);	
		
		$sPhrase = $sText;
		
		$aCache[$sText] = strtolower(preg_replace('/ +/', '-', preg_replace('/[^0-9a-zA-Z]+/', '_', $sPhrase)));
		
		$aCache[$sText] = trim($aCache[$sText], '_');
        
        if (empty($aCache[$sText])) {
            $aCache[$sText] = uniqid();
        }
				
		return $aCache[$sText];
	}
    
    /**
     * Add a phrase
     *
     * @param array $aVals used key: var_name, text.
     * @param bool  $bClean
     *
     * @return mixed|string
     */
	public function add($aVals, $bClean = false)
	{
        if (isset($aVals['type']) && $aVals['type'] == 1) {
            $aVals['var_name'] = 'app_' . md5($aVals['phrase']);
            foreach ($aVals['text'] as $iId => $sText) {
                $aVals['text'][$iId] = $aVals['phrase'];
            }
        }
		$sPhrase = $this->prepare($aVals['var_name']);
		$oParseInput = Phpfox_Parse_Input::instance();
		$sDefaultLanguage = Phpfox::getService('language')->getDefaultLanguage();
		
		foreach ($aVals['text'] as $iId => $sText) {
		    if (empty($sText) && isset($aVals['text'][$sDefaultLanguage])) {
		        $sText = $aVals['text'][$sDefaultLanguage];
            }
			$sText = trim($sText);
            if ($bClean === true) {
                $sText = $oParseInput->clean($sText);
            } else {
                $sText = $oParseInput->convert($sText);
            }

            $bInsert = empty($aVals['update']);
			if (!$bInsert) {
			    $aWhere = ['language_id' => $iId, 'var_name' => $sPhrase];
			    $aPhrase = $this->database()->select('*')->from($this->_sTable)->where($aWhere)->executeRow();
			    if (empty($aPhrase)) {
                    $bInsert = true;
                }
                else {
                    $this->database()->update($this->_sTable, ['text' => $sText], ['language_id' => $iId, 'var_name' => $sPhrase]);
                }
            }
            if($bInsert) {
                $this->database()->insert($this->_sTable, array(
                    'language_id' => $iId,
                    'var_name' => $sPhrase,
                    'text' => $sText,
                    'text_default' => $sText,
                    'added' => PHPFOX_TIME
                ));
            }
		}
        Core\Lib::phrase()->clearCache();
		$sFinalPhrase = $sPhrase;
		
		Phpfox::getService('log.staff')->add('phrase', 'add', ['phrase' => $sPhrase]);
        
		return $sFinalPhrase;
	}
    
    /**
     * Update a phrase
     *
     * @param int    $iId
     * @param string $sText
     * @param array  $aExtra
     *
     * @return bool
     */
	public function update($iId, $sText, $aExtra = [])
    {
        $aUpdate = [
            'text' => Phpfox::getLib('parse.input')->convert($sText)
        ];
        
        if ($aExtra) {
            $aUpdate = array_merge($aUpdate, $aExtra);
        }
		
		$this->database()->update($this->_sTable, $aUpdate, 'phrase_id = ' . (int) $iId);
		
        Phpfox::getLib('cache')->removeGroup('language');
		
		return true;
	}
    
    /**
     * Delete a phrase
     *
     * @param int|string $mId
     * @param bool       $bIsVar
     *
     * @return bool
     */
	public function delete($mId, $bIsVar = false)
	{
        if ($bIsVar) {
            //Support legacy phrase. End support from 4.7.0
            $mId = Core\Lib::phrase()->correctLegacyPhrase($mId);
            //End support legacy
        }

		$this->database()->delete($this->_sTable, ($bIsVar ? "var_name = '" . $this->database()->escape($mId) . "'" : 'phrase_id = ' . (int) $mId));

        Phpfox::getLib('cache')->removeGroup('locale');
		return true;
	}
    
    /**
     * Revert phrase to default
     *
     * @param array $aIds
     *
     * @return bool
     */
	public function revert($aIds)
	{
        if (!is_array($aIds)) {
            return false;
        }
        
        if (!count($aIds)) {
            return false;
        }
		
		$aRows = $this->database()->select('phrase_id, text_default')
			->from($this->_sTable)
			->where("phrase_id IN(" . implode(',', $aIds) . ")")
			->execute('getSlaveRows');
        
        foreach ($aRows as $aRow) {
            $this->update($aRow['phrase_id'], $aRow['text_default']);
        }

        Phpfox::getLib('cache')->removeGroup('language');

		return true;
	}
    
    /**
     * Update var_name of already phrase
     *
     * @param string $sLanguage
     * @param string $sVarName
     * @param string $sValue
     * @param bool   $bOverWrite
     *
     * @return bool
     */
	public function updateVarName($sLanguage, $sVarName, $sValue, $bOverWrite = false)
	{
        //Support legacy phrase. End support from 4.7.0
        $sVarName = Core\Lib::phrase()->correctLegacyPhrase($sVarName);
        //End support legacy
        
        $aSql = [
            'text' => Phpfox::getLib('parse.input')->convert($sValue)
        ];
        
        if ($bOverWrite) {
            $aSql['text_default'] = Phpfox::getLib('parse.input')->convert($sValue);
        }
		
		$this->database()->update($this->_sTable, $aSql, 'language_id = \'' . $this->database()->escape($sLanguage) . '\' AND var_name = \'' . $this->database()->escape($sVarName) . '\'');
        
        Core\Lib::phrase()->clearCache();
        
		return true;
	}
    
    /**
     * Import a language
     *
     * @param string $sLanguageId
     * @param int    $iPage
     * @param int    $iLimit
     *
     * @return array|bool|int|string
     */
	public function importPhrases($sLanguageId, $iPage = 0, $iLimit = 500)
	{		
		$aLanguage = $this->database()->select('*')
			->from(Phpfox::getT('language'))
			->where('language_id = \'' . $this->database()->escape($sLanguageId) . '\'')
			->execute('getSlaveRow');
        
        if (!isset($aLanguage['language_id'])) {
            return Phpfox_Error::set(_p('language_package_not_found'));
        }
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('language_phrase'))
			->where('language_id = \'' . $this->database()->escape($aLanguage['parent_id']) . '\'')
			->execute('getSlaveField');
        
        if (!$iCnt) {
            return false;
        }
		
		$aParentPhrases = $this->database()->select('*')
			->from(Phpfox::getT('language_phrase'))
			->where('language_id = "' . $this->database()->escape($aLanguage['parent_id']) . '"')
			->limit($iPage, $iLimit, $iCnt)
			->order('phrase_id ASC')
			->execute('getSlaveRows');
        
        foreach ($aParentPhrases as $aParentPhrase) {
            $aInsert = [];
            foreach ($aParentPhrase as $sKey => $sValue) {
                if ($sKey == 'phrase_id') {
                    continue;
                }
                
                if ($sKey == 'language_id') {
                    $sValue = $sLanguageId;
                }
                
                $aInsert[$sKey] = $sValue;
            }
            
            $this->database()->insert(Phpfox::getT('language_phrase'), $aInsert);
        }
        
        return $iCnt;
	}
    
    /**
     * @deprecated from 4.6.0
     *
     * @param string $sPack
     * @param string $sDir
     * @param int    $iPage
     * @param int    $iLimit
     *
     * @return bool|string
     */
	public function installFromFolder($sPack, $sDir, $iPage = 0, $iLimit = 5)
	{
        $iGroup = (($iPage * $iLimit) + 1);
        
        if (!is_dir($sDir)) {
            return Phpfox_Error::set(_p('not_a_valid_language_package_to_install'));
        }
        
        if (!file_exists($sDir . 'phpfox-language-import.xml')) {
            return Phpfox_Error::set(_p('not_a_valid_language_package_to_install_missing_the_xml_file'));
        }
        
        $iCnt = 0;
        $iActualCount = 0;
        $hDir = opendir($sDir);
        while ($sFile = readdir($hDir)) {
            if ($sFile == '.' || $sFile == '..') {
                continue;
            }
            
            if (preg_match('/^module-(.*?)\.xml$/i', $sFile, $aMatches)) {
                if (Phpfox::isModule($aMatches[1])) {
                    $iActualCount++;
                    
                    if ($iActualCount < $iGroup) {
                        continue;
                    }
                    
                    $aPhrases = Phpfox::getLib('xml.parser')->parse(file_get_contents($sDir . $sFile), 'UTF-8');
                    $aRows = (isset($aPhrases['phrase'][1]) ? $aPhrases['phrase'] : [$aPhrases['phrase']]);
                    foreach ($aRows as $aPhrase) {
                        $this->database()->insert(Phpfox::getT('language_phrase'), [
                            'language_id'  => $sPack,
                            'var_name'     => $aPhrase['var_name'],
                            'text'         => $aPhrase['value'],
                            'text_default' => $aPhrase['value'],
                            'added'        => $aPhrase['added']
                        ]);
                    }
                    
                    $iCnt++;
                    
                    if ($iCnt === (int)$iLimit) {
                        break;
                    }
                }
            }
        }
        closedir($hDir);
        
        return ($iCnt ? true : 'done');
    }
    
    /**
     * This function updates language phrases based on the changes made by the user from the
     * controller admincp.language.email
     * In this context phrase_id is the full phrase variable: <module>.<var_name>
     *
     * @param array $aVals
     *
     * @return true|string;
     */
	public function updateMailPhrases($aVals)
	{
		// Safety checks
        if (!isset($aVals['text']) || !is_array($aVals['text'])) {
            return Phpfox_Error::set(_p('this_shouldnt_happen_dot'));
        }
        
        if (isset($aVals['language_id']) && $aVals['language_id'] != '') {
            $sLanguage = $aVals['language_id'];
        } else {
            $sLanguage = Phpfox_Locale::instance()->getLang();
            $sLanguage = $sLanguage['language_id'];
        }
		
		foreach ($aVals['text'] as $sPhraseId => $sNewText)
		{
			// update the phrase
			$aVar = explode('.', $sPhraseId);
			$aUpdate = array(
				'text' => Phpfox::getLib('parse.input')->convert($sNewText)
			);
			$sWhere = 'language_id = "' . $sLanguage . '" AND var_name = "' . $aVar[1] . '"';
			$this->database()->update($this->_sTable, $aUpdate, $sWhere);
		}
        Phpfox::getLib('cache')->removeGroup('locale');
		
		return true;
	}

    /**
     * @description: function to update all phrases for upgrade process
     * @param array $aPhrases
     * @return bool status
     */
    public function updatePhrases($aPhrases = []) {
        if (!is_array($aPhrases)) {
            return false;
        }
        foreach ($aPhrases as $sVarName => $sText) {
            $this->database()->update($this->_sTable, [
                'text' => Phpfox::getLib('parse.input')->convert($sText),
                'text_default' => Phpfox::getLib('parse.input')->convert($sText)
            ], 'language_id = \'en\' AND var_name=\'' . $sVarName . '\' AND text=text_default');
        }
    }
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return null;
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('language.service_phrase_process__call'))
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
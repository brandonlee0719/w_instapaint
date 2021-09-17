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
 * @package  		Module_Language
 * @version 		$Id: language.class.php 4605 2012-08-20 11:17:45Z Miguel_Espinoza $
 */
class Language_Service_Language extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('language');
	}
    
    /**
     * get some phrases by conditions
     *
     * @param array $aConds
     *
     * @return array
     */
	public function get($aConds = array())
	{
		$sCacheId = $this->cache()->set(array('locale', 'language_table_' . md5((is_array($aConds) ? implode('', $aConds) : $aConds))));
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('l.*')
				->from($this->_sTable, 'l')
				->where($aConds)
				->order('l.is_default DESC, l.title')
				->execute('getSlaveRows');		
				
			foreach ($aRows as $iKey => $aRow)
			{
			    if (empty($aRow['flag_id'])) {
			        continue;
                }

                $sFlagUrl = Phpfox::getLib('image.helper')->display([
                    'server_id' => $aRow['server_id'],
                    'path' => 'core.url_pic',
                    'file' => 'flag/' . $aRow['language_id'] . '.' . $aRow['flag_id'],
                    'return_url' => true
                ]);

                if ((!$aRow['server_id'] && !file_exists($sFlagUrl)) ||
                    ($aRow['server_id'] && !Phpfox::getLib('image.helper')->checkRemoteFileExists($sFlagUrl))
                ) {
                    continue;
                }

                $aRows[$iKey]['image'] = $sFlagUrl;
			}
				
			$this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group(  'locale', $sCacheId);
		}
		
		$this->database()->clean();
		
		return $aRows;
	}
    
    /**
     * get phrases for admin
     *
     * @param array $aConds
     *
     * @return array
     */
	public function getForAdminCp($aConds = [])
	{
		$aRows = $this->database()->select('l.*')
			->from($this->_sTable, 'l')
			->where($aConds)
			->order('l.is_default DESC, l.title')
			->execute('getSlaveRows');		
				
		foreach ($aRows as $iKey => $aRow)
		{
			$aRows[$iKey]['image'] = (file_exists(Phpfox::getParam('core.dir_pic') . 'flag' . PHPFOX_DS . $aRow['language_id'] . '.' . $aRow['flag_id']) ? Phpfox::getParam('core.url_pic') . 'flag/' . $aRow['language_id'] . '.' . $aRow['flag_id'] : '');
            if (isset($aRow['store_id']) && $aRow['store_id']) {
                $store = json_decode(@fox_get_contents('https://store.phpfox.com/product/' . $aRow['store_id'] . '/view.json'),
                    true);
                if (isset($store['id']) && version_compare($aRow['version'], $store['version'], '<')) {
                    $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
                    $response = $Home->admincp(['return' => Phpfox::getLib('url')->makeUrl('admincp.app.add')]);
                    $aRows[$iKey]['latest_version'] = $store['version'];
                    $aRows[$iKey]['latest_version_url'] = $store['url'] . '/installing?iframe-mode=' . $response->token;
                } elseif (isset($store['id']) && $store['version']) {
                    $aRows[$iKey]['latest_version'] = $store['version'];
                } else {
                    $aRows[$iKey]['latest_version'] = _p('n_a');
                }
            } else {
                $aRows[$iKey]['latest_version'] = _p('n_a');
            }
		}	
		
		return $aRows;
	}
    
    /**
     * Get all language package of site
     *
     * @param boolean $defaultFirst
     *
     * @return array
     */
	public function getAll($defaultFirst = true)
    {
        if ($defaultFirst) {
            $aLanguage = $this->database()->select('*')
                ->from(Phpfox::getT('language'))
                ->order('is_default DESC')
                ->execute('getSlaveRows');
        }
        else {
            $aLanguage = $this->database()->select('*')
                ->from(Phpfox::getT('language'))
                ->execute('getSlaveRows');
        }
		return $aLanguage;
	}
    
    /**
     * @param $sPhrase
     *
     * @return array
     */
	public function getWithPhrase($sPhrase)
	{
		$aRows = $this->database()->select('l.language_id, l.title, p.phrase_id, p.text')
			->from($this->_sTable, 'l')
			->leftJoin(Phpfox::getT('language_phrase'), 'p', "p.language_id = l.language_id AND p.var_name = '" . $this->database()->escape($sPhrase) . "'")
            ->order('l.is_default DESC')
			->execute('getSlaveRows');
        
        $aLanguages = [];
        foreach ($aRows as $aRow)
		{
			$aLanguages[$aRow['language_id']] = $aRow;
		}		
		
		return $aLanguages;
	}
    
    /**
     * @param int $iId
     *
     * @return array|false
     */
	public function getLanguage($iId)
	{		
		$aRow = $this->database()->select('l.*')
			->from($this->_sTable, 'l')
			->where('l.language_id = \'' . $this->database()->escape($iId) . '\'')
			->execute('getSlaveRow');
			
		if (!isset($aRow['language_id']))
		{
			return false;
		}

        $aRow['image'] = '';
		if ($aRow['flag_id']) {
            $aRow['image'] = Phpfox::getLib('image.helper')->display([
                'server_id' => $aRow['server_id'],
                'path' => 'core.url_pic',
                'file' => 'flag/' . $aRow['language_id'] . '.' . $aRow['flag_id'],
                'return_url' => true
            ]);
        }

		return $aRow;
	}
    
    /**
     * @param string $sName
     *
     * @return array
     */
	public function getLanguageByName($sName)
	{		
		return $this->database()->select('l.*')
			->from($this->_sTable, 'l')
			->where("l.title = '" . $this->database()->escape($sName) . "'")
			->execute('getSlaveRow');			
	}
    
    /**
     * @param string $sLanguageId
     * @param bool   $bDoCustom
     *
     * @return array|false
     */
	public function exportForDownload($sLanguageId, $bDoCustom = false)
	{
        if (!defined('PHPFOX_XML_SKIP_STAMP')) {
            define('PHPFOX_XML_SKIP_STAMP', true);
        }
		
		$oXmlBuilder = Phpfox::getLib('xml.builder');
		
		$aLanguage = $this->getLanguage($sLanguageId);
			
		if (!isset($aLanguage['language_id']))
		{
			return false;
		}
								
		$sFolder = md5($aLanguage['language_id'] . uniqid() . Phpfox::getUserId());
		$sFullPath = PHPFOX_DIR_CACHE . $sFolder . PHPFOX_DS . 'upload' . PHPFOX_DS . 'include' . PHPFOX_DS . 'xml' . PHPFOX_DS . 'language' . PHPFOX_DS . $aLanguage['language_id'] . PHPFOX_DS;

		Phpfox_File::instance()->mkdir($sFullPath, true);
		
		$oXmlBuilder->addGroup('language');				
		$oXmlBuilder->addGroup('settings');
		foreach ($aLanguage as $sKey => $sValue)
		{
			if ($sKey == 'language_id' || $sKey == 'is_default' || $sKey == 'is_master' || $sKey == 'image')
			{
				continue;
			}
			$oXmlBuilder->addTag($sKey, $sValue);
		}
		
		if (!empty($aLanguage['image']))
		{
			$oXmlBuilder->addTag('image', base64_encode(fox_get_contents(str_replace(Phpfox::getParam('core.url_pic'), Phpfox::getParam('core.dir_pic'), $aLanguage['image']))));
		}
		$oXmlBuilder->closeGroup();
		$oXmlBuilder->closeGroup();
			
		Phpfox_File::instance()->write($sFullPath . 'phpfox-language-import.xml', $oXmlBuilder->output());
		
        //Assume all phrase belong to module core
        $this->export($aLanguage['language_id'], true, false);
        Phpfox_File::instance()->write($sFullPath . 'module-' . 'core' . '.xml', $oXmlBuilder->output());
        
        $iServerId = 0;
        if (Phpfox::getParam('core.allow_cdn')) {
            $iServerId = Phpfox::getLib('cdn')->getServerId();
        }
        
        return [
            'name'      => $aLanguage['language_id'],
            'folder'    => $sFolder,
            'server_id' => $iServerId
        ];
    }
    
    /**
     * @param int  $iLanguageId
     * @param bool $bOnlyPhrases
     * @param bool $bCore
     *
     * @return bool
     */
	public function export($iLanguageId, $bOnlyPhrases = false, $bCore = false)
	{
		$aPhrases = $this->database()->select('lp.*')
			->from(':language_phrase', 'lp')
			->where("lp.language_id = '" . $iLanguageId . "'")
			->order('lp.phrase_id ASC')
			->executeRows();
        
        if (!count($aPhrases)) {
            return false;
        }
		
		$oXmlBuilder = Phpfox::getLib('xml.builder');
        
        if (!$bOnlyPhrases) {
            $aLanguage = $this->database()
                ->select('l.*')
                ->from($this->_sTable, 'l')
                ->where("l.language_id = '" . $iLanguageId . "'")
                ->executeRow();
            
            $oXmlBuilder->addGroup('language');
            
            $oXmlBuilder->addGroup('settings');
            foreach ($aLanguage as $sKey => $sValue) {
                if ($sKey == 'language_id' || $sKey == 'is_default' || $sKey == 'is_master') {
                    continue;
                }
                $oXmlBuilder->addTag($sKey, $sValue);
            }
            $oXmlBuilder->closeGroup();
        }
		
		$oXmlBuilder->addGroup('phrases');
        
        $aCache = [];
        foreach ($aPhrases as $aPhrase) {
            if (isset($aCache[$aPhrase['var_name']])) {
                continue;
            }
            
            $aCache[$aPhrase['var_name']] = true;
            
            $oXmlBuilder->addTag('phrase', $aPhrase[($bCore ? 'text_default' : 'text')], [
                    'var_name' => $aPhrase['var_name'],
                    'added'    => $aPhrase['added']
                ]);
        }
        $oXmlBuilder->closeGroup();
        
        if (!$bOnlyPhrases) {
            // Close main group
            $oXmlBuilder->closeGroup();
        }
        return true;
	}

    public function exportForModule($sModule = '')
    {
        $sPhrasePath = PHPFOX_DIR . 'module' . PHPFOX_DS . $sModule . PHPFOX_DS . 'phrase.json';
        if (file_exists($sPhrasePath)) {
            $aPhrases = json_decode(file_get_contents($sPhrasePath), true);
        } else {
            $aPhrases = [];
        }
        if (!count($aPhrases)) {
            return false;
        }

        $oXmlBuilder = Phpfox::getLib('xml.builder');

        $oXmlBuilder->addGroup('phrases');

        $aCache = [];
        foreach ($aPhrases as $var_name => $sPhrase) {
            if (isset($aCache[$var_name])) {
                continue;
            }

            $aCache[$var_name] = true;

            $oXmlBuilder->addTag('phrase', $sPhrase, [
                'var_name' => $var_name,
                'added'    => PHPFOX_TIME
            ]);
        }
        $oXmlBuilder->closeGroup();
        return true;
    }
    
    /**
     * @return array
     */
	public function getForInstall()
	{
		$aPacks = array();
		$sDir = PHPFOX_DIR_INCLUDE . 'xml' . PHPFOX_DS . 'language' . PHPFOX_DS;		
		$hDir = opendir($sDir);
		while ($sFolder = readdir($hDir))
		{
			if ($sFolder == '.' || $sFolder == '..')
			{
				continue;
			}
			
			if (!file_exists($sDir . $sFolder . PHPFOX_DS . 'phpfox-language-import.xml'))
			{
				continue;
			}
			
			$iCnt = $this->database()->select('COUNT(*)')
				->from(Phpfox::getT('language'))
				->where('language_id = \'' . $this->database()->escape($sFolder) . '\'')
				->execute('getSlaveField');
				
			if (!$iCnt)
			{
				$aData = Phpfox::getLib('xml.parser')->parse(file_get_contents($sDir . $sFolder . PHPFOX_DS . 'phpfox-language-import.xml'));
				
				$aPacks[] = array_merge(array('language_id' => $sFolder), $aData['settings']);
			}
		}
		closedir($hDir);
		
		return $aPacks;
	}
    
    /**
     * @deprecated will be removed from 4.6.0
     * This function scans every .php file in PHPFOX_DIR for >subject() and >message() and picks up
     * the language phrase used in each case then it renews the cache file
     *
     * @param bool $bForce Forces to create the cache file anew, if false it only returns the cache file if available
     *
     * @return array|string
     */
	public function getMailPhrases($bForce = false)
	{
	    return [];
	}
    
    /**
     * @deprecated will be removed from 4.6.0
     * @param string $sModule
     * @param string $sVar
     *
     * @return array|int|string
     */
	public function getPhraseInEveryLanguage($sModule, $sVar){}

    /**
     * @return string
     */
    public function getDefaultLanguage(){
        //todo cache this method
        $sLangId = $this->database()->select('language_id')
            ->from(Phpfox::getT('language'))
            ->where('is_default=1')
            ->execute('getSlaveField');
        
        if (isset($sLangId) && !empty($sLangId)){
            return $sLangId;
        } else {
            //Return English in case can't get default language
            return 'en';
        }
    }

    /**
     * validate input array
     * @param array $aVals
     * @param string $sKey
     * @param boolean $bIsArray
     * @param boolean $bIsRequired set false to support clone value of default language to other language if field isn't required
     * @param boolean $bJsClean
     *
     * @return mixed
     */
    public function validateInput($aVals, $sKey = 'name', $bIsArray = true, $bIsRequired = true, $bJsClean = true)
    {
        $sDefault = $this->getDefaultLanguage();
        $oParseInput = Phpfox::getLib('parse.input');
        if ($bIsArray) {
            $sValueDefault = $bJsClean ? strip_tags($aVals[$sKey][$sDefault], Phpfox::getParam('core.allowed_html')) : $aVals[$sKey][$sDefault];
            if (empty($sValueDefault)) {
                if ($bIsRequired) {
                    Phpfox_Error::set(_p('name_is_required'));

                    return false;
                } else {
                    return $aVals;
                }
            }

            $aLanguages = Phpfox::getService('language')->getAll(true);
            foreach ($aLanguages as $aLanguage) {
                $aVals[$sKey][$aLanguage['language_id']] = strip_tags($aVals[$sKey][$aLanguage['language_id']], Phpfox::getParam('core.allowed_html'));
                if (empty($aVals[$sKey][$aLanguage['language_id']])) {
                    $aVals[$sKey][$aLanguage['language_id']] = $sValueDefault;
                }
            }
        } else {
            $sValueDefault = $bJsClean ? strip_tags($aVals[$sKey . '_' . $sDefault], Phpfox::getParam('core.allowed_html')) : $aVals[$sKey . '_' . $sDefault];
            if (empty($sValueDefault)) {
                if ($bIsRequired) {
                    Phpfox_Error::set(_p('name_is_required'));

                    return false;
                } else {
                    return $aVals;
                }
            }

            $aLanguages = Phpfox::getService('language')->getAll(true);
            foreach ($aLanguages as $aLanguage) {
                $aVals[$sKey . '_' . $aLanguage['language_id']] = $bJsClean ? strip_tags($aVals[$sKey . '_' . $aLanguage['language_id']], Phpfox::getParam('core.allowed_html')) : $aVals[$sKey . '_' . $aLanguage['language_id']];
                if (empty($aVals[$sKey . '_' . $aLanguage['language_id']])) {
                    $aVals[$sKey . '_' . $aLanguage['language_id']] = $sValueDefault;
                }
            }
        }

        return $aVals;
    }
    
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     *
     * @return  null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('language.service_language__call'))
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
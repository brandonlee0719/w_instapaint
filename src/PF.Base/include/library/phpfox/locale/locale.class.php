<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Language Localization
 * Class is used to display all the phrases on the site allowing phpFox to support multiple languages
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: locale.class.php 6981 2013-12-09 17:40:04Z Fern $
 */
class Phpfox_Locale
{
	
	/**
	 * ARRAY of the current language package being used
	 *
	 * @var array
	 */
	private $_aLanguage = array();
	
	/**
	 * ARRAY of all the language packages
	 *
	 * @var array
	 */
	private $_aLanguages = array();
	
	
	/**
	 * Regex rules to manipulate phrases
	 *
	 * @var array
	 */
	private $_aRules = array();
	
	/**
	 * @var bool
	 */
	private $_sLanguagePackHelper = false;
    
    /**
     * Class constructor used to load the default language package and all the phrases that are part of that language
     * package. Also loads language rules for that specific language package. All this information is cached and
     * database queries are only executed the first time the site is loaded after a hard re-cache.
     */
	public function __construct()
	{
		$oCache = Phpfox::getLib('cache');
		$oDb = Phpfox_Database::instance();

		if (!defined('PHPFOX_INSTALLER')) {
			$this->_sLanguagePackHelper = Phpfox::getParam('language.lang_pack_helper');
		}
		$sLangAllId = $oCache->set(array('locale', 'language'));

		if (!($this->_aLanguages = $oCache->get($sLangAllId)))
		{
			$aRows = $oDb->select('*')
				->from(Phpfox::getT('language'))
				->execute('getRows');		
				
			foreach ($aRows as $aRow)
			{
				$this->_aLanguages[$aRow['language_id']] = true;
			}
			
			$oCache->save($sLangAllId, $this->_aLanguages);
            Phpfox::getLib('cache')->group('locale', $sLangAllId);
            Phpfox::getLib('cache')->group('language', $sLangAllId);
		}
		
		$sLangId = $oCache->set(array('locale', 'language_' . $this->getLangId()));

		if (!($this->_aLanguage = $oCache->get($sLangId)))
		{			
			$this->_aLanguage = $oDb->select('*')
				->from(Phpfox::getT('language'))
				->where("language_id = '" . $oDb->escape($this->getLangId()) . "'")
				->execute('getRow');	
				
			$this->_aLanguage['image'] = (file_exists(Phpfox::getParam('core.dir_pic') . 'flag' . PHPFOX_DS . $this->_aLanguage['language_id'] . '.' . $this->_aLanguage['flag_id']) ? Phpfox::getParam('core.url_pic') . 'flag' . PHPFOX_DS . $this->_aLanguage['language_id'] . '.' . $this->_aLanguage['flag_id'] : '');			
						
			$oCache->save($sLangId, $this->_aLanguage);
            Phpfox::getLib('cache')->group('locale', $sLangId);
            Phpfox::getLib('cache')->group('language', $sLangId);
		}		
		$oCache->close($sLangId);	
		
		$sRuleId = $oCache->set(array('locale', 'language_rule_' . $this->getLangId()));

		if (($this->_aRules = $oCache->get($sRuleId)) === false)
		{
			$aRules = Phpfox_Database::instance()->select('var_name, rule, rule_value, ordering')
				->from(Phpfox::getT('language_rule'))
				->where('language_id = \'' . $this->getLangId() . '\'')
				->order('ordering ASC')
				->execute('getRows');
				
			foreach ($aRules as $aRule)
			{
				$this->_aRules[$aRule['var_name']][$aRule['ordering']] = $aRule;
			}
			if ($this->_aRules === false) {
                $this->_aRules = [];
            }
			$oCache->save($sRuleId, $this->_aRules);
            Phpfox::getLib('cache')->group('locale', $sRuleId);
            Phpfox::getLib('cache')->group('language', $sRuleId);
		}
		$oCache->close($sRuleId);
		
		(($sPlugin = Phpfox_Plugin::get('locale_contruct__end')) ? eval($sPlugin) : false);
		
		define('PHPFOX_LOCALE_LOADED', true);
	}

	/**
	 * @return $this
	 */
	public static function instance()
	{
		return Phpfox::getLib('locale');
	}
    
    /**
     * @return string
     */
	public function phrase() {
		$args = func_get_args();
		$phrase = $args[0];

		return $phrase;
	}
	
	/**
	 * Get all the information provided on the current language package being used.
	 *
	 * @return array
	 */
	public function getLang()
	{		
		$this->_aLanguage['image'] = (file_exists(Phpfox::getParam('core.dir_pic') . 'flag' . PHPFOX_DS . $this->_aLanguage['language_id'] . '.' . $this->_aLanguage['flag_id']) ? Phpfox::getParam('core.url_pic') . 'flag/' . $this->_aLanguage['language_id'] . '.' . $this->_aLanguage['flag_id'] : '');							
		
		return $this->_aLanguage;
	}
	
	/**
	 * Get all the information for a specific language package
	 *
	 * @param string $sVar Language ID to look for
	 * @return mixed ARRAY if we found the language package, empty STRING if we did'nt.
	 */
	public function getLangBy($sVar)
	{
		return (isset($this->_aLanguage[$sVar]) ? $this->_aLanguage[$sVar] : '');
	}
	
	/**
	 * Return the language ID for the current language package in use. This value is based on several
	 * variables as specific users can select a language package they want to browse the site in
	 * and admins can also select the default language package for the site.
	 *
	 * @return string Language ID for the language package in use.
	 */
    public function getLangId()
    {
        if (Phpfox::isUser()) {
            $sLanguageId = Phpfox::getUserBy('language_id');
            if (empty($sLanguageId)) {
                $sLanguageId = $this->autoLoadLanguage();
            }
        } else {
            if (($sLanguageId = Phpfox::getLib('session')->get('language_id'))) {
                
            } else {
                $sLanguageId = $this->autoLoadLanguage();
            }
        }
        
        if (!isset($this->_aLanguages[$sLanguageId])) {
            $sLanguageId = 'en';
        }
        
        return $sLanguageId;
    }

    /**
     * @return string
     */
    public function autoLoadLanguage()
    {
        if (Phpfox::getParam('core.auto_detect_language')) {
            if (isset($_SESSION['lang'])) {
                $sLanguageId = $_SESSION['lang'];
            } else {
                $sLanguageId = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
                    ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)
                    : 'en';
            }
            if (isset($this->_aLanguages[$sLanguageId])) {
                return $sLanguageId;
            }
        }

        return Phpfox::getParam('core.default_lang_id');
    }
	
	/**
     * @deprecated from 4.6.0
	 * Checks if a phrase exists in the language package or not
	 *
	 * @param string $sParam Phrase to check if it exists
	 * @return bool TRUE if it exists, FALSE if it does not
	 */
	public function isPhrase($sParam)
	{
        $sParam = Core\Lib::phrase()->correctLegacyPhrase($sParam);
        return Core\Lib::phrase()->isPhrase($sParam);
	}
	
	/**
     * @deprecated from 4.6.0
     *
     * @param string $sParam   Phrase param that is unique for that specific phrase.
     * @param array  $aParams  (Optional) ARRAY of data we need to replace in the phrase
     * @param bool   $bNoDebug (Optional) FALSE allows debug mode to be executed, while TRUE forces that there is no
     *                         debug output.
     * @param string $sDefault (Optional) If the phrase is not found you can pass a default string in its place and we will return that instead.
     * @param string $sLang    (Optional) By default we use the default language ID, however you can specify to load a phrase for a specific language package here.
     *
     * @return string Phrase value associated with the 1st argument passed.
     */
	public function getPhrase($sParam, $aParams = array(), $bNoDebug = false, $sDefault = null, $sLang = '')
	{
	    return _p($sParam, $aParams, $sLang);
	}
	
    /**
     * @deprecated 4.6.1
     *
     * @param string      $sPhraseValue
     * @param null|string $sLanguageId
     *
     * @return string
     */
	public function getPhraseHistory($sPhraseValue, $sLanguageId = null)
	{
		return _p($sPhraseValue, [], $sLanguageId);
	}	
	
	/**
     * @deprecated from 4.6.0
	 * Sets the cache ID when caching phrases for a specific page.
	 */
	public function setCache() {}
	
	/**
	 * @deprecated  from 4.6.0
	 */
	public function cache(){}
	
	/**
	 * Translates a phrase from one language to another, if the translation exists; otherwise we return the default phrase.
	 *
	 * @param string $sStr Full string of the phrase.
	 * @param mixed $sPrefix (Optional) Unique ID of a group of phrases.
	 * @return string If a phrase is found we return the translated phrase or we simply return the default phrase string.
	 */
	public function translate($sStr, $sPrefix = null)
	{
		$sPhrase = 'translate_' . ($sPrefix ? $sPrefix . '_' : '') . strtolower(preg_replace("/\W/i", "_", $sStr));

		if (Core\Lib::phrase()->isPhrase($sPhrase))
		{
			return _p($sPhrase);
		}


		// In case this is a module ID# lets change the modules to have at least the first letter uppercase
		if ($sPrefix == 'module')
		{
			$sStr = ucwords($sStr);
		}		
		
		return ($this->_sLanguagePackHelper ? '{' . $sStr . '}' : $sStr);
	}
	
	/**
	 * Converts HTML template code in phrases into actual phrases.
	 *
	 * @see self::_convert()
	 * @param string $sPhrase Phrase to convert.
	 * @return string Fully converted phrase.
	 */
	public function convert($sPhrase)
	{
		if (preg_match('/\{_p var=(.*)\}/i', $sPhrase, $aMatches))
		{
			$sPhrase = ' ' . $sPhrase . ' ';
			$sPhrase = preg_replace_callback('/\{_p var=(.*?)\}/is', array($this, '_convert'), $sPhrase);
			return trim($sPhrase);
        }
        //Support legacy data from old version
        if (preg_match('/\{phrase var=(.*)\}/i', $sPhrase, $aMatches)) {
            $sPhrase = ' ' . $sPhrase . ' ';
            $sPhrase = preg_replace_callback('/\{phrase var=(.*?)\}/is', [$this, '_convert'], $sPhrase);
            return trim($sPhrase);
        }
        
        return _p($sPhrase);
	}
    
    /**
     * Converts HTML template code in phrases into actual phrases.
     *
     * @see self::convert()
     *
     * @param array $aMatches
     *
     * @return string Fully converted phrase.
     */
	private function _convert($aMatches)
	{
		$sPhrase = trim(trim($aMatches[1], "&#039;"), "'");
        if (Core\Lib::phrase()->isPhrase($sPhrase)){
            return _p($sPhrase);
        }
        return $sPhrase;
	}
}
<?php

namespace Core;

use Phpfox;
use Phpfox_Cache;
use Phpfox_Locale;
use Phpfox_Parse_Input;
use Phpfox_Url;

class Phrase
{
    /**
     * stored all phrases of our site.
     *
     * @var array
     */
    private $_aAllPhrases = [];

    /**
     * Default language ID of current user
     *
     * @var string
     */
    private $_sDefaultLanguageId = 'en';

    /**
     * stored all phrase of Apps (included core) registered in json files.
     *
     * @var array
     */
    private static $_registeredPhrase = [];

    private $_refresh = false;

    public function __construct()
    {
        $this->_sDefaultLanguageId = Phpfox_Locale::instance()->getLangId();
        $this->init();
    }

    /**
     * @param bool $bForce
     */
    private function init($bForce = false)
    {
        $sCacheAllPhrase = Phpfox::getLib('cache')->set('language_phrase_all');
        //Save cache in 1 hour
        if ($bForce || !$aAllPhrases = Phpfox::getLib('cache')->get($sCacheAllPhrase, 60)) {
            $aLanguagePackages = Phpfox::getLib('database')->select('*')
                ->from(":language")
                ->executeRows();
            $aAllPhrases = [];
            foreach ($aLanguagePackages as $aLanguagePackage) {

                $aGetPhrases = db()->select('*')
                    ->from(':language_phrase')
                    ->where(['language_id' => $aLanguagePackage['language_id']])
                    ->executeRows();

                $aAllPhrase = [];
                $aArrayMerge = [];
                foreach ($aGetPhrases as $aPhrase) {
                    if (isset($aPhrase['module_id']) && Phpfox::isModule($aPhrase['module_id'])) {
                        $aAllPhrase[$aPhrase['module_id'] . '.' . $aPhrase['var_name']] = $aPhrase['text'];
                        $aArrayMerge[$aPhrase['var_name']] = $aPhrase['text'];
                    } else {
                        $aAllPhrase[$aPhrase['var_name']] = $aPhrase['text'];
                    }
                }
                $aAllPhrase = array_merge($aArrayMerge, $aAllPhrase);
                $aAllPhrases[$aLanguagePackage['language_id']] = $aAllPhrase;
            }
            Phpfox::getLib('cache')->save($sCacheAllPhrase, $aAllPhrases);
        }
        $this->_aAllPhrases = $aAllPhrases;
    }

    /**
     * Remove phrases cached
     */
    public function clearCache()
    {
        $this->init(true);

        if (!$this->_refresh) {
            register_shutdown_function(function () {
                Phpfox_Cache::instance()->remove();
            });
        }
        $this->_refresh = true;
    }

    /**
     * Check a Hash Name is defined.
     *
     * @param string $sHash
     *
     * @return bool
     */
    private function getRegisteredPhrase($sHash)
    {
        if (!count(self::$_registeredPhrase)) {
            $aRegisteredPhrases = [];
            $paths = array_map(function ($row) {
                return str_replace('\\', PHPFOX_DS, PHPFOX_DIR_SITE_APPS . (empty($row['apps_dir']) ? $row['apps_id'] : $row['apps_dir']) . '/phrase.json');
            }, Phpfox::getLib('database')
                ->select('*')
                ->from(':apps')
                ->where('is_active=1')
                ->execute('getSlaveRows'));

            foreach ($paths as $filePath) {
                if (file_exists($filePath)) {
                    $appPhrase = (array)json_decode(file_get_contents($filePath), true);
                    if (is_array($appPhrase)) {
                        $aRegisteredPhrases = array_merge($aRegisteredPhrases, $appPhrase);
                    }
                }
            }

            $sThemePath = PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS;
            foreach (scandir($sThemePath) as $path) {
                $sJsonFile = $sThemePath . $path . PHPFOX_DS . 'phrase.json';
                if ($path == '.' || $path == '..' || !file_exists($sJsonFile)) {
                    continue;
                }

                $themePhrases = (array)json_decode(file_get_contents($sJsonFile), true);
                if (is_array($themePhrases)) {
                    $aRegisteredPhrases = array_merge($aRegisteredPhrases, $themePhrases);
                }
            }

            foreach ($aRegisteredPhrases as $sKey => $aValue) {
                if (is_array($aValue)) {
                    if (isset($aValue['en']) && !empty($aValue['en'])) {
                        $aRegisteredPhrases[$sKey] = $aValue;
                    } else {
                        $sNewKey = 'app_' . md5($sKey);
                        $aValue['en'] = $sKey;
                        $aRegisteredPhrases[$sNewKey] = $aValue;
                    }
                } else {
                    if (empty($aValue)) {
                        $sNewKey = 'app_' . md5($sKey);
                        $aRegisteredPhrases[$sNewKey] = $sKey;
                        unset($aRegisteredPhrases[$sKey]);
                    } else {
                        $aRegisteredPhrases[$sKey] = $aValue;
                    }
                }
            }
            self::$_registeredPhrase = $aRegisteredPhrases;
        }
        return (isset(self::$_registeredPhrase[$sHash])) ? $this->_siteNameParse(self::$_registeredPhrase[$sHash]) : false;
    }

    /**
     * @param string $sVarName
     * @param array  $aParam
     * @param string $sLanguageId
     *
     * @return string
     */
    public function get($sVarName = '', $aParam = [], $sLanguageId = '')
    {
        if (empty($sLanguageId)) {
            $sLanguageId = $this->_sDefaultLanguageId;
        }

        if (isset($aParam['user'])) {

            if (!is_array($aParam['user'])) {
                error('The key "user" needs to be an array of the users details.');
            }

            $sUserPrefix = (isset($aParam['user_prefix']) ? $aParam['user_prefix'] : '');

            $aUser = $aParam['user'];
            $aUser['user_link'] = '<a href="' . Phpfox_Url::instance()
                    ->makeUrl($aUser[$sUserPrefix . 'user_name']) . '">' . Phpfox::getLib('parse.output')
                    ->clean($aUser[$sUserPrefix . 'full_name']) . '</a>';
            unset($aParam['user']);
            $aParam = array_merge($aParam, $aUser);
        }

        //Support legacy phrase. End support from 4.7.0
        if (!$this->isPhrase($sVarName, false)) {
            $sVarName = $this->correctLegacyPhrase($sVarName);
        }
        //End support legacy

        if ($this->isPhrase($sVarName)) {
            return $this->processPhrase($sVarName, $aParam, $sLanguageId);
        } else {
            $hash = 'app_' . md5($sVarName);
        }

        if ($this->isPhrase($hash)) {
            return $this->processPhrase($hash, $aParam, $sLanguageId);
        } else {
            //New phrase or phrase not exist.
            if (!defined('PHPFOX_IS_TECHIE') || !PHPFOX_IS_TECHIE) {
                //Return var_name in case can't phrase is not define
                return $sVarName;
            } else {
                // Support developer only
                $textPhrase = $this->getRegisteredPhrase($sVarName);
                if ($textPhrase) {
                    $hash = $sVarName;
                } else {
                    $textPhrase = $this->getRegisteredPhrase($hash);
                }
                if ($textPhrase) {
                    //Support multiply define language in json
                    if (is_array($textPhrase)) {
                        if (isset($textPhrase[$sLanguageId])) {
                            $textPhrase = $textPhrase[$sLanguageId];
                        } else {
                            $textPhrase = $textPhrase['en'];
                        }
                    }

                    //Check phrase exist before insert
                    $iCnt = db()->select('COUNT(*)')
                        ->from(':language_phrase')
                        ->where(['language_id' => $sLanguageId, 'var_name' => $hash])
                        ->execute('getSlaveField');
                    if ($iCnt == 0) {
                        db()->insert(':language_phrase', [
                            'language_id'  => $sLanguageId,
                            'var_name'     => $hash,
                            'text'         => Phpfox_Parse_Input::instance()->clean($textPhrase),
                            'text_default' => Phpfox_Parse_Input::instance()->clean($textPhrase),
                            'added'        => moment()->now(),
                        ]);
                    }
                    Phpfox_Cache::instance()->remove('language_phrase_all');
                    $this->init();
                    if (!isset($this->_aAllPhrases[$sLanguageId][$hash]) && defined('PHPFOX_DEBUG_PHRASE') && PHPFOX_DEBUG_PHRASE) {
                        return error('Unable to load phrase: ' . $sVarName);
                    }
                    return $this->processPhrase($hash, $aParam, $sLanguageId);
                } elseif (defined('PHPFOX_DEBUG_PHRASE') && PHPFOX_DEBUG_PHRASE) {
                    return error('Unable to load phrase: ' . $sVarName);
                }
                //Not a phrase
                return $sVarName;
            }
            //End support developer
        }
    }

    private function processPhrase($hash, $aParam, $sLanguageId)
    {
        $sVarName = isset($this->_aAllPhrases[$sLanguageId][$hash]) ? $this->_aAllPhrases[$sLanguageId][$hash] : $hash;
        //process phrase before return
        if (count($aParam)) {
            $aFind = [];
            $aReplace = [];
            foreach ($aParam as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $sVarName = str_replace('{{ ' . $key . ' }}', $value, $sVarName);
                $aFind[] = '{' . $key . '}';
                $aReplace[] = '' . $value . '';
            }
            if (count($aFind)) {
                $sVarName = str_replace($aFind, $aReplace, $sVarName);
            }
        }

        if (Phpfox::getParam('language.lang_pack_helper')) {
            $sVarName = '{' . $sVarName . '}';
        }
        return htmlspecialchars_decode($sVarName);
    }

    /**
     * find all phrases defined in phrase.json file
     *
     * @return array
     */
    public function findDefinedPhrasesFromJSon()
    {
        $aAllPhrases = [];
        $aApps = Lib::app()->all();

        // Get all defined phrases
        foreach ($aApps as $aApp) {
            $filePath = $aApp->path . 'phrase.json';
            if (file_exists($filePath)) {
                $appPhrase = json_decode(file_get_contents($aApp->path . 'phrase.json'), true);
                if (is_array($appPhrase)) {
                    $aAllPhrases = array_merge($aAllPhrases, $appPhrase);
                }
            }
        }

        //Get all defined phrases from modules
        $aDirs = scandir(PHPFOX_DIR . "module");
        foreach ($aDirs as $sDir) {
            $jsonFile = PHPFOX_DIR . "module" . PHPFOX_DS . $sDir . PHPFOX_DS . 'phrase.json';
            if (file_exists($jsonFile)) {
                $aAllPhrases = array_merge($aAllPhrases, json_decode(file_get_contents($jsonFile), true));
            }
        }

        // Get all defined phrases from themes
        $aDirs = scandir(PHPFOX_DIR_SITE . "flavors");
        foreach ($aDirs as $sDir) {
            $jsonFile = PHPFOX_DIR_SITE . "flavors" . PHPFOX_DS . $sDir . PHPFOX_DS . 'phrase.json';
            if (file_exists($jsonFile)) {
                $aAllPhrases = array_merge($aAllPhrases, json_decode(file_get_contents($jsonFile), true));
            }
        }

        return $aAllPhrases;
    }

    /**
     * find all phrase (3rd party + modules, app ) then add to database.
     *
     * @param string $sLanguageId
     */
    public function findMissingPhrases($sLanguageId = 'en')
    {
        $aAllPhrases = $this->findDefinedPhrasesFromJSon();
        $sDefaultLanguage = Phpfox::getService('language')->getDefaultLanguage();

        $aDefaultPhrases = db()->select('var_name, text')
            ->from(':language_phrase')
            ->where(['language_id' => $sDefaultLanguage])
            ->execute('getSlaveRows');

        foreach ($aAllPhrases as $sKey => $aValue) {
            if (is_array($aValue)) {
                if (isset($aValue[$sLanguageId]) && !empty($aValue[$sLanguageId])) {
                    $aAllPhrases[$sKey] = $aValue[$sLanguageId];
                } else {
                    if (isset($aValue['en']) && !empty($aValue['en'])) {
                        $aAllPhrases[$sKey] = $aValue['en'];
                    } else {
                        $sNewKey = 'app_' . md5($sKey);
                        $aAllPhrases[$sNewKey] = $sKey;
                    }
                }
            } else {
                if (empty($aValue)) {
                    $sNewKey = 'app_' . md5($sKey);
                    $aAllPhrases[$sNewKey] = $sKey;
                    unset($aAllPhrases[$sKey]);
                } else {
                    $aAllPhrases[$sKey] = $aValue;
                }
            }
        }

        foreach ($aDefaultPhrases as $row) {
            $sVarName = $row['var_name'];
            if (!isset($aAllPhrases[$sVarName])) {
                $aAllPhrases[$sVarName] = $row['text'];
            }
        }

        $aExistsPhrase = db()->select('var_name')
            ->from(':language_phrase')
            ->where(['language_id' => $sLanguageId])
            ->execute('getSlaveRows');

        foreach ($aExistsPhrase as $row) {
            unset($aAllPhrases[$row['var_name']]);
        }

        foreach ($aAllPhrases as $sKey => $sText) {
            db()->insert(':language_phrase', [
                'language_id'  => $sLanguageId,
                'var_name'     => $sKey,
                'text'         => $sText,
                'text_default' => $sText,
                'added'        => PHPFOX_TIME,
            ]);
        }

        $this->clearCache();
        //init new phrase cache
        $this->init();
    }

    /**
     * Check is a var_name is phrase or not
     *
     * @param string $sVarName
     * @param bool   $bCorrectLegacy
     *
     * @return bool
     */
    public function isPhrase($sVarName, $bCorrectLegacy = true)
    {
        if ($bCorrectLegacy) {
            $sVarName = $this->correctLegacyPhrase($sVarName);
        }

        //Re-init if default language id didn't cache.
        if (!isset($this->_aAllPhrases[$this->_sDefaultLanguageId])) {
            $this->init();
        }
        return (isset($this->_aAllPhrases[$this->_sDefaultLanguageId][$sVarName])) ? true : false;
    }

    /**
     * Add new phrase, if first param is array => add multiple phrases, if it string, it is var_name => add a single phrase
     *
     * @param string|array $sVarName
     * @param string|array $sValue
     * @param bool         $bShouldClearCache
     *
     * @return bool
     */
    public function addPhrase($sVarName, $sValue = '', $bShouldClearCache = true)
    {
        if (is_array($sVarName)) {
            $aAllPhrases = $sVarName;
        } elseif (!empty($sValue)) {
            $aAllPhrases = [
                $sVarName => $sValue,
            ];
        } else {
            return false;
        }

        foreach ($aAllPhrases as $sKey => $aValue) {
            if (is_array($aValue)) {
                if (isset($aValue['en']) && !empty($aValue['en'])) {
                    $aAllPhrases[$sKey] = $aValue;
                } else {
                    $sNewKey = 'app_' . md5($sKey);
                    $aValue['en'] = $sKey;
                    $aAllPhrases[$sNewKey] = $aValue;
                }
            } else {
                if (empty($aValue)) {
                    $sNewKey = 'app_' . md5($sKey);
                    $aAllPhrases[$sNewKey] = ['en' => $sKey];
                    unset($aAllPhrases[$sKey]);
                } else {
                    $aAllPhrases[$sKey] = ['en' => $aValue];
                }
            }
        }

        $aLanguages = db()->select('*')
            ->from(':language')
            ->executeRows();

        foreach ($aLanguages as $aLanguage) {
            $aShouldInsertPhrasePool = [];
            $aShouldDeletePhrasePool = [];

            //Get all phrases from each language package
            $aGetPhrases = db()->select('*')
                ->from(':language_phrase')
                ->where(['language_id' => $aLanguage['language_id']])
                ->executeRows();
            $aCheckPhrases = [];
            foreach ($aGetPhrases as $aGetPhrase) {
                if (isset($aGetPhrase['module_id']) && Phpfox::isModule($aGetPhrase['module_id'])) {
                    //Do not remove duplicate phrase from module, it still uses module_id
                    $aCheckPhrases[$aGetPhrase['var_name']] = $aGetPhrase;
                    continue;
                }
                if (isset($aCheckPhrases[$aGetPhrase['var_name']])) {
                    //Remove duplicate phrase
                    $aShouldDeletePhrasePool[] = (int)$aGetPhrase['phrase_id'];
                } else {
                    $aCheckPhrases[$aGetPhrase['var_name']] = $aGetPhrase;
                }
            }

            // todo Neil slice $aShouldDeletePhrasePool to 0..500, then delete using IN (?) OPERATOR.
            // @since 4.6.0
            while (($aValues = array_splice($aShouldDeletePhrasePool, 0, 100)) && count($aValues) > 0) {
                db()->delete(':language_phrase', ['phrase_id' => (int)$aGetPhrase['phrase_id']]);

                $idList = implode(', ', array_map(function ($v) {
                    return intval($v);
                }, $aValues));

                db()->delete(':language_phrase', 'phrase_id IN (' . $idList . ')');
            }

            foreach ($aAllPhrases as $sKey => $aPhrase) {
                $sNewText = (isset($aPhrase[$aLanguage['language_id']])) ? $aPhrase[$aLanguage['language_id']] : $aPhrase['en'];
                $sNewText = $this->_siteNameParse($sNewText);
                if (isset($aCheckPhrases[$sKey])) {//Old phrase Exist
                    //If language_id = en, and the phrase is not change => update to new phrase
                    if (($aLanguage['language_id'] == 'en') and ($sNewText != $aCheckPhrases[$sKey]['text']) and ($aCheckPhrases[$sKey]['text'] == $aCheckPhrases[$sKey]['text_default'])) {
                        db()->update(':language_phrase', [
                            'text_default' => $sNewText,
                            'text'         => $sNewText,
                        ], [
                            'language_id' => $aLanguage['language_id'],
                            'var_name'    => $sKey,
                        ]);
                    }
                } else {//This is new phrase

                    $aShouldInsertPhrasePool [] = [
                        $aLanguage['language_id'],
                        $sKey,
                        $sNewText,
                        $sNewText,
                        PHPFOX_TIME,
                    ];
                }
            }

            // todo Neil batch insert language_phrase
            // @since 4.6.0
            while (($aValues = array_splice($aShouldInsertPhrasePool, 0, 200)) && count($aValues) > 0) {
                db()->multiInsert(Phpfox::getT('language_phrase'), [
                    'language_id',
                    'var_name',
                    'text',
                    'text_default',
                    'added',
                ], $aValues);
            };

        }

        if ($bShouldClearCache) {
            $this->clearCache();
        }
        return true;
    }

    public function clonePhrase($sOldPhrase, $sNewPhrase)
    {
        if ($sOldPhrase == $sNewPhrase) {
            return true;
        }

        $aValues = Phpfox::getService('language.phrase')->getValues($sOldPhrase);
        if (empty($aValues) || empty($aValues[$sOldPhrase])) {
            return false;
        }
        $aValues = $aValues[$sOldPhrase];

        return $this->addPhrase($sNewPhrase, $aValues);
    }

    /**
     * Correct legacy phrase.
     * Example core.are_you_sure become  are_you_sure
     * are_you_sure become are_you_sure
     *
     * @param string $sPhrase
     *
     * @return string mixed
     */
    public function correctLegacyPhrase($sPhrase)
    {
        //Check var_name is a text
        if (strpos($sPhrase, ' ') !== false) {
            return $sPhrase;
        }

        $aParts = explode('.', $sPhrase);

        //Not a valid legacy phrase
        if (isset($aParts[2])) {
            return $sPhrase;
        }

        if (isset($aParts[1]) && !empty($aParts[1]) && Phpfox::isModule($aParts[0])) {
            return $aParts[1];
        } else {
            return $sPhrase;
        }
    }

    /**
     * Change Site name to name of user
     * @param string $sValue
     *
     * @return string
     */
    private function _siteNameParse($sValue)
    {
        $sNewName = Phpfox::getParam('core.site_title');
        if (strtolower($sNewName) == 'site name') {
            return $sValue;
        }
        //Replace all lower
        $sValue = str_replace('site name', strtolower($sNewName), $sValue);
        $sValue = str_replace('sitename', strtolower($sNewName), $sValue);

        //Replace all upper
        $sValue = str_replace('SITE NAME', strtoupper($sNewName), $sValue);
        $sValue = str_replace('SITENAME', strtoupper($sNewName), $sValue);

        //Replace upper first
        $sValue = str_replace("Site name", ucfirst(strtolower($sNewName)), $sValue);
        $sValue = str_replace('Sitename', ucfirst(strtolower($sNewName)), $sValue);

        //Replace upper first letter
        $sValue = str_replace('Site Name', ucwords(strtolower($sNewName)), $sValue);
        $sValue = str_replace('SiteName', ucwords(strtolower($sNewName)), $sValue);

        //other case
        $sValue = str_replace(strtolower('SiteName'), strtolower($sNewName), $sValue);
        return $sValue;
    }
}
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
 * @version 		$Id: phrase.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Language_Service_Phrase_Phrase extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('language_phrase');
	}
    
    /**
     * Check a var_name is already phrase
     *
     * @param array|string $sString
     *
     * @return false|string
     */
	public function isPhrase($sString)
	{
	    if (is_array($sString) && isset($sString['var_name'])){
	        $sString = $sString['var_name'];
        }
        
		$sPhrase = Phpfox::getService('language.phrase.process')->prepare($sString);
		
		$aRow = $this->database()->select('lp.var_name')
			->from($this->_sTable, 'lp')
			->where("lp.var_name = '" . $this->database()->escape($sPhrase) . "'")
			->execute('getSlaveRow');
        
        if (!isset($aRow['var_name'])) {
            return false;
        }
		
		return $sPhrase;
	}
    
    /**
     * Check a var_name is value.
     *
     * @param string $sPhrase
     * @param null   $sLanguageId
     *
     * @return bool
     */
	public function isValid($sPhrase, $sLanguageId = null)
	{
	    //Support legacy phrase. End support from 4.7.0
        $sPhrase = Core\Lib::phrase()->correctLegacyPhrase($sPhrase);
        //End support legacy
		
		$iCnt =  $this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where(($sLanguageId === null ? '' : 'language_id = \'' . $this->database()->escape($sLanguageId) . '\' AND ') . ' var_name = \'' . $this->database()->escape($sPhrase) . '\'')
			->execute('getSlaveField');
        
        return ($iCnt ? true : false);
	}
    
    /**
     * Get phrases by conditions
     *
     * @param array         $aConds
     * @param string        $sSort
     * @param string|string $iPage
     * @param string        $sLimit
     * @param bool          $bCount
     *
     * @return array|int|string
     */
	public function get($aConds = [], $sSort = 'lp.phrase_id DESC', $iPage = '', $sLimit = '', $bCount = true)
	{		
		$iCnt = ($bCount ? 0 : 1);
		$aRows = array();
        
        if ($bCount) {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from($this->_sTable, 'lp')
                ->join(Phpfox::getT('language'), 'l', 'l.language_id = lp.language_id')
                ->where($aConds)
                ->execute('getSlaveField');
        }

		if ($iCnt) {
			$aRows = $this->database()->select('lp.*, l.title')
				->from($this->_sTable, 'lp')
				->join(Phpfox::getT('language'), 'l', 'l.language_id = lp.language_id')
				->where($aConds)
				->order($sSort)
				->limit($iPage, $sLimit, $iCnt)
				->execute('getSlaveRows');
		}
        
        if (!$bCount) {
            return $aRows;
        }
		
		return array($iCnt, $aRows);
	}
    
    /**
     * @param array  $aConds
     * @param string $sSort
     *
     * @return array
     */
	public function getSearch($aConds = array(), $sSort = 'lp.phrase_id DESC')
	{
		$aRows = $this->database()->select('lp.phrase_id')
			->from($this->_sTable, 'lp')
			->where($aConds)
			->order($sSort)
			->execute('getSlaveRows', array(
				'free_result' => true
			));
        
        $aIds = [];
        foreach ($aRows as $aRow) {
            $aIds[] = $aRow['phrase_id'];
        }
        
        unset($aRows);
		
		return $aIds;
	}
    
    /**
     * @param $sVarName
     *
     * @return array
     */
	public function getValues($sVarName)
	{
        //Support legacy phrase. End support from 4.7.0
        $sVarName = Core\Lib::phrase()->correctLegacyPhrase($sVarName);
        //End support legacy
		
		$aPhrases = $this->database()->select('language_id, text')
			->from(Phpfox::getT('language_phrase'))
			->where('var_name = "' . $this->database()->escape($sVarName) . '"')
			->execute('getSlaveRows');
        
        $aGroup = [];
        foreach ($aPhrases as $aPhrase) {
            $aGroup[$sVarName][$aPhrase['language_id']] = $aPhrase['text'];
        }
        
        return $aGroup;
	}
    
    /**
     * @param $sPhrase
     *
     * @return bool
     */
	public function getStaticPhrase($sPhrase)
	{
        //Support legacy phrase. End support from 4.7.0
        $sPhrase = Core\Lib::phrase()->correctLegacyPhrase($sPhrase);
        //End support legacy
		
		$aRow = $this->database()->select('phrase_id, text')
			->from(Phpfox::getT('language_phrase'))
			->where('var_name = "' . $this->database()->escape($sPhrase) . '"')
			->execute('getSlaveRow');
        
        if (!isset($aRow['phrase_id'])) {
            return false;
        }
		
		return $aRow['text'];
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
		if ($sPlugin = Phpfox_Plugin::get('language.service_phrase_phrase__call'))
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
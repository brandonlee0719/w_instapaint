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
class Report_Service_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('report');		
	}
	
	public function update($iId, $aVals)
	{
        if (empty($aVals['module_id']))
        {
            $aVals['module_id'] = 'core';
        }
        $aLanguages = Phpfox::getService('language')->getAll();
        if (Core\Lib::phrase()->isPhrase($aVals['name_ori'])){
            $finalPhrase = $aVals['name_ori'];
            //Update phrase
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name'][$aLanguage['language_id']])){
                    $name = $aVals['name'][$aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $finalPhrase, $name);
                }
                else {
                    Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }
            }
        } else {
            $name = $aVals['name'][$aLanguages[0]['language_id']];
            $phrase_var_name = 'report_category_' . md5('Report Category'. $name . PHPFOX_TIME);
            //Add phrase
            $aText = [];
            foreach ($aLanguages as $aLanguage){
                if (!empty($aVals['name'][$aLanguage['language_id']])){
                    $aText[$aLanguage['language_id']] = $aVals['name'][$aLanguage['language_id']];
                } else {
                    Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];
            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
            $this->database()->update($this->_sTable, array(
                    'module_id' => $aVals['module_id'],
                    'product_id' => $aVals['product_id'],
                    'message' => $finalPhrase
                ), 'report_id = ' . (int) $iId
            );
        }
		$this->cache()->remove('report');
        return $finalPhrase;
	}
	
	public function add($aVals)
	{
        if (empty($aVals['module_id']))
        {
            $aVals['module_id'] = 'core';
        }
        //Add phrase for category
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name'][$aLanguages[0]['language_id']];
        $phrase_var_name = 'report_category_' . md5('Report Category'. $name . PHPFOX_TIME);
        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage){
            if (!empty($aVals['name'][$aLanguage['language_id']])){
                $aText[$aLanguage['language_id']] = $aVals['name'][$aLanguage['language_id']];
            } else {
                Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
            }
        }
        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];
        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
		$iId = $this->database()->insert($this->_sTable, array(
				'module_id' => $aVals['module_id'],
				'product_id' => $aVals['product_id'],
				'message' => $finalPhrase
			)
		);
		$this->cache()->remove('report');
		
		return $iId;
	}
	
	public function delete($iId)
	{
        $aCategory = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('report_id=' . (int) $iId)
            ->execute('getSlaveRow');
        if (isset($aCategory['message']) && Phpfox::isPhrase($aCategory['message'])){
            Phpfox::getService('language.phrase.process')->delete($aCategory['message'], true);
        }
		$this->database()->delete($this->_sTable, 'report_id = ' . (int) $iId);
		$this->cache()->remove('report');
		
		return true;
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
		if ($sPlugin = Phpfox_Plugin::get('report.service_process__call'))
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
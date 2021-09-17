<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: process.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Contact_Service_Process extends Phpfox_Service
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('contact_category');
	}

	/**
     * Adds a category to phpfox_contact_category
     * The order is not specified so it defaults to 0 as per DB design
     *
     * @param array $aVals
     *
     * @return bool
     */
    public function addCategory($aVals)
    {
        // check for plugins
        (($sPlugin = Phpfox_Plugin::get('contact.service_process_add_start')) ? eval($sPlugin) : false);

        //Add phrase for category
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_' . $aLanguages[0]['language_id']];
        $phrase_var_name = 'contact_category_' . md5('Contact Category' . $name . PHPFOX_TIME);
        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            } else {
                return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.',
                    ['language_name' => $aLanguage['title']])));
            }
        }
        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text'     => $aText
        ];
        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        $iId = $this->database()->insert(Phpfox::getT('contact_category'), array(
                'title' => $finalPhrase,
            )
        );
        Core\Lib::phrase()->clearCache();
        $this->renewCache();

        // check for plugins
        (($sPlugin = Phpfox_Plugin::get('contact.service_process_add_end')) ? eval($sPlugin) : false);
        return $iId;
    }

    /**
     * Update category
     * @param $aVals , array
     * @param $iId , integer
     * @return bool
     */
    public function updateCategory($aVals, $iId)
    {
        $aCategory = Phpfox::getService('contact')->getCategoryById($iId);
        // update pharse if old category title is PHRASE
        if (Core\Lib::phrase()->isPhrase($aCategory['title'])) {
            $aPhrases = Phpfox::getService('language.phrase')->get([
                'var_name' => $aCategory['title']
            ], 'lp.phrase_id DESC', '', '', false);
            foreach ($aPhrases as $aPhrase) {
                if (array_key_exists('name_'.$aPhrase['language_id'], $aVals)) {
                    Phpfox::getService('language.phrase.process')->update($aPhrase['phrase_id'], $aVals['name_'.$aPhrase['language_id']]);
                }
            }
        } else {
            // update phrase if old category title is NOT PHRASE
            $aLanguages = Phpfox::getService('language')->getAll();
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'contact_category_' . md5('Contact Category' . $name . PHPFOX_TIME);
            //Add phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.',
                        ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text'     => $aText
            ];
            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
            $this->database()->update(Phpfox::getT('contact_category'), array('title' => $finalPhrase,),
                array('category_id' => $iId));
            $this->renewCache();
        }
        Core\Lib::phrase()->clearCache();

        return true;
    }

    /**
     * Deletes one or more category entries from the DB, also renews cache
     *
     * @param array $aIds only integers that correspond to their category_id in this->_sTable
     *
     * @return true
     */
	public function deleteMultiple($aIds)
	{
        foreach ($aIds as $iId) {
            $aCategory = $this->database()->select('*')
                ->from(':contact_category')
                ->where('category_id=' . (int) $iId)
                ->execute('getSlaveRow');
            if (isset($aCategory['title']) && Core\Lib::phrase()->isPhrase($aCategory['title'])){
                Phpfox::getService('language.phrase.process')->delete($aCategory['title'], true);
            }
            $this->database()->delete($this->_sTable, 'category_id = ' . (int)$iId);
        }
        $this->renewCache();
        return true;
	}

    /**
     * @param array $aCategories
     *
     * @return bool
     */
	public function updateMultiple($aCategories)
	{
        $bResult = true;
        foreach ($aCategories as $aCategory) {
            if (empty($aCategory['title'])) {
                return false;
            }

        }

        foreach ($aCategories as $aCategory) {
            $bUpdate = $this->database()->update($this->_sTable, [
                'title'    => $aCategory['title'],
                'ordering' => ((int)$aCategory['ordering'] > 0) ? (int)$aCategory['ordering'] : 0
            ], 'category_id = ' . (int)$aCategory['category_id']);
            $bResult = $bResult && $bUpdate;
            
        }
        // renew cache even if the update failed, it may have updated a few only
		$this->renewCache();
		return $bResult;
	}
    
    /**
     * @param array $aVal
     */
	public function updateOrdering($aVal)
	{
        foreach ($aVal as $iId => $iPosition) {
            $this->database()
                ->update(Phpfox::getT('contact_category'), ['ordering' => (int)$iPosition], 'category_id = ' . (int)$iId);
        }
        $this->renewCache();
    }
    
    /**
     * Clear cache of contact
     */
    public function renewCache()
	{
		// clean the cache
		$this->cache()->remove('contact_category');
		// reset the cache
		$sCacheId = $this->cache()->set('contact_category','contact');
		$this->cache()->save($sCacheId, Phpfox::getService('contact')->getCategories());
        Phpfox::getLib('cache')->group('contact', $sCacheId);
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
        if ($sPlugin = Phpfox_Plugin::get('contact.service_process__call')) {
            eval($sPlugin);
            return null;
        }

		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}
}
<?php
defined('PHPFOX') or exit('NO DICE!');

abstract class Core_Service_Systems_Category_Process extends Phpfox_Service
{
    /**
     * @var string is category table name
     */
    protected $_sTable;
    
    /**
     * @var string is module name
     */
    protected $_sModule = 'core';
    
    /**
     * @var array store available language on this site
     */
    protected $_aLanguages;
    
    /**
     * @var string category data of module
     */
    protected $_sTableData;
    
    /**
     * @var string name of category (some module don't use category)
     */
    protected $_sCategoryName = 'category';

    /**
     * @var string name of item id column
     */
    protected $_sItemId;
    
    /**
     * Core_Service_Systems_Category_Process constructor.
     */
    public function __construct() {
        $this->_aLanguages = Phpfox::getService('language')->getAll();
    }

    /**
     * @param string $sTable
     */
    public function setTable($sTable)
    {
        $this->_sTable = $sTable;
    }

    /**
     * @param string $sModule
     */
    public function setModule($sModule)
    {
        $this->_sModule = $sModule;
    }

    /**
     * @param string $sCategoryName
     */
    public function setCategoryName($sCategoryName)
    {
        $this->_sCategoryName = $sCategoryName;
    }

    /**
     * @param string $sTableData
     */
    public function setTableData($sTableData)
    {
        $this->_sTableData = $sTableData;
    }
    
    /**
     * Add a new phrase for category
     *
     * @param array  $aVals
     * @param string $sName
     * @param bool   $bVerify
     *
     * @return null|string
     */
    protected function addPhrase($aVals, $sName = 'name', $bVerify = true)
    {
        $aFirstLang = end($this->_aLanguages);
        //Add phrases
        $aText = [];
        //Verify name

        foreach ($this->_aLanguages as $aLanguage){
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']]) && !empty($aVals[$sName . '_' . $aLanguage['language_id']])){
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
            } elseif ($bVerify){
                return Phpfox_Error::set((_p('Provide a "{{ language_name }}" ' . $sName . '.', ['language_name' => $aLanguage['title']])));
            } else {
                $bReturnNull = true;
            }
        }
        if (isset($bReturnNull) && $bReturnNull){
            //If we don't verify value, phrase can't be empty. Return null for this case.
            return null;
        }
        $name = $aVals[$sName . '_' . $aFirstLang['language_id']];
        $phrase_var_name = $this->_sModule . '_' . $this->_sCategoryName . '_' . md5($this->_sModule . $this->_sCategoryName . $name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        return $finalPhrase;
    }
    
    /**
     * Update phrase when edit a category
     *
     * @param array  $aVals
     * @param string $sName
     */
    protected function updatePhrase($aVals, $sName = 'name')
    {
        foreach ($this->_aLanguages as $aLanguage){
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']])){
                $name = $aVals[$sName . '_' . $aLanguage['language_id']];
                Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals[$sName], $name);
            }
        }
    }
    
    /**
     * Add a new category for module
     *
     * @param array  $aVals
     * @param string $sName
     *
     * @return int
     */
    public function add($aVals, $sName = 'name') {
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $iCategoryId = $this->database()->insert($this->_sTable, [
            'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
            'is_active' => 1,
            'name' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME
        ]);

        $this->cache()->removeGroup($this->_sModule . '_category');
        return $iCategoryId;
    }

    /**
     * @param array $aVals
     * @param string $sName
     *
     * @return bool
     */
    public function update($aVals, $sName = 'name')
    {
        //Verify data
        if (!isset($aVals['edit_id'])) {
            return false;
        }

        // update parent category
        if (!empty($this->_sItemId) && !empty($this->_sTableData)) {
            $iOldParentId = db()->select('parent_id')->from($this->_sTable)->where(['category_id' => $aVals['edit_id']])->executeField();
            $aItemsInSameCategory = db()->select($this->_sItemId)->from($this->_sTableData)->where(['category_id' => $aVals['edit_id']])->executeRows();
            foreach (array_column($aItemsInSameCategory, $this->_sItemId) as $iItemId) {
                if (empty($iOldParentId) && !empty($aVals['parent_id'])) {
                    db()->insert($this->_sTableData, [
                        $this->_sItemId => $iItemId,
                        'category_id' => $aVals['parent_id']
                    ]);
                } elseif (!empty($iOldParentId) && empty($aVals['parent_id'])) {
                    db()->delete($this->_sTableData, [
                        $this->_sItemId => $iItemId,
                        'category_id' => $iOldParentId
                    ]);
                } elseif (!empty($iOldParentId) && !empty($aVals['parent_id'])) {
                    db()->update($this->_sTableData, [
                        $this->_sItemId => $iItemId,
                        'category_id' => $aVals['parent_id']
                    ], [
                        $this->_sItemId => $iItemId,
                        'category_id' => $iOldParentId
                    ]);
                }
            }
        }

        if (isset($aVals[$sName]) && Core\Lib::phrase()->isPhrase($aVals[$sName])) {
            $finalPhrase = $aVals[$sName];
            //Update phrase
            $this->updatePhrase($aVals);
        } else {
            $finalPhrase = $this->addPhrase($aVals, $sName);
        }
        $this->database()->update($this->_sTable, [
            'parent_id' => (int)$aVals['parent_id'],
            'name' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME
        ], 'category_id = ' . $aVals['edit_id']);

        // Remove from cache
        $this->cache()->removeGroup($this->_sModule . '_category');

        return true;
    }
    
    /**
     * Active or de-active a category. This function in adminCP only
     *
     * @param int $iCategoryId
     * @param int $iActive
     */
    public function toggleActiveCategory($iCategoryId, $iActive)
    {
        Phpfox::isUser(true);
        Phpfox::isAdmin(true);

        $iActive = (int) $iActive;
        $this->database()->update($this->_sTable, [
            'is_active' =>  ($iActive == 1 ? 1 : 0)
            ], 'category_id= ' . (int) $iCategoryId);

        $this->cache()->removeGroup($this->_sModule . '_category');
    }
    
    /**
     * Delete a category and sub category (if have)
     *
     * @param int $iId
     *
     * @return bool
     */
    public function delete($iId)
    {
        $sCategoryName = $this->database()->select('name')
            ->from($this->_sTable)
            ->where('category_id = ' . (int) $iId)
            ->execute('getSlaveField');
        if (Phpfox::isPhrase($sCategoryName)){
            Phpfox::getService('language.phrase.process')->delete($sCategoryName, true);
        }
        $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
        $this->database()->delete($this->_sTableData, 'category_id = ' . (int) $iId);

        $aCategoryParents = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id=' . (int) $iId)
            ->execute('getSlaveRows');
        foreach ($aCategoryParents as $aCategoryParent) {
            if (Phpfox::isPhrase($aCategoryParent['name'])){
                Phpfox::getService('language.phrase.process')->delete($aCategoryParent['name'], true);
            }
            $this->database()->delete($this->_sTable, 'category_id = ' . (int) $aCategoryParent['category_id']);
            $this->database()->delete($this->_sTableData, 'category_id = ' . (int) $aCategoryParent['category_id']);
        }
        $this->cache()->removeGroup($this->_sModule . '_category');
        return true;
    }
    
    /**
     * Update category ordering
     *
     * @param array $aOrders
     *
     * @return bool
     */
    public function updateOrder($aOrders)
    {
        foreach ($aOrders as $iCategoryId => $iOrder) {
            $this->database()->update($this->_sTable, ['ordering' => $iOrder], 'category_id = ' . (int) $iCategoryId);
        }
        
        // Remove from cache
        $this->cache()->removeGroup($this->_sModule . '_category');
        
        return true;
    }
}
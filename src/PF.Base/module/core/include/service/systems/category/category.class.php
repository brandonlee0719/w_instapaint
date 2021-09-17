<?php
defined('PHPFOX') or exit('NO DICE!');

class Core_Service_Systems_Category_Category extends Phpfox_Service
{
    /**
     * @var string is Table name
     */
    protected $_sTable;
    
    /**
     * @var string Category data
     */
    protected $_sTableData;
    
    /**
     * @var string is module name
     */
    protected $_sModule = 'core';
    
    /**
     * Core_Service_Systems_Category_Category constructor.
     */
    public function __construct() { }

    /**
     * Get one category with all information
     *
     * @param int $iId
     *
     * @return bool|array
     */
    public function getForEdit($iId)
    {
        if ($iId == 0){
            return false;
        }
        $sCacheId = $this->cache()->set($this->_sModule . '_category_edit_' . (int)$iId);

        if (!$aCategory = $this->cache()->get($sCacheId)) {
            $aCategory = $this->database()->select('*')->from($this->_sTable)->where('category_id = ' . (int)$iId)->execute('getSlaveRow');
            $aLanguages = Phpfox::getService('language')->getAll();
            foreach ($aLanguages as $aLanguage) {
                $aCategory['name_' . $aLanguage['language_id']] = Phpfox::getSoftPhrase($aCategory['name'], [], false, null, $aLanguage['language_id']);
            }
            $this->cache()->save($sCacheId, $aCategory);
            Phpfox::getLib('cache')->group( $this->_sModule . '_category', $sCacheId);
        }
        return $aCategory;
    }
    
    /**
     * Get all parent categories (included not active)
     *
     * @return array
     */
    public function getAllParentCategories()
    {
        $sCacheId = $this->cache()->set($this->_sModule . '_category_parent_all');

        if (!$aCategories = $this->cache()->get($sCacheId)) {
            $aCategories = $this->database()->select('*')
                ->from($this->_sTable)->where('parent_id=0')
                ->order('ordering ASC')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheId, $aCategories);
            Phpfox::getLib('cache')->group( $this->_sModule . '_category', $sCacheId);
        }
        return $aCategories;
    }
    
    /**
     * Get a category for manage
     *
     * @param int $iParentCategoryId
     *
     * @return array
     */
    public function getForManage($iParentCategoryId = 0)
    {
        $iLangId = Phpfox::getLanguageId();
        $sCacheId = $this->cache()->set($this->_sModule . '_category_manage_' . $iLangId . '_' . $iParentCategoryId);

        if (!$aCategories = $this->cache()->get($sCacheId)) {
            if ($iParentCategoryId > 0) {
                $sWhere = 'parent_id=' . (int)$iParentCategoryId;
            } else {
                $sWhere = 'parent_id=0';
            }
            $aCategories = $this->database()->select('*')
                ->from($this->_sTable)
                ->where($sWhere)
                ->group('category_id')
                ->order('ordering ASC')
                ->execute('getSlaveRows');
            //Get number items used
            foreach ($aCategories as $iKey => $aCategory) {
                $iTotalUsed = $this->database()->select('count(*)')
                    ->from($this->_sTableData)
                    ->where('category_id=' . (int)$aCategory['category_id'])
                    ->execute('getSlaveField');
                $aCategories[$iKey]['used'] = $iTotalUsed;
                $aCategories[$iKey]['link'] = Phpfox::permalink($this->_sModule . '.category',
                    $aCategory['category_id'], Phpfox::getSoftPhrase($aCategory['name']));
                $aCategories[$iKey]['total_sub'] = db()->select('COUNT(*)')->from($this->_sTable)->where(['parent_id' => $aCategory['category_id']])->executeField();
            }

            $this->cache()->save($sCacheId, $aCategories);
            Phpfox::getLib('cache')->group( $this->_sModule . '_category', $sCacheId);
        }

        return $aCategories;
    }

    /**
     * @param int $iCategoryId
     *
     * @return int
     */
    public function getCountSubCategory($iCategoryId)
    {
        $iCategoryId = (int) $iCategoryId;

        if ($iCategoryId <= 0) {
            return 0;
        }
        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('parent_id=' . $iCategoryId)
            ->executeField();

        return (int) $iCnt;
    }
}
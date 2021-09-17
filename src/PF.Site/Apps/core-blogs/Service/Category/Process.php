<?php
namespace Apps\Core_Blogs\Service\Category;

use Core_Service_Systems_Category_Process;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Process
 * @package Apps\Core_Blogs\Service\Category
 */
class Process extends Core_Service_Systems_Category_Process
{
    const BLOG_CATEGORY_DELETE_ALL_BLOG = 1;
    const BLOG_CATEGORY_DELETE_AND_MOVE_BLOG_TO_ANOTHER = 3;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('blog_category');
        $this->_sTableData = Phpfox::getT('blog_category_data');
        $this->_sModule = 'blog';
        parent::__construct();
    }

    /**
     * @param int $iBlogId
     * @param array $aCategories
     * @param bool $bUpdateUsageCount
     */
    public function addCategoryForBlog($iBlogId, $aCategories, $bUpdateUsageCount = true)
    {
        if (count($aCategories)) {
            $aCache = array();
            foreach ($aCategories as $iKey => $iId) {
                if (!is_numeric($iId)) {
                    continue;
                }

                if (isset($aCache[$iId])) {
                    continue;
                }

                $aCache[$iId] = true;

                $this->database()->insert(Phpfox::getT('blog_category_data'),
                    array('blog_id' => $iBlogId, 'category_id' => $iId));
                if ($bUpdateUsageCount === true) {
                    $this->database()->updateCount('blog_category_data', 'category_id = ' . (int)$iId, 'used',
                        'blog_category', 'category_id = ' . (int)$iId);
                }
            }
        }
    }

    /**
     * @param int $iBlogId
     * @param array $aCategories
     * @param bool $bUpdateUsageCount
     * @param bool $bDecreaseUsageCount
     */
    public function updateCategoryForBlog($iBlogId, $aCategories, $bUpdateUsageCount, $bDecreaseUsageCount = true)
    {
        $aRows = $this->database()->select('category_id')
            ->from(Phpfox::getT('blog_category_data'))
            ->where('blog_id = ' . (int)$iBlogId)
            ->execute('getSlaveRows');

        if (count($aRows)) {
            foreach ($aRows as $aRow) {
                $this->database()->delete(Phpfox::getT('blog_category_data'),
                    "blog_id = " . (int)$iBlogId . " AND category_id = " . (int)$aRow["category_id"]);
                if ($bDecreaseUsageCount && $bUpdateUsageCount) {
                    $this->database()->update(Phpfox::getT('blog_category'), ['used' => 'used - 1'],
                        ['category_id' => $aRow["category_id"]], false);
                }
            }
        }

        $this->addCategoryForBlog($iBlogId, $aCategories, $bUpdateUsageCount);
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('blog.service_category_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);

        return false;
    }

    /**
     * @param $iCategoryId
     * @param array $aVals
     * @return bool
     */
    public function deleteCategory($iCategoryId, $aVals = array())
    {
        $aCategory = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id=' . intval($iCategoryId))
            ->execute('getSlaveRow');

        // Delete phrase of category
        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }

        if ($aVals && isset($aVals['delete_type'])) {
            switch ($aVals['delete_type']) {
                case self::BLOG_CATEGORY_DELETE_ALL_BLOG:
                    $aItems = db()->select('d.blog_id')
                        ->from($this->_sTableData, 'd')
                        ->where("d.category_id = " . intval($iCategoryId))
                        ->execute('getSlaveRows');
                    foreach ($aItems as $aItem) {
                        $iBlogId = $aItem['blog_id'];
                        Phpfox::getService('blog.process')->delete($iBlogId);
                    }
                    break;
                case self::BLOG_CATEGORY_DELETE_AND_MOVE_BLOG_TO_ANOTHER:
                    if (!empty($aVals['new_category_id'])) {
                        $aItems = db()->select('d.blog_id')
                            ->from($this->_sTableData, 'd')
                            ->where("d.category_id = " . intval($iCategoryId))
                            ->execute('getSlaveRows');
                        foreach ($aItems as $aItem) {
                            $iBlogId = $aItem['blog_id'];
                            db()->delete($this->_sTableData,
                                'category_id = ' . intval($aVals['new_category_id']) . ' AND blog_id = ' . intval($iBlogId));
                        }
                        db()->update($this->_sTableData,
                            array('category_id' => intval($aVals['new_category_id'])),
                            'category_id = ' . intval($iCategoryId));
                    }
                    break;
                default:
                    break;
            }
        }

        db()->update($this->_sTable, array('parent_id' => 0), 'parent_id = ' . intval($iCategoryId));
        db()->delete($this->_sTable, 'category_id = ' . intval($iCategoryId));
        $this->cache()->remove();

        return true;
    }

    /**
     * @deprecated from 4.6
     *
     * @param array $aIds
     *
     * @return true
     */
    public function deleteMultiple($aIds)
    {
        foreach ($aIds as $iId) {
            $this->delete($iId);
        }
        return true;
    }

    /**
     * @deprecated from 4.6
     * Active or De-active a blog category
     *
     * @param int $iCategoryId
     * @param int $iActive
     */
    public function toggleCategory($iCategoryId, $iActive)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        $this->database()->update(Phpfox::getT('blog_category'), [
            'is_active' => (int)($iActive == '1' ? 1 : 0)
        ], 'category_id = ' . (int)$iCategoryId);

        $this->cache()->remove();
    }
}

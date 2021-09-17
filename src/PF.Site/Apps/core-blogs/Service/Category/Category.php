<?php
namespace Apps\Core_Blogs\Service\Category;

use Core_Service_Systems_Category_Category;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Category
 * @package Apps\Core_Blogs\Service\Category
 */
class Category extends Core_Service_Systems_Category_Category
{
    private $_aBlogCategories = array();

    private $_iCnt = 0;

    private $_sLanguageId = '';
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('blog_category');
        $this->_sTableData = Phpfox::getT('blog_category_data');
        $this->_sModule = 'blog';
        $this->_sLanguageId = Phpfox::getLanguageId();
        parent::__construct();
    }

    /**
     * @param array $aConds
     * @param string $sSort
     *
     * @return array
     */
    public function getCategories($aConds, $sSort = 'c.ordering ASC, c.category_id DESC')
    {
        $sCacheId = $this->cache()->set('blog_category_get_' . md5(serialize($aConds) . $sSort) . '_' . $this->_sLanguageId);
        $this->cache()->group('blog_category',$sCacheId);
        if (!$aItems = $this->cache()->get($sCacheId)) {

            (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getcategories_start')) ? eval($sPlugin) : false);

            $aItems = db()->select('c.category_id, c.name, c.name, c.user_id')
                ->from(Phpfox::getT('blog_category'), 'c')
                ->where($aConds)
                ->group('c.category_id', true)
                ->order($sSort)
                ->execute('getSlaveRows');

            (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getcategories_end')) ? eval($sPlugin) : false);
            $this->cache()->save($sCacheId, $aItems);
        }

        return $aItems;
    }

    /**
     * Get an category with given id
     *
     * @param $iId
     * @return array|int|string
     */
    public function getCategory($iId)
    {
        $aCategory = db()->select('bc.*, bc2.name AS parent_name')
            ->from($this->_sTable, 'bc')
            ->leftJoin($this->_sTable, 'bc2', 'bc2.category_id = bc.parent_id')
            ->where('bc.category_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        return $aCategory;
    }

    /**
     * @param $sCategory
     * @return mixed|string
     */
    public function getAllCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('blog_category_parent_' . $sCategory . '_' . $this->_sLanguageId);
        $this->cache()->group('blog_category',$sCacheId);
        if (!($sCategories = $this->cache()->get($sCacheId))) {
            $iCategory = db()->select('category_id')
                ->from($this->_sTable)
                ->where('is_active = 1 AND category_id = ' . (int)$sCategory)
                ->execute('getSlaveField');

            $sCategories = $this->_getIds($sCategory);
            $sCategories = rtrim($iCategory . ',' . ltrim($sCategories, $iCategory . ','), ',');

            $this->cache()->save($sCacheId, $sCategories);
        }

        return $sCategories;
    }

    /**
     * @param $sCategory
     * @return mixed|string
     */
    public function getParentCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('blog_category_parent_extend_' . $sCategory . '_' . $this->_sLanguageId);
        $this->cache()->group('blog_category',$sCacheId);
        if (!($sCategories = $this->cache()->get($sCacheId))) {
            $iCategory = db()->select('category_id')
                ->from($this->_sTable)
                ->where('category_id = \'' . (int)$sCategory . '\'')
                ->execute('getSlaveField');

            $sCategories = $this->_getParentIds($iCategory);

            $sCategories = rtrim($sCategories, ',');

            $this->cache()->save($sCacheId, $sCategories);
        }

        return $sCategories;
    }

    /**
     * @param $sCategory
     * @return array|mixed|null
     */
    public function getParentBreadcrumb($sCategory)
    {
        $sCacheId = $this->cache()->set('blog_parent_breadcrumb_' . md5($sCategory) . '_' . $this->_sLanguageId);
        $this->cache()->group('blog_category',$sCacheId);
        if (!($aBreadcrumb = $this->cache()->get($sCacheId))) {
            $sCategories = $this->getParentCategories($sCategory);

            $aCategories = db()->select('*')
                ->from($this->_sTable)
                ->where('category_id IN(' . $sCategories . ')')
                ->execute('getSlaveRows');

            $aBreadcrumb = $this->getCategoriesByIdExtended(null, $aCategories);

            $this->cache()->save($sCacheId, $aBreadcrumb);
        }

        return $aBreadcrumb;
    }

    /**
     * @param null $iId
     * @param null $aCategories
     * @return array|null
     */
    public function getCategoriesByIdExtended($iId = null, &$aCategories = null)
    {
        return $this->getCategoriesByBlogId($iId, $aCategories);
    }

    /**
     * @param $iId
     * @param null $aCategories
     * @return array|null
     */
    public function getCategoriesByBlogId($iId, &$aCategories = null)
    {
        if ($aCategories === null) {
            $aCategories = db()->select('bc.parent_id, bc.category_id, bc.name')
                ->from(Phpfox::getT('blog_category_data'), 'bcd')
                ->join($this->_sTable, 'bc', 'bc.category_id = bcd.category_id')
                ->where('bcd.blog_id IN (' . (int)$iId . ') AND bc.is_active = 1')
                ->order('bc.parent_id ASC, bc.ordering ASC')
                ->execute('getSlaveRows');
        }

        if (!count($aCategories)) {
            return null;
        }

        $aBreadcrumb = array();
        foreach ($aCategories as $aCategory) {
            $aParentCat = $this->getCategory($aCategory['parent_id']);
            if (!empty($aParentCat['category_id']) && !$aParentCat['is_active']) {
                continue;
            }
            $aBreadcrumb[] = array(
                'category_name' => Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])),
                'link' => Phpfox::permalink('blog.category', $aCategory['category_id'],
                    Phpfox::getSoftPhrase($aCategory['name'])),
                'category_id' => $aCategory['category_id']
            );
        }
        return $aBreadcrumb;
    }

    /**
     * @param $iId
     * @return bool|mixed
     */
    public function getCategoryIds($iId)
    {
        return (isset($this->_aBlogCategories[$iId]) ? $this->_aBlogCategories[$iId] : false);
    }

    /**
     * Gets all the categories and caches them with HTML already built into it.
     * @param boolean $bAnchor
     * @param boolean $bDropDown
     * @param boolean $bMultiLevel
     * @return string HTML categories.
     */
    public function getSelect($bAnchor = true, $bDropDown = false, $bMultiLevel = true)
    {
        $sCacheId = $this->cache()->set('blog_category_html' . ($bDropDown ? '_drop' : ($bAnchor === true ? '_anchor' : '')) . '_' . $this->_sLanguageId);
        $this->cache()->group('blog_category',$sCacheId);
        if (!($sCategories = $this->cache()->get($sCacheId))) {
            $sCategories = $this->_get(0, $bAnchor, $bDropDown, $bMultiLevel);
            $this->cache()->save($sCacheId, $sCategories);
        }

        return $sCategories;
    }

    /**
     * Gets the categories and subcategories (if available) in an array to use with the core.block.category template
     *
     * @param int|null $iCategoryId
     * @param string|null $sIsRatingArea
     *
     * @return array
     */
    public function getForBrowse($iCategoryId = null, $sIsRatingArea = null)
    {
        $hash = md5($iCategoryId === null ? '' : '_' . $iCategoryId) . (empty($sIsRatingArea) ? '' : '_' . $sIsRatingArea);

        $sCacheId = $this->cache()->set('blog_category_browse_' . $hash . '_' . $this->_sLanguageId);
        $this->cache()->group('blog_category',$sCacheId);
        if (!($aCategories = $this->cache()->get($sCacheId))) {
            $aCategories = db()->select('mc.category_id, mc.name')
                ->from($this->_sTable, 'mc')
                ->where('mc.is_active = 1 AND mc.parent_id = ' . ($iCategoryId === null ? '0' : (int)$iCategoryId) . '')
                ->order('mc.ordering ASC, mc.category_id DESC')
                ->execute('getSlaveRows');
            foreach ($aCategories as $iKey => $aCategory) {
                if ($sIsRatingArea === null) {
                    $aCategories[$iKey]['url'] = Phpfox::permalink('blog.category', $aCategory['category_id'],
                        Phpfox::getSoftPhrase($aCategory['name']));
                } else {
                    $aCategories[$iKey]['url'] = Phpfox::permalink('blog.' . $sIsRatingArea . '.category',
                        $aCategory['category_id'], Phpfox::getSoftPhrase($aCategory['name']));
                }

                $aCategories[$iKey]['sub'] = db()->select('mc.category_id, mc.name')
                    ->from($this->_sTable, 'mc')
                    ->where('mc.is_active = 1 AND mc.parent_id = ' . $aCategory['category_id'] . '')
                    ->order('mc.ordering ASC, mc.category_id DESC')
                    ->execute('getSlaveRows');

                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                    if ($sIsRatingArea === null) {
                        $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('blog.category',
                            $aSubCategory['category_id'], $aSubCategory['name']);
                    } else {
                        $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('blog.' . $sIsRatingArea . '.category',
                            $aSubCategory['category_id'], $aSubCategory['name']);
                    }
                }
            }

            $this->cache()->save($sCacheId, $aCategories);
        }

        return $aCategories;
    }

    /**
     * Gets categories based on their parent ID#.
     *
     * @param int $iParentId Category ID#.
     * @param  boolean $bAnchor
     * @param boolean $bDropDown
     * @param boolean $bMultiLevel
     *
     * @return string HTML categories.
     */
    public function _get($iParentId, $bAnchor = true, $bDropDown = false, $bMultiLevel = true)
    {
        if ($bAnchor === false) {
            static $iCount = 0;

            $iCount++;
        }

        $aCategories = db()->select('bc.name, bc.category_id, bc.parent_id')
            ->from($this->_sTable, 'bc')
            ->where('bc.is_active = 1 AND bc.parent_id = ' . (int)$iParentId)
            ->order('bc.ordering ASC, bc.category_id DESC')
            ->execute('getSlaveRows');

        if (!count($aCategories)) {
            return '';
        }

        if ($iParentId != 0) {
            $this->_iCnt++;
        }

        $sCategories = ($bDropDown ? '' : '<ul>');
        foreach ($aCategories as $aCategory) {
            if ($bDropDown) {
                $sCategories .= '<option class="js_blog_category_' . $aCategory['category_id'] . '" value="' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;',
                            ($this->_iCnt * 2)) . ' ' : '') . Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</option>';

                if ($bMultiLevel) {
                    $sCategories .= $this->_get($aCategory['category_id'], false, true);
                }
            } else {
                if ($bAnchor === true) {
                    $sCategories .= '<li><a href="' . Phpfox::permalink('blog', $aCategory['category_id'],
                            $aCategory['name']) . '" class="js_blog_category" id="js_blog_category_' . $aCategory['category_id'] . '">' . Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</a>' . $this->_get($aCategory['category_id'],
                            $bAnchor) . '</li>';
                } else {
                    $sCategories .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image',
                            'misc/draggable.png') . '" alt="" /> <span class="js_blog_category" id="js_sortable_category_' . $aCategory['category_id'] . '">' . Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</span>' . $this->_get($aCategory['category_id'],
                            $bAnchor) . '</li>';
                }
            }
        }
        $sCategories .= ($bDropDown ? '' : '</ul>');

        $this->_iCnt = 0;

        return $sCategories;
    }

    /**
     * Given an array of categories (which may have sub-categories) this function
     * returns a one-dimensional array with the category_ids of the child
     * elements.
     * @param array $aCats
     * @return array
     */
    public function extractCategories($aCats)
    {
        $aOut = array();

        foreach ($aCats as $aCategory) {
            $aOut[] = $aCategory['category_id'];
            if (!empty($aCategory['sub'])) {
                $aOut = array_merge($aOut, array_values($this->extractCategories($aCategory['sub'])));
            }
        }
        return $aOut;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('blog.service_category__call')) {
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
     * Gets the parent categories based on the category ID
     *
     * @param int $iParentId Category ID to check all the parent ID
     * @param boolean $bPassName True to pass the name of the category, or false to only pass the URL string
     *
     * @return string Returns the fixed URL string
     */
    private function _getParentsUrl($iParentId, $bPassName = false)
    {
        // Cache the round we are going to increment
        static $iCnt = 0;

        // Add to the cached round
        $iCnt++;

        // Check if this is the first round
        if ($iCnt === 1) {
            // Cache the cache ID
            static $sCacheId = null;

            // Check if we have this data already cached
            $sCacheId = $this->cache()->set('blog_category_url' . ($bPassName ? '_name' : '') . '_' . $iParentId . '_' . $this->_sLanguageId);
            $this->cache()->group('blog_category',$sCacheId);
            if ($sParents = $this->cache()->get($sCacheId)) {
                return $sParents;
            }
        }

        // Get the menus based on the category ID
        $aParents = db()->select('category_id, name, parent_id')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iParentId)
            ->execute('getSlaveRows');

        // Loop thur all the sub menus
        $sParents = '';
        foreach ($aParents as $aParent) {
            $sParents .= ($bPassName ? '|' . $aParent['name'] . '|' . $aParent['category_id'] : '') . '/' . $this->_getParentsUrl($aParent['parent_id'],
                    $bPassName);
        }

        // Save the cached based on the static cache ID
        if (isset($sCacheId)) {
            $this->cache()->save($sCacheId, $sParents);
        }

        // Return the loop
        return $sParents;

    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @param int $notInclude
     * @return array|int|string
     */
    public function getForAdmin($iParentId = 0, $bGetSub = 1, $bCareActive = 0, $notInclude = 0)
    {
        $aRows = db()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId . ($bCareActive ? ' AND is_active = 1' : '') . ' AND category_id <> ' . $notInclude)
            ->order('ordering ASC, category_id DESC')
            ->execute('getSlaveRows');
        if ($bGetSub) {
            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['sub'] = $this->getForAdmin($aRow['category_id'], 1, $bCareActive, $notInclude);
            }
        }
        foreach ($aRows as $iKey => $aCategory) {
            $aRows[$iKey]['name'] = Phpfox::getSoftPhrase($aCategory['name']);
            $aRows[$iKey]['url'] = Phpfox::permalink('blog.category', $aCategory['category_id'],
                Phpfox::getSoftPhrase($aCategory['name']));
            $aRows[$iKey]['used'] = $this->getTotalItemBelongToCategory($aCategory['category_id'], 1);
        }
        return $aRows;
    }

    /**
     * @param $iCategoryId
     * @param int $bIncludeSub
     * @return array|int|string
     */
    public function getTotalItemBelongToCategory($iCategoryId, $bIncludeSub = 0)
    {
        $sChildIds = $this->getChildIds($iCategoryId);
        if ($bIncludeSub && $sChildIds) {
            $sChildIds .= ',' . $iCategoryId;
            return db()->select('COUNT(DISTINCT bcd.blog_id)')
                ->from($this->_sTableData, 'bcd')
                ->where('bcd.category_id IN (' . $sChildIds . ')')
                ->execute('getSlaveField');
        } else {
            return db()->select('COUNT(Distinct bcd.blog_id)')
                ->from($this->_sTableData, 'bcd')
                ->where('bcd.category_id = ' . $iCategoryId)
                ->execute('getSlaveField');
        }
    }

    /**
     * @param $iParentId
     * @return string
     */
    public function getChildIds($iParentId)
    {
        $sCategories = $this->_getIds($iParentId);
        $sCategories = trim($sCategories, ',');
        return $sCategories;
    }

    /**
     * @param $iParentId
     * @return string
     */
    private function _getIds($iParentId)
    {
        $aCategories = db()->select('bc.name, bc.category_id')
            ->from($this->_sTable, 'bc')
            ->where('bc.parent_id = ' . (int)$iParentId)
            ->execute('getSlaveRows');
        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getIds($aCategory['category_id']) . '';
        }
        return $sCategories;
    }

    /**
     * @param $iId
     * @return string
     */
    private function _getParentIds($iId)
    {
        $aCategories = db()->select('bc.category_id, bc.parent_id')
            ->from($this->_sTable, 'bc')
            ->where('bc.category_id = ' . (int)$iId)
            ->execute('getSlaveRows');
        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getParentIds($aCategory['parent_id']) . '';
        }
        return $sCategories;
    }

    /**************************************************************************************************************************/
    /*============================= OLD FUNCTIONS SECTION (SHOULD NOT USE AND SHOULD BE REMOVED) =============================*/
    /* Please note that the following section will be removed in phpFox 4.6. Be carefully when using them
    /**************************************************************************************************************************/

    /**
     * Get Categories by list of Id
     * @deprecated from 4.6.0
     * @param string $sId list of categories ID
     *
     * @return array
     */
    public function getCategoriesById($sId)
    {
        if (!$sId) {
            return [];
        }
        $aItems = db()->select('d.blog_id, d.category_id, c.name AS category_name, c.user_id')
            ->from(Phpfox::getT('blog_category_data'), 'd')
            ->join(Phpfox::getT('blog_category'), 'c', 'd.category_id = c.category_id')
            ->where("c.is_active = 1 AND d.blog_id IN(" . $sId . ")")
            ->execute('getSlaveRows');

        $aCategories = [];
        foreach ($aItems as $aItem) {
            $aCategories[$aItem['blog_id']][] = $aItem;
        }
        return $aCategories;
    }

    /**
     * @deprecated from 4.6.0
     * @param string $sName
     * @param int $iUserId
     * @param array $aConds
     * @param string $sSort
     * @param string $iPage
     * @param string $sLimit
     *
     * @return array
     */
    public function getBlogsByCategory($sName, $iUserId, $aConds = array(), $sSort = '', $iPage = '', $sLimit = '')
    {
        $aConds = array_merge(array("AND blog_category.user_id = " . (int)$iUserId), $aConds);
        $aConds = array_merge(array("AND (blog_category.category_id = " . $sName . " OR blog_category.name = '" . db()->escape($sName) . "') "),
            $aConds);

        $aItems = array();
        (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getblogsbycategory_count')) ? eval($sPlugin) : false);

        $iCnt = db()->select('COUNT(DISTINCT blog.blog_id)')
            ->from(Phpfox::getT('blog'), 'blog')
            ->innerJoin(Phpfox::getT('blog_category_data'), 'blog_category_data',
                'blog_category_data.blog_id = blog.blog_id')
            ->innerJoin(Phpfox::getT('blog_category'), 'blog_category',
                'blog_category.category_id = blog_category_data.category_id')
            ->where($aConds)
            ->execute('getSlaveField');

        if ($iCnt) {
            (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getblogsbycategory_query')) ? eval($sPlugin) : false);
            $aItems = db()->select("blog.*, " . (Phpfox::getParam('core.allow_html') ? "blog_text.text_parsed" : "blog_text.text") . " AS text, blog_category.category_id AS category_id, blog_category.name AS category_name, " . Phpfox::getUserField())
                ->from(Phpfox::getT('blog'), 'blog')
                ->innerJoin(Phpfox::getT('blog_category_data'), 'blog_category_data',
                    'blog_category_data.blog_id = blog.blog_id')
                ->innerJoin(Phpfox::getT('blog_category'), 'blog_category',
                    'blog_category.category_id = blog_category_data.category_id')
                ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
                ->join(Phpfox::getT('user'), 'u', 'blog.user_id = u.user_id')
                ->where($aConds)
                ->group('blog.blog_id', true)
                ->order($sSort)
                ->limit($iPage, $sLimit, $iCnt)
                ->execute('getSlaveRows');
        }

        return array($iCnt, $aItems);
    }

    /**
     * @deprecated from 4.6.0
     * Get blog search result
     * @param array $aConds
     * @param string $sSort
     *
     * @return array
     */
    public function getSearch($aConds, $sSort)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_getsearch')) ? eval($sPlugin) : false);
        $aRows = db()->select('blog.blog_id')
            ->from(Phpfox::getT('blog'), 'blog')
            ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
            ->innerJoin(Phpfox::getT('blog_category_data'), 'blog_category_data',
                'blog_category_data.blog_id = blog.blog_id')
            ->innerJoin(Phpfox::getT('blog_category'), 'blog_category',
                'blog_category.category_id = blog_category_data.category_id')
            ->where($aConds)
            ->order($sSort)
            ->execute('getSlaveRows');

        $aSearchIds = array();
        foreach ($aRows as $aRow) {
            $aSearchIds[] = $aRow['blog_id'];
        }

        return $aSearchIds;
    }

    /**
     * @deprecated from 4.6
     * Get all blog categories for admin
     *
     * @param array|string $aConds
     * @param string $sSort
     *
     * @return array
     */
    public function get($aConds = 'true', $sSort = 'c.ordering ASC, c.category_id DESC')
    {
        (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_get_start')) ? eval($sPlugin) : false);

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('blog_category'), 'c')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where($aConds)
            ->execute('getSlaveField');

        $aItems = array();
        if ($iCnt) {
            $aItems = $this->database()->select('c.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('blog_category'), 'c')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
                ->where($aConds)
                ->order($sSort)
                ->execute('getSlaveRows');
            foreach ($aItems as $iKey => $aItem) {
                $aItems[$iKey]['link'] = ($aItem['user_id'] ? Phpfox::getLib('url')->permalink($aItem['user_name'] . '.blog.category',
                    $aItem['category_id'], $aItem['name']) : Phpfox::getLib('url')->permalink('blog.category',
                    $aItem['category_id'], $aItem['name']));
            }
        }

        (($sPlugin = Phpfox_Plugin::get('blog.service_category_category_get_end')) ? eval($sPlugin) : false);

        return [$iCnt, $aItems];
    }
}

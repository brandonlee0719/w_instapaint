<?php

namespace Apps\Core_Photos\Service\Category;

use Core_Service_Systems_Category_Category;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Category extends Core_Service_Systems_Category_Category
{
    /**
     * @var array store all categories of site
     */
    private $_aPhotoCategories = array();

    /**
     * @var int
     */
    private $_iCnt = 0;

    private $_iCacheTime = 0;

    private $_sLanguageId = '';
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('photo_category');
        $this->_sTableData = Phpfox::getT('photo_category_data');
        $this->_sModule = 'photo';
        $this->_iCacheTime = Phpfox::getParam('event.categories_cache_time');
        $this->_sLanguageId = Phpfox::getLanguageId();
        parent::__construct();
    }

    public function getCategory($iId)
    {
        $aCategory = db()->select('pc.*, pc2.name AS parent_name')
            ->from($this->_sTable, 'pc')
            ->leftJoin($this->_sTable, 'pc2', 'pc2.category_id = pc.parent_id')
            ->where('pc.category_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (!empty($aCategory['category_id'])) {
            $aLanguages = Phpfox::getService('language')->getAll();
            foreach ($aLanguages as $aLanguage) {
                $aCategory['name_' . $aLanguage['language_id']] = Phpfox::getSoftPhrase($aCategory['name'], [], false,
                    null, $aLanguage['language_id']);
            }
        }
        return $aCategory;
    }

    public function getCategoryId($sName)
    {
        return db()->select('category_id')
            ->from($this->_sTable)
            ->where('name_url = "' . db()->escape($sName) . '"')
            ->execute('getSlaveField');
    }

    public function getPhotos(
        $sCategory,
        $mConditions = array(),
        $sOrder = 'p.time_stamp DESC',
        $iPage = '',
        $iPageSize = '',
        $aCallback = null
    ) {
        $sCategories = $this->getAllCategories($sCategory);

        if (empty($sCategories)) {
            return array(0, array());
        }

        $mConditions[] = ' AND pcd.category_id IN(' . $sCategories . ')';

        $aPhotos = array();
        $iCnt = db()->select('COUNT(DISTINCT p.photo_id)')
            ->from(Phpfox::getT('photo'), 'p')
            ->innerJoin(Phpfox::getT('photo_category_data'), 'pcd', 'pcd.photo_id = p.photo_id')
            ->where($mConditions)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aPhotos = db()->select('p.*, pcd.category_id, pa.name_url AS album_url, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('photo'), 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                ->innerJoin(Phpfox::getT('photo_category_data'), 'pcd', 'pcd.photo_id = p.photo_id')
                ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = p.album_id')
                ->where($mConditions)
                ->group('p.photo_id', true)
                ->order($sOrder)
                ->limit($iPage, $iPageSize, $iCnt)
                ->execute('getSlaveRows');

            $oUrl = Phpfox::getLib('url');
            foreach ($aPhotos as $iKey => $aPhoto) {
                $aPhotos[$iKey]['link'] = ($aCallback === null ? ($aPhoto['album_id'] ? $oUrl->makeUrl($aPhoto['user_name'],
                    array('photo', $aPhoto['album_url'], $aPhoto['title_url'])) : $oUrl->makeUrl($aPhoto['user_name'],
                    array('photo', 'view', $aPhoto['title_url']))) : $oUrl->makeUrl($aCallback['url_home'],
                    array('view', $aPhoto['title_url'])));
            }
        }

        return array($iCnt, $aPhotos);
    }

    public function getAllCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('photo_category_parent_' . $sCategory . '_' . $this->_sLanguageId);
        $this->cache()->group('photo_category',$sCacheId);

        if (!($sCategories = $this->cache()->get($sCacheId,$this->_iCacheTime))) {
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

    public function getCategoryName($sCategory)
    {
        return db()->select('name')
            ->from($this->_sTable)
            ->where('name_url = \'' . db()->escape($sCategory) . '\'')
            ->execute('getSlaveField');
    }

    public function getParentCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('photo_category_parent_extend_' . $sCategory . '_' . $this->_sLanguageId);
        $this->cache()->group('photo_category',$sCacheId);

        if (!($sCategories = $this->cache()->get($sCacheId, $this->_iCacheTime))) {
            $iCategory = db()->select('category_id')
                ->from($this->_sTable)
                ->where('category_id = \'' . (int)$sCategory . '\'')
                ->execute('getSlaveField');
            $sCategories = $this->_getParentIds($iCategory);
            $sCategories = ltrim($sCategories, ',');
            $this->cache()->save($sCacheId, $sCategories);
        }

        return $sCategories;
    }

    public function getParentBreadcrumb($sCategory)
    {
        $sCacheId = $this->cache()->set('photo_parent_breadcrumb_' . md5($sCategory) . '_' . $this->_sLanguageId);
        $this->cache()->group('photo_category',$sCacheId);
        if (!($aBreadcrumb = $this->cache()->get($sCacheId, $this->_iCacheTime))) {
            $sCategories = $this->getParentCategories($sCategory);
            $aCateId = explode(',',$sCategories);
            foreach ($aCateId as $iId) {
                $aCategories[] = db()->select('*')
                    ->from($this->_sTable)
                    ->where('category_id ='.$iId)
                    ->execute('getRow');
            }

            $aBreadcrumb = $this->getCategoriesByIdExtended(null, $aCategories);

            $this->cache()->save($sCacheId, $aBreadcrumb);
        }

        return $aBreadcrumb;
    }

    public function getCategoriesByIdExtended($iId = null, &$aCategories = null)
    {
        return $this->getCategoriesById($iId, $aCategories);
    }

    public function getCategoriesById($iId, &$aCategories = null)
    {
        if ($aCategories === null) {
            $aCategories = db()->select('pc.parent_id, pc.category_id, pc.name')
                ->from(Phpfox::getT('photo_category_data'), 'pcd')
                ->join($this->_sTable, 'pc', 'pc.category_id = pcd.category_id')
                ->where('pcd.photo_id = ' . (int)$iId . ' AND pc.is_active = 1')
                ->order('pc.parent_id ASC, pc.ordering ASC')
                ->execute('getSlaveRows');
        }

        if (!count($aCategories)) {
            return null;
        }

        $aBreadcrumb = array();
        if (count($aCategories) > 1) {
            foreach ($aCategories as $aCategory) {
                $aParentCat = $this->getCategory($aCategory['parent_id']);
                if (!empty($aParentCat['category_id']) && !$aParentCat['is_active']) {
                    continue;
                }
                $aBreadcrumb[] = array(
                    Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])),
                    Phpfox::permalink('photo.category', $aCategory['category_id'],
                        Phpfox::getSoftPhrase($aCategory['name'])),
                    'category_id' => $aCategory['category_id']
                );
            }
        } else {
            $aParentCat = $this->getCategory($aCategories[0]['parent_id']);
            if (!empty($aParentCat['category_id']) && !$aParentCat['is_active']) {
                return null;
            }
            $aBreadcrumb[] = array(
                Phpfox::getLib('locale')->convert($aCategories[0]['name']),
                Phpfox::permalink('photo.category', $aCategories[0]['category_id'], $aCategories[0]['name']),
                'category_id' => $aCategories[0]['category_id']
            );
        }
        return $aBreadcrumb;
    }

    public function getCategoryIds($iId)
    {
        return (isset($this->_aPhotoCategories[$iId]) ? $this->_aPhotoCategories[$iId] : false);
    }

    public function hasCategories($bAnchor = false, $bDropDown = true)
    {
        $mCategories = $this->get($bAnchor, $bDropDown);

        return ((is_array($mCategories) || (is_string($mCategories) && !empty($mCategories))) ? true : false);
    }

    /**
     * Gets all the categories and caches them with HTML already built into it.
     * @param boolean $bAnchor
     * @param boolean $bDropDown
     * @param boolean $bMultiLevel
     * @return string HTML categories.
     */
    public function get($bAnchor = true, $bDropDown = false, $bMultiLevel = true)
    {
        $sCacheId = $this->cache()->set('photo_category_html' . ($bDropDown ? '_drop' : ($bAnchor === true ? '_anchor' : '')) . '_' . $this->_sLanguageId);
        $this->cache()->group('photo_category',$sCacheId);
        if (!($sCategories = $this->cache()->get($sCacheId, $this->_iCacheTime))) {
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
        $sCacheId = $this->cache()->set('photo_category_browse_' . $hash . '_' . $this->_sLanguageId);
        $this->cache()->group('photo_category',$sCacheId);
        if (!($aCategories = $this->cache()->get($sCacheId, $this->_iCacheTime))) {
            $aCategories = db()->select('mc.category_id, mc.name')
                ->from($this->_sTable, 'mc')
                ->where('mc.is_active = 1 AND mc.parent_id = ' . ($iCategoryId === null ? '0' : (int)$iCategoryId) . '')
                ->order('mc.ordering ASC')
                ->execute('getSlaveRows');

            foreach ($aCategories as $iKey => $aCategory) {
                $aCategories[$iKey]['sub'] = db()->select('mc.category_id, mc.name')
                    ->from($this->_sTable, 'mc')
                    ->where('mc.is_active = 1 AND mc.parent_id = ' . $aCategory['category_id'] . '')
                    ->order('mc.ordering ASC')
                    ->execute('getSlaveRows');
            }
            $this->cache()->save($sCacheId, $aCategories);
        }

        foreach ($aCategories as $iKey => $aCategory) {
            if ($sIsRatingArea === null) {
                $aCategories[$iKey]['url'] = Phpfox::permalink('photo.category', $aCategory['category_id'],
                    Phpfox::getSoftPhrase($aCategory['name']));
            } else {
                $aCategories[$iKey]['url'] = Phpfox::permalink('photo.' . $sIsRatingArea . '.category',
                    $aCategory['category_id'], Phpfox::getSoftPhrase($aCategory['name']));
            }
            if (count($aCategory['sub'])) {
                foreach ($aCategory['sub'] as $iSubKey => $aSubCategory) {
                    if ($sIsRatingArea === null) {
                        $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('photo.category',
                            $aSubCategory['category_id'], $aSubCategory['name']);
                    } else {
                        $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('photo.' . $sIsRatingArea . '.category',
                            $aSubCategory['category_id'], $aSubCategory['name']);
                    }
                }
            }
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

        $aCategories = db()->select('pc.name, pc.name_url, pc.category_id, pc.parent_id')
            ->from($this->_sTable, 'pc')
            ->where('pc.is_active = 1 AND pc.parent_id = ' . (int)$iParentId)
            ->order('pc.ordering ASC')
            ->execute('getSlaveRows');

        if (!count($aCategories)) {
            return '';
        }

        if ($iParentId != 0) {
            $this->_iCnt++;
        }

        $sCategories = ($bDropDown ? '' : '<ul>');
        foreach ($aCategories as $aCategory) {
            $mUrl = $aCategory['name_url'];
            if (!empty($aCategory['parent_id'])) {
                $aParts = explode('/', $this->_getParentsUrl($aCategory['parent_id']));
                $aParts = array_reverse($aParts);
                $mUrl = array();
                foreach ($aParts as $sPart) {
                    if (empty($sPart)) {
                        continue;
                    }
                    $mUrl[] = $sPart;
                }
                $mUrl[] = $aCategory['name_url'];
            }

            if ($bDropDown) {
                $sCategories .= '<option class="js_photo_category_' . $aCategory['category_id'] . '" value="' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;',
                            ($this->_iCnt * 2)) . ' ' : '') . Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</option>';

                if ($bMultiLevel) {
                    $sCategories .= $this->_get($aCategory['category_id'], false, true);
                }
            } else {
                if ($bAnchor === true) {
                    $sCategories .= '<li><a href="' . Phpfox::getLib('url')->makeUrl('photo',
                            $mUrl) . '" class="js_photo_category" id="js_photo_category_' . $aCategory['category_id'] . '">' . Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</a>' . $this->_get($aCategory['category_id'],
                            $bAnchor) . '</li>';
                } else {
                    $sCategories .= '<li><img src="' . Phpfox::getLib('template')->getStyle('image',
                            'misc/draggable.png') . '" alt="" /> <span class="js_photo_category" id="js_sortable_category_' . $aCategory['category_id'] . '">' . Phpfox::getLib('locale')->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</span>' . $this->_get($aCategory['category_id'],
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
        if ($sPlugin = Phpfox_Plugin::get('photo.service_category__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
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
            $sCacheId = $this->cache()->set('photo_category_url' . ($bPassName ? '_name' : '') . '_' . $iParentId . '_' . $this->_sLanguageId);
            $this->cache()->group('photo_category',$sCacheId);
            if ($sParents = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
                return $sParents;
            }
        }

        // Get the menus based on the category ID
        $aParents = db()->select('category_id, name, name_url, parent_id')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iParentId)
            ->execute('getSlaveRows');

        // Loop thur all the sub menus
        $sParents = '';
        foreach ($aParents as $aParent) {
            $sParents .= $aParent['name_url'] . ($bPassName ? '|' . $aParent['name'] . '|' . $aParent['category_id'] : '') . '/' . $this->_getParentsUrl($aParent['parent_id'],
                    $bPassName);
        }

        // Save the cached based on the static cache ID
        if (isset($sCacheId)) {
            $this->cache()->save($sCacheId, $sParents);
        }

        // Return the loop
        return $sParents;

    }

    private function _getIds($iParentId, $bUseId = true)
    {
        $aCategories = db()->select('pc.name, pc.category_id')
            ->from($this->_sTable, 'pc')
            ->where(($bUseId ? 'pc.parent_id = ' . (int)$iParentId . '' : 'pc.name_url = \'' . db()->escape($iParentId) . '\''))
            ->execute('getSlaveRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getIds($aCategory['category_id']) . '';
        }

        return $sCategories;
    }

    private function _getParentIds($iId)
    {
        $aCategories = db()->select('pc.category_id, pc.parent_id')
            ->from($this->_sTable, 'pc')
            ->where('pc.category_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $this->_getParentIds($aCategory['parent_id']) . ','. $aCategory['category_id'];
        }

        return $sCategories;
    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @param int $notInclude
     * @param int $isFirst
     * @return array|int|mixed|string
     */
    public function getForAdmin($iParentId = 0, $bGetSub = 1, $bCareActive = 0, $notInclude = 0, $isFirst = 1)
    {
        if ($isFirst) {
            $hash = md5($iParentId === null ? '' : '_' . $iParentId) . (empty($bGetSub) ? '' : '_' . $bGetSub) . (empty($bCareActive) ? '' : '_' . $bCareActive) . (empty($notInclude) ? '' : '_' . $notInclude);
            $sCacheId = $this->cache()->set('photo_category_admin_' . $hash . '_' . $this->_sLanguageId);
            $this->cache()->group('photo_category',$sCacheId);
        }
        if (!$isFirst || !($aRows = $this->cache()->get($sCacheId, $this->_iCacheTime))) {
            $aRows = db()->select('*')
                ->from($this->_sTable)
                ->where('parent_id = ' . (int)$iParentId . ($bCareActive ? ' AND is_active = 1' : '') . ' AND category_id <> ' . $notInclude)
                ->order('ordering ASC')
                ->execute('getSlaveRows');

            if ($bGetSub) {
                foreach ($aRows as $iKey => $aRow) {
                    $aRows[$iKey]['sub'] = $this->getForAdmin($aRow['category_id'], 1, $bCareActive, $notInclude, 0);
                }
            }
            if ($isFirst) {
                $this->cache()->save($sCacheId, $aRows);
            }
        }

        foreach ($aRows as $iKey => $aCategory) {
            $aRows[$iKey]['name'] = Phpfox::getSoftPhrase($aCategory['name']);
            $aRows[$iKey]['url'] = Phpfox::permalink('photo.category', $aCategory['category_id'],
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
            return db()->select('COUNT(Distinct pcd.photo_id)')
                ->from($this->_sTableData, 'pcd')
                ->where('pcd.category_id IN (' . $sChildIds . ')')
                ->execute('getSlaveField');
        } else {
            return db()->select('COUNT(Distinct pcd.photo_id)')
                ->from($this->_sTableData, 'pcd')
                ->where('pcd.category_id = ' . $iCategoryId)
                ->execute('getSlaveField');
        }

    }

    /**
     * @param $iParentId
     * @return string
     */
    public function getChildIds($iParentId)
    {
        $sCategories = $this->_getIds($iParentId, 1);
        $sCategories = trim($sCategories, ',');
        return $sCategories;
    }
}
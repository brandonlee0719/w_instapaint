<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Events\Service\Category;

use Phpfox;
use Phpfox_Error;
use Phpfox_Locale;
use Phpfox_Plugin;
use Phpfox_Template;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Category
 * @package Apps\Core_Events\Service\Category
 */
class Category extends \Core_Service_Systems_Category_Category
{
    /**
     * @var string
     */
    private $_sOutput = '';

    /**
     * @var int
     */
    private $_iCnt = 0;

    /**
     * @var string
     */
    private $_sDisplay = 'select';

    private $_sLanguageId = '';
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('event_category');
        $this->_sTableData = Phpfox::getT('event_category_data');
        $this->_sModule = 'event';
        $this->_sLanguageId = Phpfox::getLanguageId();
        parent::__construct();
    }

    /**
     * @param null|int $iCategoryId
     *
     * @return array
     */
    public function getForBrowse($iCategoryId = null)
    {
        $sCacheId = $this->cache()->set('event_category_browse' . ($iCategoryId === null ? '' : '_' . md5($iCategoryId)) . '_' . $this->_sLanguageId);
        $this->cache()->group('event_category',$sCacheId);
        if (!($aCategories = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time')))) {
            $aCategories = $this->database()->select('mc.category_id, mc.name')
                ->from($this->_sTable, 'mc')
                ->where('mc.parent_id = ' . ($iCategoryId === null ? '0' : (int)$iCategoryId) . ' AND mc.is_active = 1')
                ->order('mc.ordering ASC')
                ->execute('getSlaveRows');

            foreach ($aCategories as $iKey => $aCategory) {
                $aCategories[$iKey]['url'] = Phpfox::permalink('event.category', $aCategory['category_id'],
                    Phpfox::getSoftPhrase($aCategory['name']));

                $aCategories[$iKey]['sub'] = $this->database()->select('mc.category_id, mc.name')
                    ->from($this->_sTable, 'mc')
                    ->where('mc.parent_id = ' . $aCategory['category_id'] . ' AND mc.is_active = 1')
                    ->order('mc.ordering ASC')
                    ->execute('getSlaveRows');

                foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory) {
                    $aCategories[$iKey]['sub'][$iSubKey]['url'] = Phpfox::permalink('event.category',
                        $aSubCategory['category_id'], $aSubCategory['name']);
                }
            }

            $this->cache()->save($sCacheId, $aCategories);
        }

        return $aCategories;
    }

    /**
     * @param string $sDisplay
     *
     * @return $this
     */
    public function display($sDisplay)
    {
        $this->_sDisplay = $sDisplay;
        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        $sCacheId = $this->cache()->set('event_category_display_' . $this->_sDisplay . '_' . $this->_sLanguageId);
        $this->cache()->group('event_category',$sCacheId);
        if ($this->_sDisplay == 'admincp') {
            if (!($sOutput = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time')))) {
                $sOutput = $this->_get(0, 1);
                $this->cache()->save($sCacheId, $sOutput);
            }
            return $sOutput;
        } else {
            if (!($this->_sOutput = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time')))) {
                $this->_get(0, 1);
                $this->cache()->save($sCacheId, $this->_sOutput);
            }
            return $this->_sOutput;
        }
    }

    /**
     * @param int $iParentId
     * @param null|int $iActive
     *
     * @return null|string
     */
    private function _get($iParentId, $iActive = null)
    {
        $aCategories = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int)$iParentId . ' AND is_active = ' . (int)$iActive . '')
            ->order('ordering ASC')
            ->execute('getSlaveRows');

        if (count($aCategories)) {
            $aCache = [];

            if ($iParentId != 0) {
                $this->_iCnt++;
            }
            $sOutput = '';
            if ($this->_sDisplay == 'option') {
            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput = '<ul>';
            } else {
                $this->_sOutput .= '<div class="js_mp_parent_holder" id="js_mp_holder_' . $iParentId . '" ' . ($iParentId > 0 ? ' style="display:none; padding:5px 0px 0px 0px;"' : '') . '>';
                $this->_sOutput .= '<select class="form-control js_mp_category_list" name="val[category][]" class="js_mp_category_list" id="js_mp_id_' . $iParentId . '">' . "\n";
                $this->_sOutput .= '<option value="">' . ($iParentId === 0 ? _p('select') : _p('select_a_sub_category')) . ':</option>' . "\n";
            }

            foreach ($aCategories as $iKey => $aCategory) {
                $aCache[] = $aCategory['category_id'];

                if ($this->_sDisplay == 'option') {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . ($this->_iCnt > 0 ? str_repeat('&nbsp;',
                                ($this->_iCnt * 2)) . ' ' : '') . Phpfox_Locale::instance()
                            ->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</option>' . "\n";
                    $this->_sOutput .= $this->_get($aCategory['category_id'], $iActive);
                } elseif ($this->_sDisplay == 'admincp') {
                    $sOutput .= '<li><img src="' . Phpfox_Template::instance()
                            ->getStyle('image',
                                'misc/draggable.png') . '" alt="" /> <input type="hidden" name="order[' . $aCategory['category_id'] . ']" value="' . $aCategory['ordering'] . '" class="js_mp_order" /><a href="#?id=' . $aCategory['category_id'] . '" class="js_drop_down">' . Phpfox_Locale::instance()
                            ->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</a>' . $this->_get($aCategory['category_id'],
                            $iActive) . '</li>' . "\n";
                } else {
                    $this->_sOutput .= '<option value="' . $aCategory['category_id'] . '" id="js_mp_category_item_' . $aCategory['category_id'] . '">' . Phpfox_Locale::instance()
                            ->convert(Phpfox::getSoftPhrase($aCategory['name'])) . '</option>' . "\n";
                }
            }

            if ($this->_sDisplay == 'option') {

            } elseif ($this->_sDisplay == 'admincp') {
                $sOutput .= '</ul>';

                return $sOutput;
            } else {
                $this->_sOutput .= '</select>' . "\n";
                $this->_sOutput .= '</div>';

                foreach ($aCache as $iCateoryId) {
                    $this->_get($iCateoryId, $iActive);
                }
            }

            $this->_iCnt = 0;
        }
        return null;
    }

    /**
     * @param string $sCategory
     *
     * @return array
     */
    public function getParentBreadcrumb($sCategory)
    {
        $sCacheId = $this->cache()->set('event_parent_breadcrumb_' . md5($sCategory) . '_' . $this->_sLanguageId);
        $this->cache()->group('event_category',$sCacheId);
        if (!($aBreadcrumb = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time')))) {
            $sCategories = $this->getParentCategories($sCategory);
            $aCategories = $this->database()
                ->select('*')
                ->from($this->_sTable)
                ->where('category_id IN(' . $sCategories . ')')
                ->execute('getSlaveRows');
            $aBreadcrumb = $this->getCategoriesById(null, $aCategories);
            $this->cache()->save($sCacheId, $aBreadcrumb);
        }

        return $aBreadcrumb;
    }

    /**
     * @param $iCategoryId
     * @return int
     */
    public function getParentCategoryId($iCategoryId)
    {
        $iParentCategoryId = Phpfox::getLib('database')->select('parent_id')
            ->from($this->_sTable)
            ->where('category_id=' . (int) $iCategoryId)
            ->executeField();
        return (int) $iParentCategoryId;
    }

    /**
     * @param string $sCategory
     *
     * @return string
     */
    public function getParentCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('event_category_parent_' . $sCategory . '_' . $this->_sLanguageId);
        $this->cache()->group('event_category',$sCacheId);
        if (!($sCategories = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time')))) {
            $iCategory = $this->database()
                ->select('category_id')
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
     * @param int $iId
     *
     * @return string
     */
    private function _getParentIds($iId)
    {
        $aCategories = $this->database()->select('pc.category_id, pc.parent_id')
            ->from($this->_sTable, 'pc')
            ->where('pc.category_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getParentIds($aCategory['parent_id']) . '';
        }

        return $sCategories;
    }

    /**
     * @param null|int $iId
     * @param null|array $aCategories
     *
     * @return array|null
     */
    public function getCategoriesById($iId = null, &$aCategories = null)
    {
        if ($aCategories === null) {
            $aCategories = $this->database()
                ->select('pc.parent_id, pc.category_id, pc.name')
                ->from(Phpfox::getT('event_category_data'), 'pcd')
                ->join($this->_sTable, 'pc', 'pc.category_id = pcd.category_id')
                ->where('pcd.event_id = ' . (int)$iId)
                ->order('pc.parent_id ASC, pc.ordering ASC')
                ->execute('getSlaveRows');
        }

        if (!count($aCategories)) {
            return null;
        }

        $aBreadcrumb = [];
        if (count($aCategories) > 1) {
            foreach ($aCategories as $aCategory) {
                $aBreadcrumb[] = [
                    Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aCategory['name'])),
                    Phpfox::permalink('event.category', $aCategory['category_id'],
                        Phpfox::getSoftPhrase($aCategory['name']))
                ];
            }
        } else {
            $aBreadcrumb[] = [
                Phpfox_Locale::instance()->convert($aCategories[0]['name']),
                Phpfox::permalink('event.category', $aCategories[0]['category_id'], $aCategories[0]['name'])
            ];
        }

        return $aBreadcrumb;
    }

    /**
     * @param int $iId
     *
     * @return string
     */
    public function getCategoryIds($iId)
    {
        $aCategories = $this->database()
            ->select('category_id')
            ->from(Phpfox::getT('event_category_data'))
            ->where('event_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        $aCache = [];
        foreach ($aCategories as $aCategory) {
            $aCache[] = $aCategory['category_id'];
        }

        return implode(',', $aCache);
    }

    /**
     * @param string $sCategory
     *
     * @return string
     */
    public function getAllCategories($sCategory)
    {
        $sCacheId = $this->cache()->set('event_category_childern_' . $sCategory . '_' . $this->_sLanguageId);
        $this->cache()->group('event_category',$sCacheId);
        if (!($sCategories = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time')))) {
            $iCategory = $this->database()
                ->select('category_id')
                ->from($this->_sTable)
                ->where('name_url = \'' . $this->database()->escape($sCategory) . '\'')
                ->execute('getSlaveField');

            $sCategories = $this->_getChildIds($sCategory, false);
            $sCategories = rtrim($iCategory . ',' . ltrim($sCategories, $iCategory . ','), ',');

            $this->cache()->save($sCacheId, $sCategories);
        }

        return $sCategories;
    }

    /**
     * @param int $iParentId
     * @param bool $bUseId
     *
     * @return string
     */
    private function _getChildIds($iParentId, $bUseId = true)
    {
        $aCategories = $this->database()->select('pc.name, pc.category_id')
            ->from($this->_sTable, 'pc')
            ->where(($bUseId ? 'pc.parent_id = ' . (int)$iParentId . '' : 'pc.name_url = \'' . $this->database()->escape($iParentId) . '\''))
            ->execute('getSlaveRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']) . '';
        }

        return $sCategories;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('event.service_category_category__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function getTotalSubBelongToCategory($iCategoryId)
    {
        return db()->select('COUNT(ec.category_id)')
            ->from($this->_sTable, 'ec')
            ->where('ec.parent_id = ' . $iCategoryId)
            ->execute('getSlaveField');
    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @param int $notInclude
     * @param int $isFirst
     * @return array|int|string
     */
    public function getForAdmin($iParentId = 0, $bGetSub = 1, $bCareActive = 0, $notInclude = 0, $isFirst = 1)
    {
        $sCacheId = '';
        if ($isFirst) {
            $hash = md5($iParentId === null ? '' : '_' . $iParentId) . (empty($bGetSub) ? '' : '_' . $bGetSub) . (empty($bCareActive) ? '' : '_' . $bCareActive) . (empty($notInclude) ? '' : '_' . $notInclude);
            $sCacheId = $this->cache()->set('event_category_admin_' . $hash . '_' . $this->_sLanguageId);
            $this->cache()->group('event_category',$sCacheId);
        }
        if (!($aRows = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time')))) {
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
            $aRows[$iKey]['url'] = Phpfox::permalink('event.category', $aCategory['category_id'],
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
            return db()->select('COUNT(Distinct ecd.event_id)')
                ->from($this->_sTableData, 'ecd')
                ->where('ecd.category_id IN (' . $sChildIds . ')')
                ->execute('getSlaveField');
        } else {
            return db()->select('COUNT(Distinct ecd.event_id)')
                ->from($this->_sTableData, 'ecd')
                ->where('ecd.category_id = ' . $iCategoryId)
                ->execute('getSlaveField');
        }

    }

    /**
     * @param int $iId
     *
     * @return string
     */
    public function getChildIds($iId)
    {
        return rtrim($this->_getChildIds($iId), ',');
    }

    /**
     * @param int $iParentId
     * @param bool $bPassName
     *
     * @return mixed|string
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
            $sCacheId = $this->cache()->set('event_category_url' . ($bPassName ? '_name' : '') . '_' . $iParentId . '_' . $this->_sLanguageId);
            $this->cache()->group('event_category',$sCacheId);
            if ($sParents = $this->cache()->get($sCacheId, Phpfox::getParam('event.categories_cache_time'))) {
                return $sParents;
            }
        }

        // Get the menus based on the category ID
        $aParents = $this->database()
            ->select('category_id, name, name_url, parent_id')
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
}
<?php

namespace Apps\PHPfox_Videos\Service;

use Core;
use Phpfox;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Category extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('video_category');
    }

    /**
     * @param $iParentId
     * @return string
     */
    public function getChildIds($iParentId)
    {
        $sCategories = $this->_getChildIds($iParentId);
        $sCategories = trim($sCategories, ',');

        return $sCategories;
    }

    /**
     * @param $iParentId
     * @return string
     */
    private function _getChildIds($iParentId)
    {
        $aCategories = db()->select('vc.category_id')
            ->from($this->_sTable, 'vc')
            ->where('vc.parent_id = ' . (int)$iParentId)
            ->execute('getRows');

        $sCategories = '';
        foreach ($aCategories as $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',' . $this->_getChildIds($aCategory['category_id']);
        }

        return $sCategories;
    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @param int $notInclude
     * @param int $isFirst
     * @param int $iCacheTime
     * @return array|int|string
     */
    public function getForAdmin(
        $iParentId = 0,
        $bGetSub = 1,
        $bCareActive = 0,
        $notInclude = 0,
        $isFirst = 1,
        $iCacheTime = 5
    ) {
        if ($isFirst) {
            $hash = md5($iParentId === null ? '' : '_' . $iParentId) . (empty($bGetSub) ? '' : '_' . $bGetSub) . (empty($bCareActive) ? '' : '_' . $bCareActive) . (empty($notInclude) ? '' : '_' . $notInclude);
            $sCacheId = $this->cache()->set('video_category_' . $hash . '_' . Phpfox::getLanguageId());
            Phpfox::getLib('cache')->group('video', $sCacheId);
        }
        if (!isset($sCacheId) || !($aRows = $this->cache()->get($sCacheId, $iCacheTime))) {
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
            if ($isFirst && isset($sCacheId)) {
                $this->cache()->save($sCacheId, $aRows);
            }
        }

        if (is_array($aRows)) {
            foreach ($aRows as $iKey => $aCategory) {
                $aRows[$iKey]['name'] = Phpfox::getSoftPhrase($aCategory['name']);
                $aRows[$iKey]['url'] = Phpfox::permalink('video.category', $aCategory['category_id'],
                    Phpfox::getSoftPhrase($aCategory['name']));
                $aRows[$iKey]['used'] = $this->getTotalItemBelongToCategory($aCategory['category_id'], 1);
            }
        }

        return $aRows;
    }

    /**
     * @param int $iParentId
     * @param int $bGetSub
     * @param int $bCareActive
     * @param int $iCacheTime
     * @return array|int|string
     */
    public function getForUsers($iParentId = 0, $bGetSub = 1, $bCareActive = 1, $iCacheTime = 5)
    {
        return $this->getForAdmin($iParentId, $bGetSub, $bCareActive, 0, 1, $iCacheTime);
    }

    /**
     * @param $iId
     * @return array|bool|int|string
     */
    public function getForEdit($iId)
    {
        $aRow = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aRow['category_id'])) {
            return false;
        }
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [],
                $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }

        return $aRow;
    }

    /**
     * @param $iCategoryId
     * @return array|bool|int|string
     */
    public function getCategory($iCategoryId)
    {
        $aCategory = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id = ' . (int)$iCategoryId)
            ->execute('getSlaveRow');

        return (isset($aCategory['category_id']) ? $aCategory : false);
    }

    /**
     * @param $iVideoId
     * @param string $sOrder
     * @return array|int|string
     */
    public function getHtmlCategoryString($iVideoId, $sOrder = 'parent_id ASC')
    {
        $aCategories = db()->select('vc.*')
            ->from(Phpfox::getT('video_category_data'), 'vcd')
            ->join($this->_sTable, 'vc', 'vc.category_id = vcd.category_id')
            ->where('vcd.video_id = ' . (int)$iVideoId . ' AND vc.is_active = 1')
            ->order($sOrder)
            ->execute('getSlaveRows');
        foreach ($aCategories as $iKey => $aCategory) {
            $aParentCat = $this->getCategory($aCategory['parent_id']);
            if ($aParentCat && !$aParentCat['is_active']) {
                unset($aCategories[$iKey]);
            }
        }

        return implode(', ', array_map(function ($aCategory) {
            return strtr('<a href=":link">:text</a>', [
                ':text' => \Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aCategory['name'])),
                ':link' => Phpfox::permalink('video.category', $aCategory['category_id'],
                    Phpfox::getSoftPhrase($aCategory['name']))
            ]);
        }, $aCategories));
    }

    /**
     * @param $iVideoId
     * @return array|int|string
     */
    public function getStringCategoryByVideoId($iVideoId)
    {
        return db()->select('GROUP_CONCAT(category_id)')
            ->from(Phpfox::getT('video_category_data'))
            ->where('video_id = ' . $iVideoId)
            ->execute('getSlaveField');
    }

    /**
     * @param $iVideoId
     * @return array
     */
    public function getCategoriesByVideoId($iVideoId)
    {
        $aItems = db()->select('d.video_id, d.category_id, c.name AS category_name')
            ->from(Phpfox::getT('video_category_data'), 'd')
            ->join(Phpfox::getT('video_category'), 'c', 'd.category_id = c.category_id')
            ->where("c.is_active = 1 AND d.video_id = " . $iVideoId)
            ->execute('getSlaveRows');

        return $aItems;
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

            return db()->select('COUNT(Distinct vcd.video_id)')
                ->from(Phpfox::getT('video_category_data'), 'vcd')
                ->where('vcd.category_id IN (' . $sChildIds . ')')
                ->execute('getSlaveField');
        } else {
            return db()->select('COUNT(Distinct vcd.video_id)')
                ->from(Phpfox::getT('video_category_data'), 'vcd')
                ->where('vcd.category_id = ' . $iCategoryId)
                ->execute('getSlaveField');
        }

    }

    /**
     * @param $iCategory
     * @return mixed|string
     */
    public function getStringParentCategories($iCategory)
    {
        $aCategory = $this->getCategory($iCategory);

        if (empty($aCategory['parent_id'])) {
            return $aCategory['category_id'];
        } else {
            return ($this->getStringParentCategories($aCategory['parent_id']) . ',' . $aCategory['category_id']);
        }
    }

    /**
     * @param $iCategory
     * @return array
     */
    public function getParentBreadcrumb($iCategory)
    {
        $sCategories = $this->getStringParentCategories($iCategory);
        $aCategories = db()
            ->select('*')
            ->from($this->_sTable)
            ->where('category_id IN(' . $sCategories . ')')
            ->execute('getSlaveRows');

        $aBreadcrumb = [];
        if (count($aCategories) > 1) {
            foreach ($aCategories as $aCategory) {
                $aBreadcrumb[] = [
                    \Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aCategory['name'])),
                    Phpfox::permalink('video.category', $aCategory['category_id'],
                        Phpfox::getSoftPhrase($aCategory['name']))
                ];
            }
        } else {
            $aBreadcrumb[] = [
                \Phpfox_Locale::instance()->convert(Phpfox::getSoftPhrase($aCategories[0]['name'])),
                Phpfox::permalink('video.category', $aCategories[0]['category_id'],
                    Phpfox::getSoftPhrase($aCategories[0]['name']))
            ];
        }

        return $aBreadcrumb;
    }

    public function getParentCategories($iCategory)
    {
        $sCategories = $this->getStringParentCategories($iCategory);
        $aCategories = db()
            ->select('*')
            ->from($this->_sTable)
            ->where('category_id IN(' . $sCategories . ')')
            ->execute('getSlaveRows');

        return $aCategories;
    }
}

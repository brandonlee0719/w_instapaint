<?php

namespace Apps\Core_eGifts\Service\Category;

use Phpfox_Service;
use Phpfox;

class Category extends Phpfox_Service
{
    protected $_sTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('egift_category');
    }

    /**
     * Getter function that returns a list of categories.
     *
     * @param bool $bGetItemBelong
     * @param bool $bFilter
     *
     * @return array
     */
    public function getCategories($bGetItemBelong = false, $bFilter = false)
    {
        if ($bGetItemBelong || $bFilter) {
            db()->select('COUNT(e.egift_id) as item_count, ')->leftJoin(':egift','e','e.category_id = ec.category_id');
        }
        $aCategories = db()
            ->select('ec.*')
            ->from($this->_sTable, 'ec')
            ->order('ec.ordering ASC')
            ->group('ec.category_id')
            ->execute('getSlaveRows');

        foreach ($aCategories as $key => &$aCat) {
            if ($bFilter && !$aCat['item_count']) {
                unset($aCategories[$key]);
                continue;
            }
            $aCat['name'] = Phpfox::getSoftPhrase($aCat['phrase']);

            $aCat['start_time'] = Phpfox::getLib('date')->convertFromGmt($aCat['time_start'], Phpfox::getTimeZone());
            $aCat['end_time'] = Phpfox::getLib('date')->convertFromGmt($aCat['time_end'], Phpfox::getTimeZone());

            $aCat['start_month'] = date('n', $aCat['start_time']);
            $aCat['start_day'] = date('j', $aCat['start_time']);
            $aCat['start_year'] = date('Y', $aCat['start_time']);

            $aCat['end_month'] = date('n', $aCat['end_time']);
            $aCat['end_day'] = date('j', $aCat['end_time']);
            $aCat['end_year'] = date('Y', $aCat['end_time']);
        }
        /* Now we make sure there is at least an empty string for the missing languages */
        return $aCategories;
    }

    /**
     * @param int $notInclude
     * @return array|int|string
     */
    public function getForAdmin($notInclude = 0)
    {
        $aRows = db()->select('ec.*, COUNT(e.egift_id) as used')
            ->from($this->_sTable,'ec')
            ->where('ec.category_id <> ' . $notInclude)
            ->leftJoin(':egift','e','e.category_id = ec.category_id')
            ->order('ec.ordering ASC')
            ->group('ec.category_id')
            ->execute('getSlaveRows');

        foreach ($aRows as &$aCategory) {
            $aCategory['name'] = Phpfox::getSoftPhrase($aCategory['phrase']);
        }
        return $aRows;
    }

    /**
     * Gets one single category
     *
     * @param int $iId
     *
     * @return array
     */
    public function getCategoryById($iId)
    {
        $aCategory = db()->select('ec.*, ec.phrase as name')
            ->from($this->_sTable, 'ec')
            ->where('ec.category_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!empty($aCategory['category_id'])) {
            if ($aCategory['time_start'] > 0) {
                $aCategory['start_day'] = date('d', $aCategory['time_start']);
                $aCategory['start_month'] = date('m', $aCategory['time_start']);
                $aCategory['start_year'] = date('Y', $aCategory['time_start']);

                $aCategory['end_day'] = date('d', $aCategory['time_end']);
                $aCategory['end_month'] = date('m', $aCategory['time_end']);
                $aCategory['end_year'] = date('Y', $aCategory['time_end']);

                $aCategory['do_schedule'] = true;
            }
        }
        return $aCategory;
    }

    public function getTotalItemBelongToCategory($iCategoryId)
    {
        return db()->select('COUNT(egift_id)')
            ->from(Phpfox::getT('egift'))
            ->where('category_id = ' . $iCategoryId)
            ->executeField();
    }
}

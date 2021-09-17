<?php

namespace Apps\Core_eGifts\Service\Category;

use Core_Service_Systems_Category_Process;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox;
use \Core\Lib as Lib;

class Process extends Core_Service_Systems_Category_Process
{
    const EGIFT_CATEGORY_DELETE_ALL_EGIFT = 1;
    const EGIFT_CATEGORY_DELETE_AND_MOVE_EGIFT_TO_ANOTHER = 3;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('egift_category');
        $this->_sModule = 'egift';
        parent::__construct();
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed`
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('egift.service_category_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * This function stores categories in the database and clears cache. It only stores language phrases so
     * if a category is going to be added it will create a language phrase for it.
     *
     * @param $aVals
     * @return int
     */
    public function addCategory($aVals)
    {
        $aInsert['phrase'] = $this->addPhrase($aVals);
        if (isset($aVals['do_schedule']) && $aVals['do_schedule'] == 1) {
            $aVals['start_time'] = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_month'],
                $aVals['start_day'], $aVals['start_year']);
            $aVals['end_time'] = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_month'],
                $aVals['end_day'], $aVals['end_year']);
            if ($aVals['start_time'] > $aVals['end_time']) {
                return Phpfox_Error::set(_p('schedule_availability_end_date_must_be_greater_than_or_equal_to_start_date'));
            }
            $aInsert['time_start'] = Phpfox::getLib('date')->convertToGmt($aVals['start_time']);
            $aInsert['time_end'] = Phpfox::getLib('date')->convertToGmt($aVals['end_time']);
        }

        $iCategoryId = db()->insert($this->_sTable, $aInsert);

        /* Delete cache */
        $this->cache()->remove();
        return $iCategoryId;
    }

    /**
     * This function stores categories in the database and clears cache. It only stores language phrases so
     * if a category is going to be added it will create a language phrase for it.
     *
     * @param $iEditId
     * @param $aVals
     * @return int
     */
    public function updateCategory($iEditId, $aVals)
    {
        $this->updatePhrase($aVals);
        if (isset($aVals['do_schedule']) && $aVals['do_schedule'] == 1) {
            $aVals['start_time'] = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_month'],
                $aVals['start_day'], $aVals['start_year']);
            $aVals['end_time'] = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_month'],
                $aVals['end_day'], $aVals['end_year']);
            if ($aVals['start_time'] > $aVals['end_time']) {
                return Phpfox_Error::set(_p('schedule_availability_end_date_must_be_greater_than_or_equal_to_start_date'));
            }
            $aUpdate['time_start'] = Phpfox::getLib('date')->convertToGmt($aVals['start_time']);
            $aUpdate['time_end'] = Phpfox::getLib('date')->convertToGmt($aVals['end_time']);
        } else {
            $aUpdate['time_start'] = null;
            $aUpdate['time_end'] = null;
        }

        db()->update($this->_sTable, $aUpdate, 'category_id = ' . $iEditId);

        /* Delete cache */
        $this->cache()->remove();
        return true;
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
            ->limit(1)
            ->execute('getSlaveRow');

        // Delete phrase of category
        if (isset($aCategory['phrase']) && Lib::phrase()->isPhrase($aCategory['phrase'])) {
            Phpfox::getService('language.phrase.process')->delete($aCategory['phrase'], true);
        }

        if ($aVals && isset($aVals['delete_type'])) {
            switch ($aVals['delete_type']) {
                case self::EGIFT_CATEGORY_DELETE_ALL_EGIFT:
                    $aItems = db()->select('eg.egift_id')
                        ->from(Phpfox::getT('egift'), 'eg')
                        ->where("eg.category_id = " . intval($iCategoryId))
                        ->execute('getSlaveRows');
                    foreach ($aItems as $aItem) {
                        $iGiftId = $aItem['egift_id'];
                        Phpfox::getService('egift.process')->deleteGift($iGiftId);
                    }
                    break;
                case self::EGIFT_CATEGORY_DELETE_AND_MOVE_EGIFT_TO_ANOTHER:
                    if (!empty($aVals['new_category_id'])) {
                        db()->update(Phpfox::getT('egift'),
                            array('category_id' => intval($aVals['new_category_id'])),
                            'category_id = ' . intval($iCategoryId));
                    }
                    break;
                default:
                    break;
            }
        }

        db()->delete($this->_sTable, 'category_id = ' . intval($iCategoryId));
        $this->cache()->remove();

        return true;
    }
}

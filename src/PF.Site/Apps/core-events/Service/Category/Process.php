<?php
namespace Apps\Core_Events\Service\Category;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class Process extends \Core_Service_Systems_Category_Process
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('event_category');
        $this->_sTableData = Phpfox::getT('event_category_data');
        $this->_sModule = 'event';
        $this->_sItemId = 'event_id';
        parent::__construct();
    }

    /**
     * @param array $aVals
     * @param string $sName
     *
     * @return bool
     */
    public function update($aVals, $sName = 'name')
    {
        $aCategory = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id =' . intval($aVals['edit_id']))
            ->execute('getRow');
        //Verify data
        if (!isset($aVals['parent_id'])) {
            $aVals['parent_id'] = 0;
        }
        if (!isset($aVals['edit_id'])) {
            return false;
        }

        if (isset($aVals[$sName]) && \Core\Lib::phrase()->isPhrase($aVals[$sName])) {
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
        ], 'category_id = ' . $aVals['edit_id']
        );
        //update category data when change parent category
        if (($aVals['parent_id'] != $aCategory['parent_id'])) {
            $aEvents = db()->select('d.event_id')
                ->from($this->_sTableData, 'd')
                ->where("d.category_id = " . $aVals['edit_id'])
                ->execute('getSlaveRows');
            if ($aCategory['parent_id'] > 0) {
                if ($aVals['parent_id'] == 0) {
                    foreach ($aEvents as $aEvent) {
                        db()->delete($this->_sTableData,
                            ['event_id' => $aEvent['event_id'], 'category_id' => $aCategory['parent_id']]);
                    }
                } else {
                    foreach ($aEvents as $aEvent) {
                        db()->update($this->_sTableData, ['category_id' => $aVals['parent_id']],
                            ['event_id' => $aEvent['event_id'], 'category_id' => $aCategory['parent_id']]);
                    }
                }
            } else {
                foreach ($aEvents as $aEvent) {
                    db()->insert($this->_sTableData,
                        ['event_id' => $aEvent['event_id'], 'category_id' => $aVals['parent_id']]);
                }
            }
        }
        // Remove from cache
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
            ->execute('getSlaveRow');

        // Delete phrase of category
        if (isset($aCategory['name']) && \Core\Lib::phrase()->isPhrase($aCategory['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }

        if ($aVals && isset($aVals['delete_type'])) {
            switch ($aVals['delete_type']) {
                case 1:
                    $aSubs = db()->select('ec.category_id')
                        ->from($this->_sTable, 'ec')
                        ->where('ec.parent_id = ' . intval($iCategoryId))
                        ->execute('getSlaveRows');
                    $sCategoryIds = $iCategoryId;
                    foreach ($aSubs as $key => $aSub) {
                        $sCategoryIds .= ',' . $aSub['category_id'];
                    }
                    $aItems = db()->select('d.event_id')
                        ->from($this->_sTableData, 'd')
                        ->where("d.category_id IN (" . $sCategoryIds . ')')
                        ->execute('getSlaveRows');
                    foreach ($aItems as $aItem) {
                        $iEventId = $aItem['event_id'];
                        Phpfox::getService('event.process')->delete($iEventId);
                    }
                    db()->delete($this->_sTable, 'parent_id = ' . intval($iCategoryId));
                    break;
                case 2:
                    if (!empty($aVals['new_category_id'])) {
                        $aItems = db()->select('d.event_id')
                            ->from($this->_sTableData, 'd')
                            ->where("d.category_id = " . intval($iCategoryId))
                            ->execute('getSlaveRows');
                        foreach ($aItems as $aItem) {
                            $iEventId = $aItem['event_id'];
                            db()->delete($this->_sTableData,
                                'category_id = ' . intval($aVals['new_category_id']) . ' AND event_id = ' . intval($iEventId));
                        }
                        db()->update($this->_sTableData,
                            array('category_id' => intval($aVals['new_category_id'])),
                            'category_id = ' . intval($iCategoryId));
                        db()->update($this->_sTable, array('parent_id' => $aVals['new_category_id']),
                            'parent_id = ' . intval($iCategoryId));
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
        if ($sPlugin = Phpfox_Plugin::get('event.service_category_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
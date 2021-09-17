<?php

namespace Apps\Core_RSS\Service\Group;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox_Service;

class Group extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('rss_group');
    }

    public function getForEdit($iId)
    {
        $aGroup = db()->select('rg.*')
            ->from($this->_sTable, 'rg')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = rg.module_id AND m.is_active = 1')
            ->join(Phpfox::getT('product'), 'p', 'p.product_id = rg.product_id AND p.is_active = 1')
            ->where('rg.group_id = ' . (int)$iId)
            ->executeRow();

        if (!isset($aGroup['group_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_group_you_are_planning_to_edit'));
        }

        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            $aGroup['name_var_' . $aLanguage['language_id']] = Phpfox::getSoftPhrase($aGroup['name_var'], [], false,
                null, $aLanguage['language_id']);
        }

        return $aGroup;
    }

    public function get()
    {
        return db()->select('rg.*')
            ->from($this->_sTable, 'rg')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = rg.module_id AND m.is_active = 1')
            ->join(Phpfox::getT('product'), 'p', 'p.product_id = rg.product_id AND p.is_active = 1')
            ->order('rg.ordering ASC')
            ->executeRows();
    }

    public function getDropDown()
    {
        return db()->select('rg.group_id, rg.name_var')
            ->from($this->_sTable, 'rg')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = rg.module_id AND m.is_active = 1')
            ->join(Phpfox::getT('product'), 'p', 'p.product_id = rg.product_id AND p.is_active = 1')
            ->where('rg.is_active = 1')
            ->order('rg.ordering ASC')
            ->executeRows();
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
        if ($sPlugin = Phpfox_Plugin::get('rss.service_group_group__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

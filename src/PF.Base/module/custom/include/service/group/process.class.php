<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Service_Group_Process
 */
class Custom_Service_Group_Process extends Phpfox_Service
{
    /**
     * @var string
     */
    protected $_sTable = '';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('custom_group');
    }

    /**
     * @param array $aVals
     *
     * @return bool|int
     */
    public function add($aVals)
    {
        Phpfox::getUserParam('custom.can_add_custom_fields_group', true);

        if (!isset($aVals['module_id'])) {
            return Phpfox_Error::set(_p('provide_a_module_for_this_group_to_belong_to'));
        }

        if (empty($aVals['type_id'])) {
            return Phpfox_Error::set(_p('select_where_this_custom_field_should_be_located'));
        }

        if (empty($aVals['module_id'])) {
            $aVals['module_id'] = 'core';
        }

        foreach ($aVals['group'] as $sPhrase) {
            if (empty($sPhrase)) {
                return Phpfox_Error::set(_p('provide_a_name_for_this_group'));
            }
            !isset($sVarName) && $sVarName = Phpfox::getService('language.phrase.process')->prepare($sPhrase);
        }

        if (empty($sVarName)) {
            return Phpfox_Error::set(_p('provide_a_name_for_this_group'));
        }

        $sVarName = 'custom_group_' . $sVarName;

        if ($this->database()
            ->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('phrase_var_name = \'' . $this->database()->escape($aVals['module_id'] . '.' . $sVarName) . '\'')
            ->execute('getSlaveField')
        ) {
            return Phpfox_Error::set(_p('there_is_already_a_group_with_the_same_name'));
        }

        $iId = $this->database()->insert($this->_sTable, [
            'module_id' => $aVals['module_id'],
            'product_id' => $aVals['product_id'],
            'user_group_id' => (int)(isset($aVals['user_group_id']) ? $aVals['user_group_id'] : 0),
            'type_id' => $aVals['type_id'],
            'phrase_var_name' => $aVals['module_id'] . '.' . $sVarName,
            'ordering' => 0
        ]);

        // Add the new phrase
        Phpfox::getService('language.phrase.process')->add([
            'var_name' => $sVarName,
            'text' => $aVals['group']
        ], true);

        return $iId;
    }

    /**
     * @param int $iId
     * @param array $aVals
     *
     * @return bool
     */
    public function update($iId, $aVals)
    {
        Phpfox::getUserParam('custom.can_manage_custom_fields', true);

        foreach ($aVals['group'] as $sKey => $aPhrases) {
            foreach ($aPhrases as $sLang => $sValue) {
                if (Phpfox::getService('language.phrase')->isValid($sKey, $sLang)) {
                    Phpfox::getService('language.phrase.process')->updateVarName($sLang, $sKey, $sValue);
                } else {
                    list($sModule, $sVarName) = explode('.', $sKey);

                    // Add the new phrase
                    Phpfox::getService('language.phrase.process')->add([
                        'var_name' => $sVarName,
                        'text' => [$sLang => $sValue]
                    ], true);
                }
            }
        }

        return true;
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function toggleActivity($iId)
    {
        Phpfox::getUserParam('custom.can_manage_custom_fields', true);

        $aField = $this->database()
            ->select('group_id, is_active')
            ->from($this->_sTable)
            ->where('group_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aField['group_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_custom_group'));
        }

        $this->database()
            ->update($this->_sTable, ['is_active' => ($aField['is_active'] ? 0 : 1)],
                'group_id = ' . $aField['group_id']);

        $this->cache()->removeGroup('custom');

        return true;
    }

    /**
     * @param array $aVals
     *
     * @return bool
     */
    public function updateOrder($aVals)
    {
        Phpfox::getUserParam('custom.can_manage_custom_fields', true);

        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, ['ordering' => (int)$iOrder], 'group_id = ' . (int)$iId);
        }

        $this->cache()->removeGroup('custom_field');

        return true;
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function delete($iId)
    {
        Phpfox::getUserParam('custom.can_manage_custom_fields', true);

        $aGroup = $this->database()
            ->select('*')
            ->from($this->_sTable)
            ->where('group_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aGroup['group_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_group_you_plan_on_deleting'));
        }

        list($sModule, $sPhrase) = explode('.', $aGroup['phrase_var_name']);
        //Todo check condition of this delete query again
        $this->database()->delete(Phpfox::getT('language_phrase'), 'var_name = \'' . $sPhrase . '\'');

        $this->database()->update(Phpfox::getT('custom_field'), array('group_id' => 0),
            'group_id = ' . $aGroup['group_id']);

        $this->database()->delete($this->_sTable, 'group_id = ' . $aGroup['group_id']);

        $this->cache()->removeGroup('custom_field');

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
        if ($sPlugin = Phpfox_Plugin::get('custom.service_group_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

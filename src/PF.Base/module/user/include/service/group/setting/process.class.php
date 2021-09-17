<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Group_Setting_Process
 */
class User_Service_Group_Setting_Process extends Phpfox_Service
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
        $this->_sTable = Phpfox::getT('user_group_setting');
    }

    /**
     * @param \Core\App\App $App
     * @return bool
     */
    public function importFromApp($App)
    {
        if (empty($App->user_group_settings)) {
            return true;
        }

        $aUserGroupSettings = json_decode(json_encode($App->user_group_settings), true);
        $aPhrases = [];
        $indexing = 0;
        $sAlias = $App->alias;
        $sAppId = $App->id;

        if (!$sAlias) {
            $sAlias = $App->id;
        }

        $aActualValues = [];

        foreach ($aUserGroupSettings as $sVarName => $aRow) {
            ++$indexing;
            $valueActual = isset($aRow['value']) ? $aRow['value'] : '0';
            $bIsHidden = isset($aRow['is_hidden']) ? ($aRow['is_hidden'] ? 1 : 0) : 0;
            $iOrdering = isset($aRow['ordering']) ? intval($aRow['ordering']) : $indexing;
            $sType = isset($aRow['type']) ? $aRow['type'] : 'input:text';
            $sOptions = serialize((isset($aRow['options']) and is_array($aRow['options'])) ? $aRow['options'] : []);

            if (!is_array($valueActual)) {
                $valueActual = [
                    1 => $valueActual,
                    2 => $valueActual,
                    3 => $valueActual,
                    4 => $valueActual,
                    5 => $valueActual,
                ];
            }
            $userGroupValues = [];
            $sLastValue = '';

            foreach ([ADMIN_USER_ID, NORMAL_USER_ID, GUEST_USER_ID, STAFF_USER_ID] as $iGroupId) {
                $temp = isset($valueActual[$iGroupId]) ? $valueActual[$iGroupId]
                    : $sLastValue;
                $userGroupValues[$iGroupId] = $sLastValue = is_array($temp) ? serialize($temp)
                    : (string)$temp;

            }

            $aVals = [
                'name' => $sVarName,
                'type_id' => $sType,
                'is_admin_setting' => isset($aRow['is_admin_setting']) ? ($aRow['is_admin_setting'] ? 1 : 0) : 0,
                'is_hidden' => $bIsHidden,
                'module_id' => $sAlias,
                'product_id' => $sAppId,
                'user_group' => $userGroupValues,
                'ordering' => $iOrdering,
                'option_values' => $sOptions,
            ];

            // since 4.5.3 there are 02 items with the same {name,module_id}, should delete one old.
            $check01 = $this->database()->select('*')
                ->from($this->_sTable)
                ->where(sprintf("name='%s' and module_id='%s' and product_id !='%s'", $aVals['name'], $sAlias, $sAppId))
                ->execute('getSlaveRow');

            $check02 = $this->database()->select('*')
                ->from($this->_sTable)
                ->where(sprintf("name='%s' and module_id='%s' and product_id ='%s'", $aVals['name'], $sAlias, $sAppId))
                ->execute('getSlaveRow');

            $bIsExists =  null;

            if($check01 and $check02){
                // clear old values.
                $this->database()->delete($this->_sTable, ['setting_id' => $check01['setting_id']]);
                $this->database()->delete(Phpfox::getT('user_setting'), ['setting_id' => $check01['setting_id']]);
                $bIsExists =  $check02;
            }elseif($check01){
                $bIsExists = $check01;
            }elseif($check02){
                $bIsExists =  $check02;
            }

            // how many duplicated items?

            // user group settings phrases
            if (isset($aRow['description'])) {
                $sPhraseValue = '<title>' . $aRow['info'] . '</title><info>' . $aRow['description'] . '</info>';
            } else {
                $sPhraseValue = $aRow['info'];
            }
            $aPhrases['user_setting_' . $sVarName] = $sPhraseValue;

            if ($bIsExists) {
                if ($bIsExists['type_id'] != $aVals['type_id']) {
                    $this->deleteOldSetting($aVals['name'], $aVals['module_id']);
                } elseif (isset($aVals['overwrite']) and $aVals['overwrite']) {
                    $this->deleteOldSetting($aVals['name'], $aVals['module_id']);
                }elseif($bIsExists['product_id'] != $aVals['product_id']){
                    $this->database()->update($this->_sTable,['product_id'=> $aVals['product_id']],'setting_id='. $bIsExists['setting_id']);
                    continue;
                }else{
                    continue;
                }
            }

            if(true){
                $bModuleSettingIsExists = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where([
                        'module_id' => $aVals['module_id'],
                        'name' => $aVals['name'],
                    ])->execute('getSlaveRow');

                if ($bModuleSettingIsExists) {
                    $aValues = [];
                    $aGroups = Phpfox::getService('user.group')->get();
                    foreach ($aGroups as $aGroup) {
                        $aValues[$aGroup['user_group_id']] = user($aVals['module_id'] . '.' . $aVals['name'], null,
                            $aGroup['user_group_id']);

                        if (is_bool($aValues[$aGroup['user_group_id']])) {
                            $aValues[$aGroup['user_group_id']] = (int)$aValues[$aGroup['user_group_id']];
                        }
                    }
                }
            }


            $iSettingId = $this->database()->insert($this->_sTable, array(
                    'module_id' => $aVals['module_id'],
                    'product_id' => $aVals['product_id'],
                    'name' => $aVals['name'],
                    'type_id' => $aVals['type_id'],
                    'default_admin' => $aVals['user_group'][ADMIN_USER_ID],
                    'default_user' => $aVals['user_group'][NORMAL_USER_ID],
                    'default_guest' => $aVals['user_group'][GUEST_USER_ID],
                    'default_staff' => $aVals['user_group'][STAFF_USER_ID],
                    'option_values' => $sOptions,
                )
            );

            if (!empty($bModuleSettingIsExists) && !empty($aValues)) {
                foreach ($aValues as $key => $value) {
                    $aActualValues[$key][$iSettingId] = $value;
                }
            }
        }

        if ($aActualValues) {
            foreach ($aActualValues as $iGroupId => $aValues) {
                $this->update($iGroupId, ['value_actual' => $aValues]);
            }
        }

        if (!empty($aPhrases)) {
            \Core\Lib::phrase()->addPhrase($aPhrases, null, false);
        }

        return true;
    }

    public function addSetting($aVals)
    {
        $aModules = explode('|', $aVals['module']);

        $aVals['name'] = strtolower(preg_replace("/\W/i", "_", $aVals['name']));

        // Look thru all the values in case we need to do some work
        foreach ($aVals['user_group'] as $iGroupId => $sValue) {
            // Switch thur all the types
            switch ($aVals['type']) {
                // Fix arrays
                case 'array':
                    // Make sure it is an array
                    if (preg_match("/^array\((.*)\);$/i", $sValue)) {
                        // Yes it is, lets serialize
                        $aVals['user_group'][$iGroupId] = serialize($sValue);
                    } else {
                        return Phpfox_Error::set(_p('not_valid_array'));
                    }
                    break;
            }
        }

        $bIsExists = $this->database()->select('*')
            ->from($this->_sTable)
            ->where([
                'module_id' => $aModules[0],
                'product_id' => $aVals['product_id'],
                'name' => $aVals['name'],
            ])->execute('getSlaveFields');

        if ($bIsExists) {
            if ($bIsExists['type_id'] != $aVals['type_id']) {
                $this->deleteOldSetting($aVals['name'], $aModules[0]);
            } elseif (isset($aVals['overwrite']) and $aVals['overwrite']) {
                $this->deleteOldSetting($aVals['name'], $aModules[0]);
            } else {
                return true;
            }
        }

        $this->database()->insert($this->_sTable, array(
                'module_id' => $aModules[0],
                'product_id' => $aVals['product_id'],
                'name' => $aVals['name'],
                'type_id' => $aVals['type'],
                'default_admin' => $aVals['user_group'][ADMIN_USER_ID],
                'default_user' => $aVals['user_group'][NORMAL_USER_ID],
                'default_guest' => $aVals['user_group'][GUEST_USER_ID],
                'default_staff' => $aVals['user_group'][STAFF_USER_ID]
            )
        );

        Phpfox::getService('language.phrase.process')->add(array(
                'var_name' => 'user_setting_' . $aVals['name'],
                'text' => $aVals['text']
            )
        );

        Phpfox::getLib('session')->set('cache_new_user_setting', $aModules[0] . '.' . $aVals['name']);

        return true;
    }

    public function deleteOldSetting($sName, $sModule)
    {
        $this->database()
            ->delete($this->_sTable, [
                'module_id' => $sModule,
                'name' => $sName
            ]);

        $this->database()
            ->delete(Phpfox::getT('user_group_custom'), [
                'module_id' => $sModule,
                'name' => $sName
            ]);
    }


    public function updateSetting($aVals)
    {
        $aModules = explode('|', $aVals['module']);

        $aVals['name'] = strtolower(preg_replace("/\W/i", "_", $aVals['name']));

        $this->database()->update($this->_sTable, array(
            'module_id' => $aModules[0],
            'product_id' => $aVals['product_id'],
            'name' => $aVals['name'],
            'type_id' => $aVals['type'],
            'default_admin' => $aVals['user_group'][ADMIN_USER_ID],
            'default_user' => $aVals['user_group'][NORMAL_USER_ID],
            'default_guest' => $aVals['user_group'][GUEST_USER_ID],
            'default_staff' => $aVals['user_group'][STAFF_USER_ID]
        ), 'setting_id = ' . (int)$aVals['setting_id']);

        if (Phpfox_Locale::instance()->isPhrase('admincp.user_setting_' . $aVals['name'])) {
            foreach ($aVals['text'] as $sLang => $sValue) {
                Phpfox::getService('language.phrase.process')->updateVarName($sLang,
                    'admincp.user_setting_' . $aVals['name'], $sValue);
            }
        } else {
            Phpfox::getService('language.phrase.process')->add([
                'var_name' => 'user_setting_' . $aVals['name'],
                'text' => $aVals['text']
            ]);
        }

        Phpfox::getLib('cache')->removeGroup('user_group_setting');

        return true;
    }

    /**
     * Updates the table phpfox_user_group_setting
     * @param int $iGroupId
     * @param array $aVals array(value_actual => array(setting_id => #))
     * @return true
     */
    public function update($iGroupId, $aVals)
    {
        if (isset($aVals['order'])) {
            foreach ($aVals['order'] as $iId => $iOrder) {
                $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'setting_id = ' . (int)$iId);
            }
        }
        $aSettings = array();
        $aRows = $this->database()->select('setting_id, type_id')
            ->from($this->_sTable)
            ->execute('getSlaveRows');
        foreach ($aRows as $aRow) {
            $aSettings[$aRow['setting_id']] = $aRow['type_id'];
        }

        $aSql = array();
        foreach ($aVals['value_actual'] as $iId => $sValue) {
            if (!isset($aSettings[$iId])) {
                continue;
            }
            // Check on callbacks to verify values
            if (isset($aVals['param']) && isset($aVals['param'][$iId])) {
                if (preg_match('/(?P<module>[a-z]+)\.(?P<variable>[a-z0-9\_\-]+)/i', $aVals['param'][$iId],
                        $aMatches) > 0 && isset($aMatches['module']) && isset($aMatches['variable']) && Phpfox::hasCallback($aMatches['module'],
                        'isValidUserGroupSetting')) {
                    $bValid = Phpfox::callback($aMatches['module'] . '.isValidUserGroupSetting',
                        array('user_group_id' => $iGroupId, 'variable' => $aMatches['variable'], 'value' => $sValue));
                    if ($bValid == false) {
                        Phpfox_Error::set('Invalid value "' . $sValue . '" for setting "' . $aMatches['module'] . '.' . $aMatches['variable'] . '"');
                        continue;
                    }
                }
            } else {
                $aSettingInfo = $this->database()->select('*')
                    ->from(':user_group_setting')
                    ->where('setting_id=' . (int)$iId)
                    ->execute('getSlaveRow');
                if (Phpfox::hasCallback($aSettingInfo['module_id'], 'isValidUserGroupSetting')) {
                    $bValid = Phpfox::callback($aSettingInfo['module_id'] . '.isValidUserGroupSetting',
                        array('user_group_id' => $iGroupId, 'variable' => $aSettingInfo['name'], 'value' => $sValue));
                    if ($bValid == false) {
                        Phpfox_Error::set('Invalid value "' . $sValue . '" for setting "' . $aSettingInfo['module_id'] . '.' . $aSettingInfo['name'] . '"');
                        continue;
                    }
                }
            }

            $this->database()->delete(Phpfox::getT('user_setting'),
                "user_group_id = " . (int)$iGroupId . " AND setting_id = " . (int)$iId);

            // Make sure the values are correct and if not fix them
            switch ($aSettings[$iId]) {
                case 'currency':
                case 'array':
                case 'multi_text':
                    if (empty($sValue)) {
                        $sValue = serialize([]);
                    } elseif (is_array($sValue)) {
                        $sValue = serialize($sValue);
                    } elseif (!is_array(@unserialize($sValue))) {
                        $aArrayParts = explode(',', $sValue);
                        $sNewValue = 'array(';
                        foreach ($aArrayParts as $sArrayPart) {
                            $sNewValue .= '\'' . trim($sArrayPart) . '\',';
                        }
                        $sValue = serialize(rtrim($sNewValue, ',') . ');');
                    }
                    break;
                case 'boolean':
                    if ($sValue != '1' && $sValue != '0') {
                        $sValue = '0';
                    }
                    break;
                case 'integer':
                    $sValue = strtolower($sValue);
                    if (!is_numeric($sValue) && $sValue != 'null') {
                        $sValue = 0;
                    }
                    break;
                case 'string' && !is_array($sValue):
                    $sValue = Phpfox::getLib('parse.input')->clean($sValue);
                    $sValue = Phpfox::getLib('parse.output')->shorten($sValue, 255);
                    break;
            }

            if (isset($aVals['sponsor_setting_id_' . $iId]) && $iId == $aVals['sponsor_setting_id_' . $iId]) {

                $iEmpty = 0;
                foreach ($aVals['value_actual'][$iId] as $sCurrency => $iValue) {
                    if (preg_match('/[^\d\.]/', $iValue)) {
                        return Phpfox_Error::set(_p('money_field_only_accepts_numbers_and_point'));
                    }
                    if (empty($iValue) && $iValue != 0) {
                        $iEmpty++;
                    }
                    if (substr_count($iValue, '.') > 1) {
                        return Phpfox_Error::set(_p('only_one_point_is_allowed'));
                    }
                }
                if ($iEmpty > 0 && count($aVals['value_actual'][$iId]) > $iEmpty) {
                    //Setting auto save, Admin can't fill all price field
                    return true;
                }
                $sValue = serialize($aVals['value_actual'][$iId]);
            }

            $aSql[] = array(
                $iGroupId,
                $iId,
                $sValue
            );

        }

        foreach ($aSql as $aRow) {
            $this->database()->delete(Phpfox::getT('user_setting'),
                'user_group_id = ' . $aRow[0] . ' AND setting_id = ' . $aRow[1]);
            $this->database()->insert(Phpfox::getT('user_setting'),
                array('user_group_id' => $aRow[0], 'setting_id' => $aRow[1], 'value_actual' => $aRow[2]));
        }

        if (!isset($aVals['bDontClearCache'])) {
            $this->cache()->remove('user_group_setting_' . $iGroupId);
        }

        return true;
    }

    public function import($aVals, $bMissingOnly = false)
    {
        $iProductId = Phpfox::getService('admincp.product')->getId($aVals['product']);
        if (!$iProductId) {
            $iProductId = 1;
        }

        if ($bMissingOnly) {
            $aCache = array();
            $aRows = $this->database()->select('name')
                ->from($this->_sTable)
                ->execute('getRows', array(
                    'free_result' => true
                ));
            foreach ($aRows as $aRow) {
                $aCache[] = $aRow['name'];
            }

            $aSql = array();
            foreach ($aVals['setting'] as $aVal) {
                if (!in_array($aVal['value'], $aCache)) {
                    $iModuleId = Phpfox_Module::instance()->getModuleId($aVal['module']);
                    $aSql[] = array(
                        $iModuleId,
                        $iProductId,
                        $aVal['value'],
                        $aVal['type'],
                        $aVal['admin'],
                        $aVal['user'],
                        $aVal['guest'],
                        $aVal['staff'],
                        $aVal['ordering']
                    );
                }
            }

            if ($aSql) {
                $this->database()->multiInsert($this->_sTable, array(
                    'module_id',
                    'product_id',
                    'name',
                    'type',
                    'default_admin',
                    'default_user',
                    'default_guest',
                    'default_staff',
                    'ordering'
                ), $aSql);

                Phpfox::getLib('cache')->removeGroup('user_group_setting');
            }
        } else {
            $aSql = array();
            foreach ($aVals['setting'] as $aVal) {
                $iModuleId = (int)Phpfox_Module::instance()->getModuleId($aVal['module']);
                $aSql[] = array(
                    $iModuleId,
                    $iProductId,
                    $aVal['value'],
                    $aVal['type_id'],
                    $aVal['admin'],
                    $aVal['user'],
                    $aVal['guest'],
                    $aVal['staff'],
                    $aVal['ordering']
                );
            }

            $this->database()->multiInsert($this->_sTable, array(
                'module_id',
                'product_id',
                'name',
                'type_id',
                'default_admin',
                'default_user',
                'default_guest',
                'default_staff',
                'ordering'
            ), $aSql);

        }

        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('user.service_group_setting_process__call')) {
            eval($sPlugin);

            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

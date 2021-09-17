<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Service_Custom
 */
class Custom_Service_Custom extends Phpfox_Service
{
    private $_sAlias = 'cf_';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('custom_field');
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->_sAlias;
    }

    /**
     * @param int $iUserId
     * @param string $sFieldName
     *
     * @return string
     */
    public function getUserCustomValue($iUserId, $sFieldName)
    {
        return $this->database()->select($sFieldName)
            ->from(Phpfox::getT('user_custom_value'))
            ->where('user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');
    }

    /**
     * This function returns the appropriate phrase for when a user updates their relationship status
     * by choosing the actual phrase (_new, _feed, ) and the replacements.
     * It is used in the callback custom.getActivityFeedRelation and
     *
     * `user_field`.`relation_with` == 1 ? `user_field`.`user_id` = `custom_relation_data`.`with_user_id`
     *
     * @param array $aRelation Can have a feed but always has the user whose profile we're looking at as user_id
     * @param array $aReplace
     * @param string $sPrevious
     *
     * @return bool
     */
    public function getRelationshipPhrase($aRelation, $aFeed = [], $aReplace = [], $sPrevious = '')
    {
        $bMustComplete = false;
        if (!isset($aRelation['with_user_id'])) {
            $bMustComplete = true;
            if (!isset($aRelation['relation_data_id'])) {
                return Phpfox_Error::set(_p('a_relation_does_not_have_relation_data_id_or_with_user_id'));
            }

            $sCacheId = $this->cache()->set(array(
                'relations',
                "user_" . $aRelation['user_id'] . "_relation_" . $aRelation['relation_data_id']
            ));

            if (!($aUser = $this->cache()->get($sCacheId))) {
                // We need to figure out the other user
                if ($aRelation['relation_with'] == 1) {
                    // $aRelation has the With user and we need to get the first user
                    $aUser = $this->database()->select('cr.relation_id, crd.relation_data_id, crd.status_id, u.user_name, u.full_name, u.user_id, cr.phrase_var_name, cr.confirmation')
                        ->from(Phpfox::getT('custom_relation_data'), 'crd')
                        ->join(Phpfox::getT('custom_relation'), 'cr', 'cr.relation_id = crd.relation_id')
                        ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = crd.user_id')
                        ->where('crd.relation_data_id = ' . $aRelation['relation_data_id'] . ' ')
                        ->execute('getSlaveRow');
                } else {
                    // $aRelation has the first user and we need to get the With user
                    $aUser = $this->database()->select('cr.relation_id, crd.relation_data_id,crd.status_id, u.user_name, u.full_name, u.user_id, cr.phrase_var_name, cr.confirmation')
                        ->from(Phpfox::getT('custom_relation_data'), 'crd')
                        ->join(Phpfox::getT('custom_relation'), 'cr', 'cr.relation_id = crd.relation_id')
                        ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = crd.with_user_id')
                        ->where('crd.relation_data_id = ' . $aRelation['relation_data_id'] . ' ')
                        ->execute('getSlaveRow');
                }

                $this->cache()->save($sCacheId, $aUser);
                Phpfox::getLib('cache')->group(  'relation', $sCacheId);
            }

            if (isset($aRelation['is_header']) && $aRelation['is_header'] == true
                && (
                    (!isset($aUser['user_id']) || empty($aUser['user_id']))
                    || ($aUser['status_id'] != 2)
                    || ($aUser['confirmation'] != 1)
                )
                || empty($aUser)
            ) {
                return false;
            }

            if (isset($aUser['status_id']) && $aUser['status_id'] == 2) { // only show the other user if this relation has been confirmed
                $aRelation['with_user_name'] = $aUser['user_name'];
                $aRelation['with_full_name'] = $aUser['full_name'];
                $aRelation['with_user_id'] = $aUser['user_id'];
                $aRelation['status_id'] = 2;
            }
            $aRelation['phrase_var_name'] = $aUser['phrase_var_name'];
        } else {
            $aReplace['with_user_name'] = $aRelation['with_user_name'];
            $aReplace['with_full_name'] = $aRelation['with_full_name'];
            $aReplace['with_user_id'] = $aRelation['with_user_id'];
        }

        if (!isset($aReplace['with_full_name'])) {
            $aLastRelation = Phpfox::getService('custom.relation')->getLatestForUser($aRelation['user_id'],
                $aRelation['relation_data_id']);

            if (isset($aLastRelation['with_user']['full_name'])) {
                $aReplace['with_full_name'] = $aLastRelation['with_user']['full_name'];
            }
            if (isset($aLastRelation['with_user']['user_name'])) {
                $aReplace['with_user_name'] = $aLastRelation['with_user']['user_name'];
            }
            if (isset($aLastRelation['with_user']['user_id'])) {
                $aReplace['with_user_id'] = $aLastRelation['with_user']['user_id'];
            }
        }

        if (isset($aRelation['full_name'])) {
            $aReplace['full_name'] = $aRelation['full_name'];
        }
        if (isset($aRelation['user_name'])) {
            $aReplace['user_name'] = $aRelation['user_name'];
        }

        if (isset($aFeed['user_id']) && $aRelation['with_user_id'] == $aFeed['user_id']) {
            $aReplace['with_user_name'] = '<a href="' . Phpfox_Url::instance()->makeUrl($aRelation['user_name']) . '">' . $aRelation['user_name'] . '</a>';
            $aReplace['with_full_name'] = '<a href="' . Phpfox_Url::instance()->makeUrl($aRelation['user_name']) . '">' . $aRelation['full_name'] . '</a>';
        } else {
            if (isset($aRelation['with_user_name'])) {
                $aReplace['with_user_name'] = '<a href="' . Phpfox_Url::instance()->makeUrl($aRelation['with_user_name']) . '">' . $aRelation['with_user_name'] . '</a>';
                $aReplace['with_full_name'] = '<a href="' . Phpfox_Url::instance()->makeUrl($aRelation['with_user_name']) . '">' . $aRelation['with_full_name'] . '</a>';
            }
        }
        /* To show the phrase we have to know if the user is coming from a relationship or not */
        if (!empty($sPrevious) && $sPrevious != 'custom.custom_relation_blank'
            && $aRelation['phrase_var_name'] == 'custom.custom_relation_blank') {
            /* user is removing the previous relation */
            $sPhrase = 'custom.user_is_no_longer_listed_as';
            $aReplace['previous'] = _p($sPrevious, array('previous' => $sPrevious));
        } elseif ($aRelation['status_id'] == 2 && $aRelation['with_user_id'] == 0 && $sPrevious != '' && $sPrevious != 'custom.custom_relation_blank') {
            /* User went from one relationship to another */
            $sPhrase = 'custom.relation_went_from_old_status_to_new_status';
        } elseif ($aRelation['status_id'] == 1 && $aRelation['user_id'] != $aRelation['with_user_id']) {
            /* user is in a relation that has not been confirmed yet */
            $sPhrase = $aRelation['phrase_var_name'] . '_new';
        } elseif ($aRelation['status_id'] == 2 && (isset($aRelation['with_user_id']) && $aRelation['with_user_id'] != 0 || isset($aRelplace['with_user_id']) && $aReplace['with_user_id'] != 0)) {
            /* User is in a confirmed relation */
            $sPhrase = $aRelation['phrase_var_name'] . '_with';
        } else {
            if (!isset($aRelation['bRelationshipHeader']) || $aRelation['bRelationshipHeader'] != true) {
                $sPhrase = $aRelation['phrase_var_name'] . '_new';
            } else {
                $sPhrase = $aRelation['phrase_var_name'];
            }
        }

        $sPhraseOut = _p($sPhrase, $aReplace);
        if (strpos($sPhraseOut, '{with_full_name}') !== false) {
            $sPhrase = $aRelation['phrase_var_name'];
        }

        $sOut = _p($sPhrase, $aReplace);
        if (isset($aRelation['user_id']) && $aRelation['user_id'] == Phpfox::getUserId()) {
            $sOut = str_replace(Phpfox::getUserBy('full_name'), '', $sOut);
        }

        return $sOut;
    }

    /**
     * @param array $aParams
     *
     * @return array
     */
    public function searchFields($aParams)
    {
        $sCacheId = $this->cache()->set('custom_field_search');

        if (!($aFields = $this->cache()->get($sCacheId))) {
            $aRows = $this->database()->select('field_id, field_name, var_type')
                ->from(Phpfox::getT('custom_field'))
                ->execute('getSlaveRows');
            foreach ($aRows as $aRow) {
                $aFields[$aRow['field_id']] = $aRow;
            }
            $this->cache()->save($sCacheId, $aFields);
            Phpfox::getLib('cache')->group(  'custom', $sCacheId);
        }

        $aConds = array();
        foreach ($aParams as $iKey => $mValue) {
            if (!isset($aFields[$iKey])) {
                continue;
            }

            if ($aFields[$iKey]['var_type'] == 'multiselect'
                || $aFields[$iKey]['var_type'] == 'checkbox'
            ) {
                $mTest = array_shift($mValue);
                if ($mTest == 0 || !is_numeric($mTest)) // "any"
                {
                    continue;//with any, can search user don't set this value
                } else {
                    $mValue[] = $mTest;
                }

                foreach ($mValue as $iOptionId) {
                    $aConds[] = [
                        'table' => Phpfox::getT('user_custom_multiple_value'),
                        'alias' => 'mvc',
                        'on' => 'mvc.user_id = u.user_id',
                        'where' => 'mvc.option_id = ' . (int)$iOptionId
                    ];
                }
            } else {
                if ($aFields[$iKey]['var_type'] == 'select'
                    || $aFields[$iKey]['var_type'] == 'radio') {
                    $aConds[] = array(
                        'table' => Phpfox::getT('user_custom_multiple_value'),
                        'alias' => 'mvc',
                        'on' => 'mvc.user_id = u.user_id',
                        'where' => 'mvc.option_id = ' . (int)$mValue
                    );
                } else {
                    $sAlias = 'ucv' . rand(1, 10000);
                    $aConds[] = array(
                        'table' => Phpfox::getT('user_custom'),
                        'alias' => $sAlias,
                        'on' => $sAlias . '.user_id = u.user_id',
                        'where' => $sAlias . '.cf_' . $aFields[$iKey]['field_name'] . ' LIKE \'%' . $this->database()->escape($mValue) . '%\''
                    );
                }
            }
        }

        return $aConds;
    }

    /**
     * @param string $sType
     * @param int $iUserGroup
     * @param boolean $bCheckField
     * @return mixed
     */
    public function getForPublic($sType, $iUserGroup = 0, $bCheckField = false)
    {
        $aCustom = [];
        $bFieldExist = false;
        $aGroups = $this->database()->select('group_id, phrase_var_name')
            ->from(Phpfox::getT('custom_group'))
            ->where('type_id = \'' . $this->database()->escape($sType) . '\' ' . ($iUserGroup > 0 ? 'AND user_group_id = ' . (int)$iUserGroup : '') . ' AND is_active = 1')
            ->execute('getSlaveRows');

        foreach ($aGroups as $aGroup) {
            $aFields = $this->database()->select('field_id, field_name, phrase_var_name, var_type')
                ->from(Phpfox::getT('custom_field'))
                ->where('group_id = ' . $aGroup['group_id'] . ' AND is_active = 1 AND is_search = 1')
                ->execute('getSlaveRows');

            $aCustom[$aGroup['group_id']] = array(
                'phrase_var_name' => $aGroup['phrase_var_name'],
                'group_id' => $aGroup['group_id']
            );

            foreach ($aFields as $aField) {
                $bFieldExist = true;
                $aCustom[$aGroup['group_id']]['fields'][$aField['field_id']] = $aField;

                if ($aField['var_type'] == 'select' || $aField['var_type'] == 'radio'
                    || $aField['var_type'] == 'multiselect' || $aField['var_type'] == 'checkbox'
                ) {
                    $aCustom[$aGroup['group_id']]['fields'][$aField['field_id']]['options'] = $this->database()->select('option_id, phrase_var_name')
                        ->from(Phpfox::getT('custom_option'))
                        ->where('field_id = ' . $aField['field_id'])
                        ->order('option_id ASC')
                        ->execute('getSlaveRows');
                }
            }
        }
        return ($bCheckField) ? array($bFieldExist, $aCustom) : $aCustom;
    }

    /**
     * @static var array $aItemData
     *
     * @param string $sTypeId
     * @param int $iItemId most often (or always) its the user_id of the profile (can be != Phpfox::getUserId())
     * @param int $iUserGroupId
     *
     * @return array
     */
    public function getForDisplay($sTypeId, $iItemId, $iUserGroupId = null)
    {
        $sSuffix = '';
        if ($iUserGroupId !== null) {
            $sSuffix .= '_' . $iUserGroupId;
        }

        $sCacheId = $this->cache()->set('custom_field_' . $sTypeId . $sSuffix);

        if ((!($aFields = $this->cache()->get($sCacheId))) || (!is_array($aFields))) {
            $iInherit = $this->database()->select('inherit_id')->from(Phpfox::getT('user_group'))->where('user_group_id = ' . (int)$iUserGroupId)->execute('getSlaveField');

            if (empty($iUserGroupId) && !Phpfox::getService('user.group.setting')->getGroupParam(Phpfox::getUserBy('user_group_id'),
                    'custom.has_special_custom_fields')) {
                $aCustomGroups = $this->database()->select('cg.group_id')
                    ->from(Phpfox::getT('custom_group'), 'cg')
                    ->where('cg.user_group_id = 0 OR cg.user_group_id = ' . Phpfox::getUserBy('user_group_id'))
                    ->execute('getSlaveRows');

                $sCustomGroupIDs = null;
                foreach ($aCustomGroups as $aCustomGroup) {
                    $sCustomGroupIDs .= $aCustomGroup['group_id'] . ', ';
                }
                $sCustomGroupIDs = rtrim($sCustomGroupIDs, ', ');
            }

            $sWhere = 'cf.user_group_id = 0 OR cf.user_group_id = ' . (int)$iInherit . ' ' . ($iUserGroupId !== null ? 'OR cf.user_group_id = ' . (int)$iUserGroupId . '' : '') . ' AND cf.is_active = 1';

            if (isset($sCustomGroupIDs)) {
                $sWhere = 'cf.group_id IN (' . $sCustomGroupIDs . ') AND (' . $sWhere . ')';
            }

            $aRows = $this->database()->select('cf.*')
                ->from($this->_sTable, 'cf')
                ->where($sWhere)
                ->join(Phpfox::getT('module'), 'm', 'm.module_id = cf.module_id AND m.is_active = 1')
                ->order('cf.ordering ASC')
                ->execute('getSlaveRows');

            $aFields = array();
            foreach ($aRows as $aRow) {
                $aRow['value'] = '';
                $aFields[$this->_sAlias . $aRow['field_name']] = $aRow;
            }
            $sCacheId = $this->cache()->set('custom_field_' . $sTypeId . $sSuffix);
            Phpfox::getLib('cache')->group(  'custom', $sCacheId);
            $this->cache()->save($sCacheId, $aFields);
        }

        static $aItemData = array();

        if (!isset($aItemData[$iItemId])) {
            $aItemData[$iItemId] = array();
            if ($iUserGroupId !== null) {
                $sTable = Phpfox::getUserGroupParam($iUserGroupId, 'custom.custom_table_name');
                if (!empty($sTable)) {
                    $aItemData[$iItemId] = $this->database()->select('*')
                        ->from($sTable)
                        ->where('user_id = ' . (int)$iItemId)
                        ->execute('getSlaveRows');
                }
            }

            $sTable = Phpfox::getT('user_custom');

            $aTemp = $this->database()->select('*')
                ->from($sTable)
                ->where('user_id = ' . (int)$iItemId)
                ->execute('getSlaveRows');

            $aItemData[$iItemId] = array_merge($aItemData[$iItemId], $aTemp);

            if ($iUserGroupId !== null) {
                $sTable = Phpfox::getUserGroupParam($iUserGroupId, 'custom.custom_table_name');
            }

            foreach ($aFields as $sFieldName => $aRow) {
                $bValueFound = false;
                foreach ($aItemData[$iItemId] as $iKey => $aColumns) {
                    if (isset($aItemData[$iItemId][$iKey][$sFieldName])) {
                        $aFields[$sFieldName]['value'] = $aItemData[$iItemId][$iKey][$sFieldName];
                        $bValueFound = true;
                    }
                }

                switch ($aRow['var_type']) {
                    case 'radio':
                    case 'select':
                        $aFields[$sFieldName]['value'] = $this->database()->select('co.phrase_var_name')
                            ->from(Phpfox::getT('custom_option'), 'co')
                            ->join(Phpfox::getT('user_custom_multiple_value'), 'cmv', 'cmv.option_id = co.option_id')
                            ->where('cmv.field_id = ' . (int)$aFields[$sFieldName]['field_id'] . ' AND cmv.user_id = ' . (int)$iItemId)
                            ->execute('getSlaveField');
                        break;
                    case 'multiselect':
                    case 'checkbox':
                        $aOptions = $this->database()->select('co.phrase_var_name')
                            ->from(Phpfox::getT('custom_option'), 'co')
                            ->join(Phpfox::getT('user_custom_multiple_value'), 'cmv', 'cmv.option_id = co.option_id')
                            ->where('cmv.field_id = ' . (int)$aFields[$sFieldName]['field_id'] . ' AND cmv.user_id = ' . (int)$iItemId)
                            ->execute('getSlaveRows');

                        if (!is_array($aFields[$sFieldName]['value'])) {
                            $aFields[$sFieldName]['value'] = array();
                        }
                        foreach ($aOptions as $iKey => $aOption) {
                            $aFields[$sFieldName]['value'][] = $aOption['phrase_var_name'];
                        }
                        break;
                    case 'text':
                        $aFields[$sFieldName]['value'] = $this->database()->select($sFieldName)
                            ->from(Phpfox::getT('user_custom'))
                            ->where('user_id = ' . (int)$iItemId)
                            ->execute('getSlaveField');
                    default:
                }

            }
            $aItemData[$iItemId] = $aFields;
        }
        $aOut = array();
        foreach ($aItemData[$iItemId] as $sFieldName => $aField) {
            if ($aField['type_id'] == $sTypeId) {
                $aOut[$sFieldName] = $aField;
            }
        }
        return $aOut;
    }

    /**
     * This function retrieves additional info to display in profile.info
     *
     * @param int $iUserId
     *
     * @return array
     */
    public function getUserPanelForUser($iUserId)
    {

        // These are the fields to be displayed
        $aFields = $this->database()->select('cf.*, cmv.option_id as customValue, co.phrase_var_name as phrase_chosen')
            ->from($this->_sTable, 'cf')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = cf.module_id AND m.is_active = 1')
            ->join(Phpfox::getT('user_custom_multiple_value'), 'cmv', 'cmv.field_id = cf.field_id')
            ->join(Phpfox::getT('custom_option'), 'co', 'co.option_id = cmv.option_id')
            ->where('cf.type_id = "user_panel" AND cmv.user_id = ' . (int)$iUserId . ' AND cf.type_id = "user_main"')
            ->order('cf.ordering ASC')
            ->execute('getSlaveRows');
        return $aFields;
    }

    /**
     * @param int $iId
     * @param int $iItemId
     * @param int $iEditUserId
     *
     * @return bool|string
     */
    public function getFieldForEdit($iId, $iItemId, $iEditUserId)
    {
        $aField = $this->database()->select('cf.*')
            ->from($this->_sTable, 'cf')
            ->where('cf.field_id = ' . (int)$iId)
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = cf.module_id AND m.is_active = 1')
            ->execute('getSlaveRow');

        if (!isset($aField['field_id'])) {
            return Phpfox_Error::set(_p('not_a_valid_custom_field_to_edit'));
        }

        $bCanEdit = false;
        if ($iEditUserId == Phpfox::getUserId() || (Phpfox::getUserParam('custom.can_edit_other_custom_fields'))) {
            $bCanEdit = true;
        }

        if ($bCanEdit === false) {
            return Phpfox_Error::set(_p('you_do_not_have_permission_to_edit_this_field'));
        }

        $aUser = $this->database()->select('user_group_id')
            ->from(Phpfox::getT('user'))
            ->where('user_id = ' . $iItemId)
            ->execute('getSlaveRow');

        $sTable = 'user_custom';
        if ($aField['var_type'] == 'select' || $aField['var_type'] == 'radio') {
            $sUserValue = $this->database()->select('co.*')
                ->from(Phpfox::getT('custom_option'), 'co')
                ->join(Phpfox::getT('user_custom_multiple_value'), 'cmv', 'cmv.option_id = co.option_id')
                ->where('cmv.user_id = ' . (int)$iEditUserId . ' AND cmv.field_id = ' . (int)$iId)
                ->execute('getSlaveRow');
        } else {
            if ($aField['var_type'] != 'multiselect' && $aField['var_type'] != 'checkbox') {
                $sUserValue = $this->database()->select($this->_sAlias . $aField['field_name'])
                    ->from(Phpfox::getT($sTable))
                    ->where('user_id = ' . (int)$iItemId)
                    ->execute('getSlaveField');
            } else {
                $aUserValues = $this->database()->select('co.*')
                    ->from(Phpfox::getT('custom_option'), 'co')
                    ->join(Phpfox::getT('user_custom_multiple_value'), 'cmv', 'cmv.option_id = co.option_id')
                    ->where('cmv.user_id = ' . (int)$iEditUserId . ' AND cmv.field_id = ' . (int)$iId)
                    ->execute('getSlaveRows');
            }
        }

        $sContent = '';
        switch ($aField['var_type']) {
            case 'textarea':
                $sContent .= '<div class="form-group"><textarea class="form-control" cols="50" rows="8" name="custom_field_value" id="js_custom_field_post_' . $aField['field_id'] . '">' . Phpfox::getLib('parse.output')->ajax($sUserValue) . '</textarea></div>';
                $sContent .= '<div class="btn-wrapper"><input type="button" value="' . _p('update') . '" class="btn btn-primary btn-primary" onclick="if (function_exists(\'\' + Editor.sEditor + \'_wysiwyg_custom_field\')) { eval(\'\' + Editor.sEditor + \'_wysiwyg_custom_field();\'); } $(\'#js_custom_field_post_' . $aField['field_id'] . '\').ajaxCall(\'custom.update\', \'field_id=' . $aField['field_id'] . '&amp;item_id=' . $iItemId . '&amp;edit_user_id=' . $iEditUserId . '\');" /> <a class="btn btn-default" href="#" onclick="$(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custom_content\').show(); $(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custon_field\').hide(); return false;">' . _p('cancel') . '</a></div>';
                break;
            case 'text':
                $sContent .= '<div class="form-group"><input class="form-control" type="text" name="custom_field_value" id="js_custom_field_post_' . $aField['field_id'] . '" value="' . Phpfox::getLib('parse.output')->ajax(Phpfox::getLib('parse.output')->clean($sUserValue)) . '" maxlength="60"/></div>';
                $sContent .= '<div class="btn-wrapper"><input type="button" value="' . _p('update') . '" class="btn btn-primary" onclick="if (function_exists(\'\' + Editor.sEditor + \'_wysiwyg_custom_field\')) { eval(\'\' + Editor.sEditor + \'_wysiwyg_custom_field();\'); } $(\'#js_custom_field_post_' . $aField['field_id'] . '\').ajaxCall(\'custom.update\', \'field_id=' . $aField['field_id'] . '&amp;item_id=' . $iItemId . '&amp;edit_user_id=' . $iEditUserId . '\');" />  <a  class="btn btn-default" href="#" onclick="$(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custom_content\').show(); $(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custon_field\').hide(); return false;">' . _p('cancel') . '</a></div>';
                break;
            case 'select':
            case 'radio':
                $aOptions = $this->database()->select('option_id, field_id, phrase_var_name')
                    ->from(Phpfox::getT('custom_option'))
                    ->where('field_id = ' . $aField['field_id'])
                    ->execute('getSlaveRows');
                $sContent .= '<select class="form-control" name="custom_field_value" id="js_custom_field_post_' . $aField['field_id'] . '">';
                foreach ($aOptions as $aOption) {
                    $sContent .= '<option value="' . $aOption['option_id'] . '"' . ($sUserValue == $aOption['option_id'] ? ' selected="selected"' : '') . '>' . _p($aOption['phrase_var_name']) . '</option>';
                }
                $sContent .= '</select>';
                $sContent .= '<div class="btn-wrapper"><input type="button" value="' . _p('update') . '" class="btn btn-primary" onclick="$(\'#js_custom_field_post_' . $aField['field_id'] . '\').ajaxCall(\'custom.update\', \'field_id=' . $aField['field_id'] . '&amp;item_id=' . $iItemId . '&amp;edit_user_id=' . $iEditUserId . '\');" /> <a class="btn btn-default" href="#" onclick="$(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custom_content\').show(); $(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custon_field\').hide(); return false;">' . _p('cancel') . '</a></div>';
                break;
            case 'multiselect':
                $aOptions = $this->database()->select('option_id, field_id, phrase_var_name')
                    ->from(Phpfox::getT('custom_option'))
                    ->where('field_id = ' . $aField['field_id'])
                    ->group('option_id', true)
                    ->execute('getSlaveRows');
                $sContent .= '<select class="form-control" multiple name="custom_field_value" id="js_custom_field_post_' . $aField['field_id'] . '">';
                foreach ($aOptions as $iKey => $aOption) {
                    foreach ($aUserValues as $iOptionChosen) {
                        $aOptions[$iKey]['chosen'] = false;
                        if ($iOptionChosen['option_id'] == $aOption['option_id']) {
                            $aOptions[$iKey]['chosen'] = true;
                            break;
                        }
                    }
                }
                foreach ($aOptions as $aOption) {
                    $sContent .= '<option value="' . $aOption['option_id'] . '"' . ($aOption['chosen'] ? ' selected="selected"' : '') . '>' . _p($aOption['phrase_var_name']) . '</option>';
                }
                $sContent .= '</select>';

                $sContent .= '<div class="btn-wrapper">';
                $sContent .= '<input type="button" value="' . _p('update') . '" class="btn btn-primary"';
                $sContent .= 'onclick="var selectedNumbers = \'\';';
                $sContent .= '$(\'#js_custom_field_post_' . $aField['field_id'] . ' :selected\').each(function(i,selected){';
                $sContent .= 'selectedNumbers += \'&amp;custom_field_value[]=\'+$(selected).val();});';
                $sContent .= '$.ajaxCall(\'custom.update\', \'field_id=' . $aField['field_id'] . '&amp;item_id=' . $iItemId . '&amp;edit_user_id=' . $iEditUserId . '&amp;\' + selectedNumbers);" /> <a class="btn btn-default" href="#" onclick="$(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custom_content\').show(); $(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custon_field\').hide(); return false;">' . _p('cancel') . '</a></div>';
                break;
            case 'checkbox':
                $aOptions = $this->database()->select('option_id, field_id, phrase_var_name')
                    ->from(Phpfox::getT('custom_option'))
                    ->where('field_id = ' . $aField['field_id'])
                    ->execute('getSlaveRows');
                foreach ($aOptions as $iKey => $aOption) {
                    foreach ($aUserValues as $iOptionChosen) {
                        $aOptions[$iKey]['chosen'] = false;
                        if ($iOptionChosen['option_id'] == $aOption['option_id']) {
                            $aOptions[$iKey]['chosen'] = true;
                            break;
                        }
                    }
                }
                foreach ($aOptions as $aOption) {
                    $sContent .= '<div><input type="checkbox" name="custom_field_value" class="js_custom_field_post_' . $aField['field_id'] . '"' . ((isset($aOption['chosen']) && $aOption['chosen']) == true ? ' checked="checked"' : '') . ' value="' . $aOption['option_id'] . '"> ' . _p($aOption['phrase_var_name']) . '<br /></div>';
                }
                $sContent .= '<div class="btn-wrapper">';
                $sContent .= '<input type="button" value="' . _p('update') . '" class="btn btn-primary"';
                $sContent .= 'onclick="var selectedNumbers = \'\';';
                $sContent .= '$(\'.js_custom_field_post_' . $aField['field_id'] . ':checked\').each(function(i,selected){';
                $sContent .= 'selectedNumbers += \'&amp;custom_field_value[]=\'+$(selected).val();});';
                $sContent .= '$.ajaxCall(\'custom.update\', \'field_id=' . $aField['field_id'] . '&amp;item_id=' . $iItemId . '&amp;edit_user_id=' . $iEditUserId . '&amp;\' + selectedNumbers);" /> <a class="btn btn-default" href="#" onclick="$(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custom_content\').show(); $(this).parents(\'.js_custom_content_holder:first\').find(\'.js_custon_field\').hide(); return false;">' . _p('cancel') . '</a></div>';
                break;
            default:

                break;
        }

        return $sContent;
    }

    /**
     * @param array $aTypes
     * @param null|int $iItemId
     * @param null|int $iUserGroup
     * @param bool $bRegister
     * @param null|int $iUserId
     *
     * @return array
     */
    public function getForEdit($aTypes, $iItemId = null, $iUserGroup = null, $bRegister = false, $iUserId = null)
    {
        $iGroup = 0;

        if ($sPlugin = Phpfox_Plugin::get('custom.service_custom_getforedit_1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }
        $sTypes = '';
        foreach ($aTypes as $sType) {
            $sTypes .= '\'' . $sType . '\',';
        }
        $sTypes = rtrim($sTypes, ',');

        $aWhere = array('cf.type_id IN (' . $sTypes . ') AND cf.is_active = 1 AND cg.is_active = 1');

        if ($iUserGroup !== null && Phpfox::getUserGroupParam($iUserGroup, 'custom.has_special_custom_fields')) {
            // Need to get the inherit user group id
            $iInherit = $this->database()->select('inherit_id')->from(Phpfox::getT('user_group'))->where('user_group_id = ' . (int)$iUserGroup)->execute('getSlaveField');
            $aWhere[] = 'AND (cf.user_group_id = ' . (int)$iUserGroup . ' OR cf.user_group_id = ' . $iInherit . ' OR cf.user_group_id = 0)';
        } else {
            $aWhere[] = 'AND cf.user_group_id = ' . (int)$iGroup . '';
        }
        if ($bRegister == true) {
            $aWhere[] = 'AND cf.on_signup = 1';
        }

        if ($iUserId !== null) {
            $this->database()->leftJoin(Phpfox::getT('user_custom_multiple_value'), 'cmv',
                'cmv.field_id = cf.field_id AND cmv.user_id = ' . (int)$iUserId);
        } else {
            $this->database()->leftJoin(Phpfox::getT('custom_option'), 'cmv', 'cmv.field_id = cf.field_id');
        }

        $aRows = $this->database()->select('cf.*, cmv.option_id as customValue, cg.user_group_id as cg_user_group_id')
            ->from($this->_sTable, 'cf')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = cf.module_id AND m.is_active = 1')
            ->leftJoin(Phpfox::getT('custom_group'), 'cg', 'cg.group_id = cf.group_id')
            ->where($aWhere)
            ->order('cf.ordering ASC')
            ->execute('getSlaveRows');

        // we already have the values from the multiple selection table now we need to
        // glue them into the same field_id
        $aTemp = array();
        $aTexts = array();
        $aOptions = array();
        $aFields = array();

        if ($iUserId !== null) {
            $aTexts = $this->database()->select('*')
                ->from(Phpfox::getT('user_custom'))
                ->where('user_id = ' . (int)$iUserId)
                ->execute('getSlaveRow');
        } else {
            if ($iItemId !== null) {
                $aTexts = $this->database()->select('*')
                    ->from(Phpfox::getT('user_custom_value'))
                    ->where('user_id = ' . (int)$iItemId)
                    ->execute('getSlaveRow');
            }
        }

        foreach ($aRows as $iKey => $aRow) {
            if (!empty($aRow['cg_user_group_id']) && $aRow['cg_user_group_id'] != $iUserGroup) {
                unset($aRows[$iKey]);
                continue;
            }
            if (!isset($aTemp[$aRow['field_id']])) {
                $aTemp[$aRow['field_id']] = $aRow;
            }
            // merge duplicated fields (they have different customValue
            if (($aRow['var_type'] == 'multiselect' || $aRow['var_type'] == 'checkbox') && !is_array($aTemp[$aRow['field_id']]['customValue'])) {
                $aTemp[$aRow['field_id']]['customValue'] = array($aRow['customValue']);
            } elseif ($aRow['var_type'] == 'multiselect' || $aRow['var_type'] == 'checkbox') {
                $aTemp[$aRow['field_id']]['customValue'][] = $aRow['customValue'];
            } // if this is a textarea and we have the value then assign it
            else {
                if ($aRow['type_name'] == 'MEDIUMTEXT' && isset($aTexts['cf_' . $aRow['field_name']]) || (strpos($aRow['type_name'],
                            'VARCHAR') !== false && isset($aTexts['cf_' . $aRow['field_name']]))) {
                    $aRow['value'] = $aTexts['cf_' . $aRow['field_name']];
                    $aRow['value'] = str_replace('<br />', "\n", $aRow['value']);
                    $aTemp[$aRow['field_id']] = $aRow;
                }
            }

            if ($aRow['type_name'] != 'MEDIUMTEXT') {
                $aRow['value'] = $aRow['customValue'];
            }
            $aFields[$this->_sAlias . $aRow['field_name']] = $aTemp[$aRow['field_id']];
            if (
                $aRow['var_type'] == 'select'
                || $aRow['var_type'] == 'multiselect'
                || $aRow['var_type'] == 'radio'
                || $aRow['var_type'] == 'checkbox') {
                $aOptions[$aRow['field_id']] = $aRow['field_id'];
            }
        }

        // Match the text areas if we are searching for a specific user
        if (count($aOptions)) {
            $aOptionsRows = $this->database()->select('*')
                ->from(Phpfox::getT('custom_option'))
                ->where('field_id IN(' . implode(',', array_values($aOptions)) . ')')
                ->order('option_id ASC')
                ->execute('getSlaveRows');

            $aCacheOptions = array();
            foreach ($aOptionsRows as $aOptionsRow) {
                $aCacheOptions[$aOptionsRow['field_id']][$aOptionsRow['option_id']] = _p($aOptionsRow['phrase_var_name']);
            }
        }

        $oReq = Phpfox_Request::instance();
        $bIsRegistration = ($oReq->get('req1') == 'user' && $oReq->get('req2') == 'register') || ($oReq->get('req1') == 'core' && ($oReq->get('req2') == 'index-visitor' || $oReq->get('req2') == 'index-visitor-mobile')) || $oReq->get('req1') == '';

        foreach ($aFields as $sFieldKey => $aField) {
            if (!isset($aField['value'])) {
                $aField['value'] = '';
            }
            if ($aField['var_type'] == 'textarea') {
                continue;
            }

            if (!empty($aCacheOptions)) {
                foreach ($aCacheOptions as $iFieldId => $aValues) {
                    if ($iFieldId == $aField['field_id']) {
                        if (!isset($aFields[$sFieldKey]['options'])) {
                            $aFields[$sFieldKey]['options'] = array();
                        }
                        foreach ($aValues as $iOptionId => $sPhrase) {
                            $aTemp = array('value' => $sPhrase);
                            // check if this is a selected option
                            if (is_array($aField['customValue']) && !$bIsRegistration) {
                                foreach ($aField['customValue'] as $iOptionIdVal) {
                                    if ($iOptionIdVal == $iOptionId) {
                                        $aTemp['selected'] = true;
                                    }
                                }
                            } else {
                                if (!$bIsRegistration && !empty($aField['customValue']) && $iOptionId == $aField['customValue']) {
                                    $aTemp['selected'] = true;
                                }
                            }
                            $aFields[$sFieldKey]['options'][$iOptionId] = $aTemp;
                        }
                    }
                }
            }
        }

        return $aFields;
    }

    /**
     * @return array
     */
    public function getForListing()
    {
        $aFields = $this->database()
            ->select('cf.*, ug.title AS user_group_name')
            ->from($this->_sTable, 'cf')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = cf.module_id AND m.is_active = 1')
            ->leftJoin(Phpfox::getT('user_group'), 'ug', 'ug.user_group_id = cf.user_group_id')
            ->order('cf.ordering ASC')
            ->execute('getSlaveRows');

        $aCustomFields = [];
        foreach ($aFields as $aField) {
            $aCustomFields[$aField['group_id']][] = $aField;
        }

        $aGroups = $this->database()
            ->select('cg.*, ug.title AS user_group_name')
            ->from(Phpfox::getT('custom_group'), 'cg')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = cg.module_id AND m.is_active = 1')
            ->leftJoin(Phpfox::getT('user_group'), 'ug', 'ug.user_group_id = cg.user_group_id')
            ->order('cg.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aGroups as $iKey => $aGroup) {
            if (isset($aCustomFields[$aGroup['group_id']])) {
                $aGroups[$iKey]['child'] = $aCustomFields[$aGroup['group_id']];
            }
        }

        if (isset($aCustomFields[0])) {
            $aGroups['PHPFOX_EMPTY_GROUP']['child'] = $aCustomFields[0];
        }

        return $aGroups;
    }

    /**
     * @param int $iId
     *
     * @return array
     */
    public function getForCustomEdit($iId)
    {
        $aField = $this->database()->select('cf.*')
            ->from($this->_sTable, 'cf')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = cf.module_id AND m.is_active = 1')
            ->where('cf.field_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        list($sModule, $sVarName) = explode('.', $aField['phrase_var_name']);

        // Get the name of the field in every language
        $aPhrases = $this->database()->select('language_id, text')
            ->from(Phpfox::getT('language_phrase'))
            ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
            ->execute('getSlaveRows');

        foreach ($aPhrases as $aPhrase) {
            $aField['name'][$aField['phrase_var_name']][$aPhrase['language_id']] = $aPhrase['text'];
        }

        if ($aField['var_type'] == 'select' || $aField['var_type'] == 'multiselect'
            || $aField['var_type'] == 'radio' || $aField['var_type'] == 'checkbox') {
            $aOptions = $this->database()->select('option_id, field_id, phrase_var_name')
                ->from(Phpfox::getT('custom_option'))
                ->where('field_id = ' . $aField['field_id'])
                ->order('option_id ASC')
                ->execute('getSlaveRows');

            foreach ($aOptions as $iKey => $aOption) {
                list($sModule, $sVarName) = explode('.', $aOption['phrase_var_name']);

                $aPhrases = $this->database()->select('language_id, text, var_name')
                    ->from(Phpfox::getT('language_phrase'))
                    ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'' . ($aField['has_feed'] == 1 ? ' OR var_name = \'' . $this->database()->escape($sVarName) . '_feed\'' : ''))
                    ->execute('getSlaveRows');

                foreach ($aPhrases as $aPhrase) {
                    if (!isset($aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']])) {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']] = array();
                    }
                    if ((preg_match('/[.]*_feed/', $aPhrase['var_name'])) > 0) {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]['feed'] = $aPhrase['text'];
                    } else {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]['text'] = $aPhrase['text'];
                    }

                }
            }
        }
        return $aField;
    }

    /**
     * @param string $sProductId
     * @param null|string $sModule
     *
     * @return bool
     */
    public function export($sProductId, $sModule = null)
    {
        $oXmlBuilder = Phpfox::getLib('xml.builder');

        $aSql = array();
        $aSql[] = "cf.product_id = '" . $sProductId . "'";
        if ($sModule !== null) {
            $aSql[] = "AND cf.module_id = '" . $sModule . "'";
        }

        $aRows = $this->database()->select('cf.*, cg.phrase_var_name AS group_name')
            ->from($this->_sTable, 'cf')
            ->join(Phpfox::getT('module'), 'm', 'm.module_id = cf.module_id AND m.is_active = 1')
            ->leftJoin(Phpfox::getT('custom_group'), 'cg', 'cg.group_id = cf.group_id')
            ->where($aSql)
            ->execute('getSlaveRows');

        if (!count($aRows)) {
            return false;
        }

        if (count($aRows)) {
            $oXmlBuilder->addGroup('custom_field');

            foreach ($aRows as $aRow) {
                $aOptions = $this->database()->select('co.phrase_var_name')
                    ->from(Phpfox::getT('custom_option'), 'co')
                    ->where('co.field_id = ' . $aRow['field_id'])
                    ->execute('getSlaveRows');

                $oXmlBuilder->addTag('field', (count($aOptions) ? serialize($aOptions) : ''), array(
                        'group_name' => ($aRow['group_id'] ? $aRow['group_name'] : ''),
                        'field_name' => $aRow['field_name'],
                        'module_id' => $aRow['module_id'],
                        'type_id' => $aRow['type_id'],
                        'phrase_var_name' => $aRow['phrase_var_name'],
                        'type_name' => $aRow['type_name'],
                        'var_type' => $aRow['var_type'],
                        'is_active' => $aRow['is_active'],
                        'is_required' => $aRow['is_required'],
                        'ordering' => $aRow['ordering']
                    )
                );
            }
            $oXmlBuilder->closeGroup();
        }

        $aSql = array();
        $aSql[] = "cg.product_id = '" . $sProductId . "'";
        if ($sModule !== null) {
            $aSql[] = "AND cg.module_id = '" . $sModule . "'";
        }

        $aRows = $this->database()->select('cg.*')
            ->from(Phpfox::getT('custom_group'), 'cg')
            ->where($aSql)
            ->execute('getSlaveRows');

        if (count($aRows)) {
            $oXmlBuilder->addGroup('custom_group');

            foreach ($aRows as $aRow) {
                $oXmlBuilder->addTag('group', '', [
                    'module_id' => $aRow['module_id'],
                    'type_id' => $aRow['type_id'],
                    'phrase_var_name' => $aRow['phrase_var_name'],
                    'is_active' => $aRow['is_active'],
                    'ordering' => $aRow['ordering']
                ]);
            }
            $oXmlBuilder->closeGroup();
        }

        return true;
    }

    /**
     * Gets all the relationships
     * @return array
     */
    public function getRelations()
    {
        if (Phpfox::getParam('user.enable_relationship_status') != true) {
            return array();
        }
        $aReturn = $this->database()->select('cr.relation_id, cr.phrase_var_name')
            ->from(Phpfox::getT('custom_relation'), 'cr')
            ->execute('getSlaveRows');

        foreach ($aReturn as $iKey => $aRelation) {
            $aReturn[$iKey]['phrase'] = _p($aRelation['phrase_var_name']);
        }
        return $aReturn;
    }

    /**
     * @deprecated This function will be removed in 4.6.0
     * @param array $aItem
     *
     * @return array
     */
    public function getInfoForAction($aItem)
    {
        $aRow = $this->database()
            ->select('crd.relation_data_id, cr.phrase_var_name, crd.user_id, u.gender, u.user_name, u.full_name')
            ->from(Phpfox::getT('custom_relation_data'), 'crd')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = crd.user_id')
            ->join(Phpfox::getT('custom_relation'), 'cr', 'cr.relation_id = crd.relation_data_id')
            ->where('crd.relation_data_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');
        $aRow['link'] = Phpfox_Url::instance()->makeUrl('user_name');
        $aRow['title'] = _p($aRow['phrase_var_name']);
        return $aRow;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return  null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('custom.service_custom__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
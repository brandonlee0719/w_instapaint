<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Component_Ajax_Ajax
 */
class Custom_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function edit()
    {
        if (($sContent = Phpfox::getService('custom')->getFieldForEdit($this->get('field_id'), $this->get('item_id'),
            $this->get('edit_user_id')))) {
            $this->call('$(\'#js_custom_field_' . $this->get('field_id') . '\').html(\'' . str_replace(array(
                    "'",
                    '<br />'
                ), array("\'", "\n"), $sContent) . '\');')
                ->show('#js_custom_field_' . $this->get('field_id'));
            (($sPlugin = Phpfox_Plugin::get('custom.component_ajax_edit')) ? eval($sPlugin) : false);
        }
    }

    public function update()
    {
        $sContent = Phpfox::getService('custom.process')->updateField($this->get('field_id'), $this->get('item_id'),
            $this->get('edit_user_id'), $this->get('custom_field_value'));
        if ($sContent !== false) {
            $this->hide('#js_custom_field_' . $this->get('field_id'));
            if ($sContent === '') {
                $this->html('#js_custom_content_' . $this->get('field_id'),
                    'Nothing added yet. Click <a href="#" onclick=" $(\'#js_custom_content_' . $this->get('field_id') . '\').hide(); $(this).parent().removeClass(\'extra_info\'); $.ajaxCall(\'custom.edit\', \'field_id=' . $this->get('field_id') . '&amp;item_id=' . $this->get('item_id') . '&amp;edit_user_id=' . $this->get('edit_user_id') . '\'); return false;">here</a> to edit.');
            } else {
                $this->html('#js_custom_content_' . $this->get('field_id'), $sContent);
            }
            $this->show('#js_custom_content_' . $this->get('field_id'));
        }
    }

    public function addGroup()
    {
        if (($iId = Phpfox::getService('custom.group.process')->add($this->get('val'))) && ($aGroup = Phpfox::getService('custom.group')->getGroup($iId))) {
            $this->append('#js_group_listing',
                '<option value="' . $aGroup['group_id'] . '" selected="selected">' . _p($aGroup['phrase_var_name']) . '</option>')
                ->hide('#js_group_holder')
                ->show('#js_field_holder');
        }
    }

    public function toggleActiveGroup()
    {
        if (Phpfox::getService('custom.group.process')->toggleActivity($this->get('id'))) {
            $this->call('$Core.custom.toggleGroupActivity(' . $this->get('id') . ')');
        }
    }

    public function toggleActiveField()
    {
        if (Phpfox::getService('custom.process')->toggleActivity($this->get('id'))) {
            $this->call('$Core.custom.toggleFieldActivity(' . $this->get('id') . ')');
        }
    }

    public function deleteField()
    {
        if (Phpfox::getService('custom.process')->delete($this->get('id'))) {
            $this->call('$(\'#js_field_' . $this->get('id') . '\').parents(\'li:first\').remove();');
        }
    }

    public function deleteOption()
    {
        if (Phpfox::getService('custom.process')->deleteOption($this->get('id'))) {
            $this->call('$(\'#js_current_value_' . $this->get('id') . '\').remove();');
        } else {
            $this->alert(_p('could_not_delete'));
        }
    }

    public function updateFields()
    {
        define('NO_TWO_FEEDS_THIS_ACTION', true);
        $aVals = $this->get('custom');
        if (empty($aVals)) {
            $aVals = $this->get('val');
        }
        if (!(empty($aVals))) {
            $aCustomFields = Phpfox::getService('custom')->getForEdit(array('user_main', 'user_panel', 'profile_panel'),
                Phpfox::getUserId(), Phpfox::getUserBy('user_group_id'), false, Phpfox::getUserId());
            if (Phpfox::getUserParam('custom.can_edit_own_custom_field')) {
                foreach ($aCustomFields as $aCustomField) {
                    if (empty($aVals[$aCustomField['field_id']]) && $aCustomField['is_required']) {
                        Phpfox_Error::set(_p('the_field_field_is_required',
                            array('field' => _p($aCustomField['phrase_var_name']))));
                    } else {
                        if ((!isset($aVals[$aCustomField['field_id']]) || empty($aVals[$aCustomField['field_id']])) && !$aCustomField['is_required']) {
                            Phpfox::getService('custom.process')->updateField($aCustomField, Phpfox::getUserId(),
                                Phpfox::getUserId(), '');
                        }
                    }
                }
            }
            if ($sPlugin = Phpfox_Plugin::get('custom.component_ajax_updatefields__1')) {
                eval($sPlugin);
                if (isset($aPluginReturn)) {
                    return $aPluginReturn;
                }
            }
            if (Phpfox_Error::isPassed()) {
                $bReturnCustom = Phpfox::getService('custom.process')->updateFields(Phpfox::getUserId(),
                    Phpfox::getUserId(), $aVals);
                $aUser = $this->get('val');
                $aUser['language_id'] = Phpfox::getUserBy('language_id');
                define('PHPFOX_IS_CUSTOM_FIELD_UPDATE', true);

                if (Phpfox::getParam('user.require_basic_field')) {
                    $aUserFieldsRequired =
                        array(
                            'location' => array('user.location' => $aUser['country_iso']),
                            'day' => array('user.date_of_birth' => $aUser['day']),
                            'month' => array('user.date_of_birth' => $aUser['month']),
                            'year' => array('user.date_of_birth' => $aUser['year'])
                        );
                    if (Phpfox::getUserParam('user.can_edit_gender_setting')) {
                        $aUserFieldsRequired['gender'] = ['user.gender' => (isset($aUser['gender']) ? $aUser['gender'] : '')];
                    }
                    if (Phpfox::getUserParam('custom.can_edit_own_custom_field')) {
                        foreach ($aUserFieldsRequired as $aFieldRequired) {
                            foreach ($aFieldRequired as $sLangId => $mValue) {
                                if (empty($mValue) && !in_array(_p('the_field_field_is_required',
                                            array('field' => _p($sLangId))) . " ", Phpfox_Error::get())) {
                                    Phpfox_Error::set(_p('the_field_field_is_required',
                                            array('field' => _p($sLangId))) . " ");
                                }
                            }
                        }
                    }
                }
                $month = isset($aUser['month']) ? (int)$aUser['month'] : 0;
                $day = isset($aUser['day']) ? (int)$aUser['day'] : 0;
                $year = isset($aUser['year']) ? (int)$aUser['year'] : 0;
                if (($month || $day || $year) && (!$month || !$day || !$year)) {
                    Phpfox_Error::set(_p('not_a_valid_date'));
                }
                if ($month && $day && $year && !checkdate($month, $day, $year)) {
                    Phpfox_Error::set(_p('not_a_valid_date'));
                }
                $bReturnUser = false;
                if (Phpfox_Error::isPassed()) {
                    $bReturnUser = Phpfox::getService('user.process')->update(Phpfox::getUserId(), $aUser);
                }

                if ($bReturnCustom && $bReturnUser) {
                    $this->call('$(\'#public_message\').html(\'' . _p('profile_successfully_updated') . '\'); $Core.processingEnd(); $Core.loadInit();');
                    $this->call('$("#relation").val(' . $aUser['relation'] . ');');
                    $aRelateInfo = Phpfox::getService('user')->getUser($aUser['relation_with'], 'u.full_name');
                    $aRelateInfo['full_name'] = str_replace('&#039;', '\'', $aRelateInfo['full_name']);
                    if (!empty($aUser['relation_with']) && ($aUser['relation_with'] != Phpfox::getUserId())) {
                        $this->call('$("#sFriendInput").val("' . $aRelateInfo['full_name'] . '");');
                    }

                    return true;
                }
            }
            $this->call('$(\'#js_custom_submit_button\').attr(\'disabled\', false).removeClass(\'disabled\'); $Core.processingEnd();');
        }
        return null;
    }

    public function processRelationship()
    {
        Phpfox::isUser(true);

        $aRelationship = Phpfox::getService('custom.relation')->getDataById($this->get('relation_data_id'));

        if (isset($aRelationship['with_user_id']) && $aRelationship['with_user_id'] == Phpfox::getUserId()) {
            if ($this->get('type') == 'accept') {
                Phpfox::getService('custom.relation.process')->updateRelationship(0, $aRelationship['user_id'],
                    $aRelationship['with_user_id']);
                $this->remove('#drop_down_' . $this->get('request_id'));
            } else {
                Phpfox::getService('custom.relation.process')->denyStatus($this->get('relation_data_id'),
                    $aRelationship['with_user_id']);
                if (Phpfox::isModule('friend')) {
                    Phpfox::getService('friend.request.process')->delete($this->get('request_id'),
                        $aRelationship['user_id']);
                }
                $this->remove('#drop_down_' . $this->get('request_id'));
            }
        } else {
            if (empty($aRelationship)) {
                Phpfox::getService('custom.relation.process')->checkRequest($this->get('relation_data_id'));
                $this->remove('#drop_down_' . $this->get('request_id'));
            }
        }
    }
}

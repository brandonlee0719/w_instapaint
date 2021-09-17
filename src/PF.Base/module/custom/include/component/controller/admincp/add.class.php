<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Component_Controller_Admincp_Add
 */
class Custom_Component_Controller_Admincp_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bHideOptions = true;
        $iDefaultSelect = 4;
        $bIsEdit = false;

        if (($iEditId = $this->request()->getInt('id'))) {
            Phpfox::getUserParam('custom.can_manage_custom_fields', true);

            $aField = Phpfox::getService('custom')->getForCustomEdit($iEditId);
            if (isset($aField['field_id'])) {
                $bIsEdit = true;

                $this->template()->assign(array(
                        'aForms' => $aField
                    )
                );

                if (isset($aField['option']) && $aField['var_type'] == 'select') {
                    $bHideOptions = false;
                }
            }
        } else {
            Phpfox::getUserParam('custom.can_add_custom_fields', true);
            $this->template()->assign(array('aForms' => array()));
        }

        $aFieldValidation = array(
            'product_id' => _p('select_a_product_this_custom_field_will_belong_to'),
            'type_id' => _p('select_a_module_this_custom_field_will_belong_to'),
            'var_type' => _p('select_what_type_of_custom_field_this_is')
        );

        $oCustomValidator = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_custom_field',
                'aParams' => $aFieldValidation,
                'bParent' => true
            )
        );

        $this->template()->assign(array(
                'sCustomCreateJs' => $oCustomValidator->createJS(),
                'sCustomGetJsForm' => $oCustomValidator->getJsForm()
            )
        );

        if (($aVals = $this->request()->getArray('val'))) {
            if ($oCustomValidator->isValid($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('custom.process')->update($aField['field_id'], $aVals)) {
                        $this->url()->send('admincp.custom.add', array('id' => $aField['field_id']),
                            _p('field_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('custom.process')->add($aVals)) {
                        $this->url()->send('admincp.custom.add', null, _p('field_successfully_added'));
                    }
                }
            }

            if (isset($aVals['var_type']) && $aVals['var_type'] == 'select') {
                $bHideOptions = false;
                $iCnt = 0;
                $sOptionPostJs = '';
                foreach ($aVals['option'] as $iKey => $aOptions) {
                    if (!$iKey) {
                        continue;
                    }

                    $aValues = array_values($aOptions);
                    if (!empty($aValues[0])) {
                        $iCnt++;
                    }

                    foreach ($aOptions as $sLang => $mValue) {
                        $sOptionPostJs .= 'option_' . $iKey . '_' . $sLang . ': \'' . str_replace("'", "\'",
                                $mValue['text']) . '\',';
                    }
                }
                $sOptionPostJs = rtrim($sOptionPostJs, ',');
                $iDefaultSelect = $iCnt;
            }
        }

        $aTypes = array();
        foreach (Phpfox::massCallback('getCustomFieldLocations') as $sModule => $aCustomFields) {
            foreach ($aCustomFields as $sKey => $sPhrase) {
                $aTypes[$sKey] = $sPhrase;
            }
        }

        $aGroupTypes = array();
        foreach (Phpfox::massCallback('getCustomGroups') as $sModule => $aCustomGroups) {
            foreach ($aCustomGroups as $sKey => $sPhrase) {
                $aGroupTypes[$sKey] = $sPhrase;
            }
        }

        $aGroupValidation = [
            'product_id' => _p('select_a_product_this_custom_field_will_belong_to'),
            'module_id' => _p('select_a_module_this_custom_field_will_belong_to'),
            'type_id' => _p('select_where_this_custom_field_should_be_located')
        ];

        $oGroupValidator = Phpfox_Validator::instance()->set([
            'sFormName' => 'js_group_field',
            'aParams' => $aGroupValidation,
            'bParent' => true
        ]);

        $this->template()->assign([
            'sGroupCreateJs' => $oGroupValidator->createJS(),
            'sGroupGetJsForm' => $oGroupValidator->getJsForm(false)
        ]);

        $aUserGroups = Phpfox::getService('user.group')->get();
        foreach ($aUserGroups as $iKey => $aUserGroup) {
            if (!Phpfox::getUserGroupParam($aUserGroup['user_group_id'], 'custom.has_special_custom_fields')) {
                unset($aUserGroups[$iKey]);
            }
        }
        // only show the input if there are custom fields
        $this->template()->assign(array('bShowUserGroups' => (count($aUserGroups) > 0)));

        $aGroups = Phpfox::getService('custom.group')->get();
        foreach ($aGroups as &$aGroup) {
            $sPharse = _p($aGroup['phrase_var_name']);
            if (strlen($sPharse) > 30) {
                $aGroup['phrase_var_name'] = substr($sPharse, 0, 30);
                $aGroup['phrase_var_name'] .= '...';
            } else {
                $aGroup['phrase_var_name'] = $sPharse;
            }
        }

        $this->template()
            ->setSectionTitle(_p('custom_fields'))
            ->setBreadCrumb(_p('Members'),'#')
            ->setBreadCrumb(_p('custom_fields'),$this->url()->makeUrl('admincp.custom'))
            ->setTitle(_p('add_a_new_custom_field'))
            ->setBreadCrumb($bIsEdit ? _p('Edit Custom Field') : _p('add_a_new_custom_field'), $this->url()->current())
            ->setPhrase(array(
                    'are_you_sure_you_want_to_delete_this_custom_option',
                    'set_to_active'
                )
            )
            ->setHeader(array(
                    '<script type="text/javascript"> var bIsEdit = ' . ($bIsEdit ? 'true' : 'false') . '</script>',
                    'admin.js' => 'module_custom',
                    '<script type="text/javascript">$Behavior.custom_admin_add_init = function(){$Core.custom.init(' . ($bIsEdit == true ? 1 : $iDefaultSelect) . '' . (isset($sOptionPostJs) ? ', {' . $sOptionPostJs . '}' : '') . ');};</script>'
                )
            )
            ->setActiveMenu('admincp.member.custom')
            ->assign(array(
                    'aTypes' => $aTypes,
                    'aLanguages' => Phpfox::getService('language')->getAll(),
                    'aGroupTypes' => $aGroupTypes,
                    'aGroups' => $aGroups,
                    'bHideOptions' => $bHideOptions,
                    'bIsEdit' => $bIsEdit,
                    'aUserGroups' => $aUserGroups
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('custom.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}

<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Component_Controller_Admincp_Group_Add
 */
class Custom_Component_Controller_Admincp_Group_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;

        if (($iEditId = $this->request()->getInt('id'))) {
            Phpfox::getUserParam('custom.can_manage_custom_fields', true);

            if (($aGroup = Phpfox::getService('custom.group')->getForEdit($iEditId)) && isset($aGroup['group_id'])) {
                $bIsEdit = true;
                $this->template()->assign(array(
                        'aForms' => $aGroup
                    )
                );
            }
        } else {
            Phpfox::getUserParam('custom.can_add_custom_fields_group', true);
        }

        $aGroupValidation = array(
            'product_id' => _p('select_a_product_this_custom_field_will_belong_to'),
            'module_id' => _p('select_a_module_this_custom_field_will_belong_to'),
            'type_id' => _p('select_where_this_custom_field_should_be_located')
        );

        $oGroupValidator = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_group_field',
                'aParams' => $aGroupValidation,
                'bParent' => true
            )
        );

        $aGroupTypes = array();
        foreach (Phpfox::massCallback('getCustomGroups') as $sModule => $aCustomGroups) {
            foreach ($aCustomGroups as $sKey => $sPhrase) {
                $aGroupTypes[$sKey] = $sPhrase;
            }
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($oGroupValidator->isValid($aVals)) {
                if ($bIsEdit === true) {
                    if (Phpfox::getService('custom.group.process')->update($aGroup['group_id'], $aVals)) {
                        $this->url()->send('admincp.custom.group.add', array('id' => $aGroup['group_id']),
                            _p('group_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('custom.group.process')->add($aVals)) {
                        $this->url()->send('admincp.custom.group.add', null, _p('group_successfully_added'));
                    }
                }
            }
        }

        $aUserGroups = Phpfox::getService('user.group')->get();
        foreach ($aUserGroups as $iKey => $aUserGroup) {
            if (!Phpfox::getUserGroupParam($aUserGroup['user_group_id'], 'custom.has_special_custom_fields')) {
                unset($aUserGroups[$iKey]);
            }
        }

        $this->template()->setTitle(_p('add_a_new_custom_group'))
            ->setBreadCrumb(_p('add_a_new_custom_group'))
            ->setActiveMenu('admincp.member.custom')
            ->assign(array(
                    'sGroupCreateJs' => $oGroupValidator->createJS(),
                    'sGroupGetJsForm' => $oGroupValidator->getJsForm(),
                    'aGroupTypes' => $aGroupTypes,
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
        (($sPlugin = Phpfox_Plugin::get('custom.component_controller_admincp_group_add_clean')) ? eval($sPlugin) : false);
    }
}

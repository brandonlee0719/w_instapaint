<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Component_Controller_Admincp_Index
 */
class Custom_Component_Controller_Admincp_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('custom.can_manage_custom_fields', true);
        $bOrderUpdated = false;

        if (($iDeleteId = $this->request()->getInt('delete')) && Phpfox::getService('custom.group.process')->delete($iDeleteId)) {
            $this->url()->send('admincp.custom', null, _p('custom_group_successfully_deleted'));
        }

        if (($aFieldOrders = $this->request()->getArray('field')) && Phpfox::getService('custom.process')->updateOrder($aFieldOrders)) {
            $bOrderUpdated = true;
        }

        if (($aGroupOrders = $this->request()->getArray('group')) && Phpfox::getService('custom.group.process')->updateOrder($aGroupOrders)) {
            $bOrderUpdated = true;
        }

        if ($bOrderUpdated === true) {
            $this->url()->send('admincp.custom', null, _p('custom_fields_successfully_updated'));
        }
        if (Phpfox::getUserParam('custom.can_add_custom_fields')) {
            $this->template()->setActionMenu([
                _p('create_a_custom_field') => [
                    'url' => $this->url()->makeUrl('admincp.custom.add'),
                    'class' => '_popup'
                ]
            ]);
        }
        $this->template()
            ->setSectionTitle(_p('custom_fields'))
            ->setTitle(_p('manage_custom_fields'))
            ->setBreadCrumb(_p('Members'),'#')
            ->setBreadCrumb(_p('manage_custom_fields'))
            ->setPhrase(array(
                    'are_you_sure_you_want_to_delete_this_custom_option',
                    'set_to_active'
                )
            )
            ->setActiveMenu('admincp.member.custom')
            ->setHeader(array(
                    'admin.js' => 'module_custom',
                    '<script type="text/javascript">$Behavior.custom_set_url = function() { $Core.custom.url(\'' . $this->url()->makeUrl('admincp.custom') . '\'); };</script>',
                    'jquery/ui.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.custom_admin_addSort = function(){$Core.custom.addSort();};</script>'
                )
            )
            ->assign(array(
                    'aGroups' => Phpfox::getService('custom')->getForListing()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('custom.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}

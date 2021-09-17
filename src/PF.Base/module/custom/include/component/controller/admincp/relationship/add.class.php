<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Custom_Component_Controller_Admincp_Relationship_Add
 */
class Custom_Component_Controller_Admincp_Relationship_Add extends Phpfox_Component
{
    public function process()
    {
        $this->template()
            ->setBreadCrumb(_p('Members'),'#')
            ->setBreadCrumb(_p('relationship_statues'),$this->url()->makeUrl('admincp.relationships'))
            ->setBreadCrumb(_p('Add Relationship'))
            ->setActiveMenu('admincp.member.relationships')
            ->setActionMenu([
                _p('relationship_statues') => $this->url()->makeUrl('admincp.custom.relationships')
            ]);
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('custom.component_controller_admincp_relationships_add_clean')) ? eval($sPlugin) : false);
    }
}

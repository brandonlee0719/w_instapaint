<?php

namespace Apps\PHPfox_Groups\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class IntegrateController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        if ($aVals = $this->request()->get('val')) {
            if (storage()->get('groups_integrate')) {
                storage()->del('groups_integrate');
            }
            storage()->set('groups_integrate', $aVals);
            \Phpfox_Cache::instance()->remove();
        }

        $aModules = Phpfox::massCallback('getGroupPerms');
        unset($aModules['groups']);
        unset($aModules['shoutbox']);

        if ($values = storage()->get('groups_integrate')) {
            $values = (array)$values->value;
        }

        foreach ($aModules as $sModuleId => $value) {
            $aModule = \Phpfox_Module::instance()->get($sModuleId);
            if ($aModule['phrase_var_name'] == 'module_apps') {
                $aModules[$sModuleId]['title'] = _p('module_' . $aModule['module_id']);
            } else {
                $aModules[$sModuleId]['title'] = _p($aModule['phrase_var_name']);
            }
            if (isset($values) && array_key_exists($sModuleId, $values)) {
                $aModules[$sModuleId]['value'] = $values[$sModuleId];
            } else {
                $aModules[$sModuleId]['value'] = 1;
            }
        }

        $this->template()->assign([
            'aModules' => $aModules
        ])->setBreadCrumb(_p('manage_integrated_items'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_admincp_integrate_clean')) ? eval($sPlugin) : false);
    }
}

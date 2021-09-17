<?php

namespace Apps\Core_Pages\Controller\Admin;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class IntegrateController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        if ($aVals = $this->request()->get('val')) {
            if (storage()->get('pages_integrate')) {
                storage()->del('pages_integrate');
            }
            storage()->set('pages_integrate', $aVals);
            \Phpfox_Cache::instance()->remove();
        }

        $aModules = Phpfox::massCallback('getPagePerms');
        unset($aModules['pages']);
        unset($aModules['shoutbox']);

        if ($values = storage()->get('pages_integrate')) {
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
}

<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @author Neil <neil@phpfox.com>
 * Class Admincp_Component_Controller_App_Settings
 */
class Admincp_Component_Controller_App_Settings extends Phpfox_Component
{
    public function process()
    {
        $sAppId = $this->request()->get('id');
        if (empty($sAppId)) {
            $sAppId = Phpfox::isAppAlias($this->request()->get('req2'), true);
        }

        $App = \Core\Lib::appInit($sAppId);
        $sModule = isset($App->alias) ? $App->alias : $App->id;
        Phpfox::getLib('request')->set('module-id', $sModule);
        Phpfox::getLib('module')->setController('admincp.setting.edit');

        return true;
    }
}

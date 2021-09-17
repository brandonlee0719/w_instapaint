<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AllController extends Phpfox_Component
{

    public function process()
    {
        $aUser = $this->getParam('aUser');
        if (empty($aUser)) {
            $this->url()->send('groups');
        }
        $sExtraConds = (Phpfox::getUserParam('core.can_view_private_items') || $aUser['user_id'] == Phpfox::getUserId()) ? "" : " AND (p.reg_method <> 2)";
        list($iTotal, $aGroups) = \Phpfox::getService('groups')->getForProfile($aUser['user_id'], 0, false, $sExtraConds);
        if (!$iTotal) {
            return false;
        }
        $this->template()->assign([
            'aGroupsList' => $aGroups,
        ]);

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_all_clean')) ? eval($sPlugin) : false);
    }
}

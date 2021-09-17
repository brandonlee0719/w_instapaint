<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Controller_Points
 */
class Profile_Component_Controller_Points extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $userName = Phpfox::getUserBy('user_name');
        //Check is admin active display points
        if (!Phpfox::getParam('user.no_show_activity_points')) {
            $this->url()->send($userName);
        }
        if ($userName != $this->request()->get('req1')) {
            $this->url()->send($userName);
        }
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
        $aModules = Phpfox::massCallback('getDashboardActivity');
        $aActivites = [
            _p('total_items') => $aUser['activity_total'],
            _p('activity_points') => $aUser['activity_points'] . (Phpfox::getParam('user.can_purchase_activity_points') ? '<span id="purchase_points_link">(<a href="#" onclick="$Core.box(\'user.purchasePoints\', 500); return false;">' . _p('purchase_points') . '</a>)</span>' : ''),
        ];
        foreach ($aModules as $aModule) {
            foreach ($aModule as $sPhrase => $sLink) {
                $aActivites[$sPhrase] = $sLink;
            }
        }
        $this->template()->setBreadCrumb(_p('activity_points'))->setTitle(_p('activity_points'))->assign(['aActivites' => $aActivites]);

        (($sPlugin = Phpfox_Plugin::get('profile.component_controller_points_process_end')) ? eval($sPlugin) : false);
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('profile.component_controller_points_clean')) ? eval($sPlugin) : false);
    }
}

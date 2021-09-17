<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_SKIP_POST_PROTECTION', true);

/**
 * Class Ad_Component_Controller_Manage
 */
class Ad_Component_Controller_Manage extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        $sView = $this->request()->get('view');

        $aCond = array();
        switch ($sView) {
            case 'pending':
                $aCond[] = 'AND a.is_custom = 2';
                break;
            case 'payment':
                $aCond[] = 'AND a.is_custom = 1';
                break;
            case 'denied':
                $aCond[] = 'AND a.is_custom = 4';
                break;
            default:
                $aCond[] = 'AND a.is_custom = 3';
                break;
        }
        $aCond[] = 'AND a.user_id = ' . Phpfox::getUserId();

        if (Phpfox::getParam('ad.multi_ad')) {
            $aCond[] = ' AND a.location = 50';
        }
        $aAds = Phpfox::getService('ad')->getForUser($aCond);

        Phpfox::getService('ad')->getSectionMenu();
        if (Phpfox::getUserParam('ad.can_create_ad_campaigns')) {
            sectionMenu(' ' . _p('create_an_ad'), url('/ad/add'));
        }

        $this->template()->setTitle(_p('ad_management'))
            ->setBreadCrumb(_p('advertise'), $this->url()->makeUrl('ad'))
            ->setBreadCrumb(_p('advertise'), $this->url()->makeUrl('ad.manage'), true)
            ->setHeader(array(
                    'table.css' => 'style_css',
                    'manage.js' => 'module_ad'
                )
            )
            ->assign(array(
                    'aAllAds' => $aAds,
                    'sView' => $sView,
                    'bNewPurchase' => $this->request()->get('payment')
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_controller_manage_clean')) ? eval($sPlugin) : false);
    }
}

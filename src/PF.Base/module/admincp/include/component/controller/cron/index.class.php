<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Cron_Index extends Phpfox_Component {
    public function process(){
        $this->template()->setTitle(_p("Cron Job"))
            ->setActiveMenu('admincp.settings.cron')
            ->setBreadCrumb(_p("Settings"), '#')
            ->setBreadCrumb(_p("Cron Job"), $this->url()->makeUrl('admincp.cron'))
            ->assign([
               'cron_dir' => dirname(PHPFOX_DIR) . PHPFOX_DS . 'cron.php'
            ]);
    }
}
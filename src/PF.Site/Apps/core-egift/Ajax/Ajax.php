<?php

namespace Apps\Core_eGifts\Ajax;

use Phpfox_Ajax;
use Phpfox;

class Ajax extends Phpfox_Ajax
{

    public function setOrder()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('egift.process')->setOrder($aVals['ordering']);
    }

    public function showEgifts()
    {
        $aAllParams = $this->getAll();
        $this->setTitle(_p('send_egift'));
        Phpfox::getBlock('egift.list-egifts', $aAllParams);
    }

    public function changeCategories()
    {
        $aAllParams = $this->getAll();
        Phpfox::getComponent('egift.list-egifts', $aAllParams, 'block');
        $this->html('#js_core_egift_list_egifts', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }
}

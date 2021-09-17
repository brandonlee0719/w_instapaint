<?php

namespace Apps\Core_Pages\Block;

defined('PHPFOX') or exit('NO DICE!');

class Cropme extends \Phpfox_Component
{
    public function process()
    {
        $iPage = $this->request()->get('id');
        $aPage = \Phpfox::getService('pages')->getForEdit($iPage);
        $this->template()->assign([
            'aPageCropMe' => $aPage
        ]);
    }
}

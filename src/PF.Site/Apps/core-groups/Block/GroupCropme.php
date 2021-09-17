<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class GroupCropme extends Phpfox_Component
{
    public function process()
    {
        $iGroupId = $this->request()->get('id');
        $aGroup = \Phpfox::getService('groups')->getForEdit($iGroupId);
        $this->template()->assign([
            'aGroupCropMe' => $aGroup
        ]);
    }
}

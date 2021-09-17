<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox_Component;
use Phpfox_Plugin;


defined('PHPFOX') or exit('NO DICE!');

class LoginUser extends Phpfox_Component
{
    public function process()
    {

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_login_user_clean')) ? eval($sPlugin) : false);
    }
}

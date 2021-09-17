<?php

namespace Apps\Core_Poke\Block;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('No dice!');

class Poke extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('user')->getUserFields(false, $aUser, null, $this->request()->get('user_id'));
        $this->template()->assign(array(
            'aUser' => $aUser,
            'bCanPoke' => Phpfox::getService('poke')->canSendPoke($this->request()->get('user_id'))
        ));

        return 'block';
    }
}

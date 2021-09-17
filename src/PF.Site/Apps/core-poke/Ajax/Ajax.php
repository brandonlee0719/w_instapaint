<?php

namespace Apps\Core_Poke\Ajax;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Error;

defined('PHPFOX') or exit('NO DICE!');

class Ajax extends Phpfox_Ajax
{

    public function poke()
    {
        $this->setTitle(_p('poke'));

        Phpfox::getBlock('poke.poke');
        echo '<script type="text/javascript">$Core.loadInit();</script>';

    }

    public function doPoke()
    {
        if (!Phpfox::getUserParam('poke.can_poke')) {
            return Phpfox_Error::display(_p('you_are_not_allowed_to_send_pokes'));
        }
        if (Phpfox::getUserParam('poke.can_only_poke_friends') &&
            Phpfox::isModule('friend') && !Phpfox::getService('friend')->isFriend(Phpfox::getUserId(),
                $this->get('user_id'))
        ) {
            return Phpfox_Error::display(_p('you_can_only_poke_your_own_friends'));
        }

        if (Phpfox::getService('poke.process')->sendPoke($this->get('user_id'))) {
            /* Type 1 is when poking back from the display block*/
            if ((int)$this->get('type') != 1) {
                $this->call('$("#section_poke").hide().remove();');
                return $this->alert(_p('your_poke_successfully_sent'), _p('notice'), 300, 150, true);
            }
        } else {
            $this->alert(_p('poke_could_not_be_sent'));
        }

        $this->call('$(".js_core_poke_item_' . $this->get('user_id') . '").hide().remove();');
    }

    public function ignore()
    {
        Phpfox::isUser(true);

        // Verify user_id
        $iUserId = (int)$this->get('user_id');
        if (!$iUserId) {
            return false;
        }

        // Process ignore the poke
        Phpfox::getService('poke.process')->ignore($iUserId);
        $this->call('$(".js_core_poke_item_' . $this->get('user_id') . '").hide().remove();');
    }

    public function viewMore()
    {
        Phpfox::isUser(true);
        $aAllParams = $this->getAll();
        $this->setTitle(_p('pokes'));
        Phpfox::getBlock('poke.display', $aAllParams);
        $this->call('<script>$Core.loadInit();</script>');
    }
}

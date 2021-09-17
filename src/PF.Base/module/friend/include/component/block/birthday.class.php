<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Friend_Component_Block_Birthday extends Phpfox_Component
{
    public function process()
    {
        $bHideBlock = $this->getParam('bHideBirthday', false);
        if ($bHideBlock || !Phpfox::getParam('friend.enable_birthday_notices')) {
            return false;
        }

        if (!Phpfox::isUser()) {
            return false;
        }

        $aBirthdays = Phpfox::getService('friend')->getBirthdays(Phpfox::getUserId());
        $iDayToCheck = Phpfox::getParam('friend.days_to_check_for_birthday');

        foreach ($aBirthdays as $key => $aBirthday) {
            if (isset($aBirthday['days_left']) && ($aBirthday['days_left'] > $iDayToCheck)) {
                unset($aBirthdays[$key]);
            }
        }

        if (empty($aBirthdays) && (Phpfox::getParam('friend.show_empty_birthdays') == false)) {
            return false;
        }

        $this->template()->assign(array(
                'aBirthdays' => $aBirthdays,
                'sHeader' => _p('birthdays')
            )
        );

        return 'block';
    }
}

<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Info
 */
class  User_Component_Block_Info extends Phpfox_Component
{
    public function process()
    {
        $viewer_id = Phpfox::getUserId();
        $user_id = $this->getParam('friend_user_id');

        if (!$viewer_id) {
            return false;
        }

        if ($viewer_id == $user_id) {
            return false;
        }

        // more info
        $iNumberOfInfo = $this->getParam('number_of_info', 1);
        $aUser = Phpfox::getService('user')->get($user_id);

        // get location of current user
        if ($iNumberOfInfo > 0) {
            $aLocation = [];
            if (!empty($aUser['city_location'])) {
                $aLocation[] = $aUser['city_location'];
            }
            if (empty($aLocation) && $aUser['country_child_id']) {
                $aLocation[] = Phpfox::getService('core.country')->getChild($aUser['country_child_id']);
            }
            if ($aUser['country_iso']) {
                $aLocation[] = Phpfox::getService('core.country')->getCountry($aUser['country_iso']);
            }
            if (!empty($aLocation)) {
                $iNumberOfInfo--;
                $sLocation = implode(', ', $aLocation);
            }
        }

        // get gender
        if ($iNumberOfInfo > 0 && $aUser['gender']) {
            $aGenders = Phpfox::getService('core')->getGenders();
            $sGender = $aGenders[$aUser['gender']];
            $iNumberOfInfo--;
        }

        // get joined date
        if ($iNumberOfInfo > 0) {
            $sJoined = Phpfox::getLib('date')->convertTime($aUser['joined'], null, true);
        }

        $this->template()->assign([
            'sLocation' => isset($sLocation) ? $sLocation : '',
            'sGender' => isset($sGender) ? $sGender : '',
            'sJoined' => isset($sJoined) ? $sJoined : ''
        ]);
        return null;
    }
}

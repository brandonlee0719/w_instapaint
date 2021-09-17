<?php

if ($sTemplate == 'user.block.rows_wide') {
    $aUser = Phpfox::getLib('template')->getVar('aUser');
    $aLocation = [];
    if (!empty($aUser)) {
        if (!empty($aUser['country_iso']) && $sCountry = Core_Service_Country_Country::instance()->getCountry($aUser['country_iso'], 'country')) {
            $aLocation[] = $sCountry;
        }

        if (!empty($aUser['country_child_id']) && $sState = Core_Service_Country_Country::instance()->getChild($aUser['country_child_id'])) {
            $aLocation[] = $sState;
        }

        if (count($aLocation) < 2 && !empty($aUser['city_location'])) {
            $aLocation[] = $aUser['city_location'];
        }

        if (Phpfox::isUser()) {
            $aUser['is_blocked'] = Phpfox::getService('user.block')->isBlocked($aUser['user_id'], Phpfox::getUserId());
        }

        if (!isset($aUser['is_featured'])) {
            $aUser['is_featured'] = Phpfox::getService('user')->isFeatured($aUser['user_id']);
        }

        if (!empty($aLocation)) {
            $aLocation = array_reverse($aLocation);
            $aUser['location_string'] = implode(', ', $aLocation);
        }

        Phpfox::getLib('template')->assign('aUser', $aUser);
    }
}
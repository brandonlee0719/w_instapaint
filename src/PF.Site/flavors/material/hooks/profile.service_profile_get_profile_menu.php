<?php

if (function_exists('materialParseIcon')) {
    foreach ($aMenus as $key => $aMenu) {
        $sType = 'default';
        if (!empty($aMenu['actual_url']) && preg_match('/profile_/i', $aMenu['actual_url'])) {
            $aParts = explode('_', $aMenu['actual_url'], 2);
            $sType = $aParts[1];
        }

        $aMenus[$key]['icon_class'] = materialParseIcon($sType);
    }
}
<?php

if (isset($aOut['privacy'])) {
    $sIconClass = 'ico ';
    switch ((int) $aOut['privacy']) {
        case 0:
            $sIconClass .= 'ico-globe';
            break;
        case 1:
            $sIconClass .= 'ico-user3-two';
            break;
        case 2:
            $sIconClass .= 'ico-user-man-three';
            break;
        case 3:
            $sIconClass .= 'ico-lock';
            break;
        case 4:
            $sIconClass .= 'ico-gear-o';
            break;
    }

    $aOut['privacy_icon_class'] = $sIconClass;
}
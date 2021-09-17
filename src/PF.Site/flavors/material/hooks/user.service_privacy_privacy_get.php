<?php

foreach ($aItems as $sModule => $aSettings) {
    foreach ($aSettings as $sKey => $aItem) {
        if (!isset($aItems[$sModule][$sKey]['icon_class']) && function_exists('materialParseIcon')) {
            $aItems[$sModule][$sKey]['icon_class'] = materialParseIcon($sModule);
        }
    }
}

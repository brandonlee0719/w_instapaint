<?php

if ($iId == 6) {
    array_splice($aBlocks[$iId], 1, 0, [['type_id' => 0, 'component' => 'core.template-breadcrumbmenu', 'params' => []]]);
} elseif ($sController == 'core.index-visitor' && in_array($iId, [3, 10])) {
    foreach ($aBlocks[$iId] as $iIndex => $aBlock) {
        if ($aBlock['component'] != 'user.register') {
            unset($aBlocks[$iId][$iIndex]);
        }
    }
} elseif ($iId == 1 && Phpfox::getLib('module')->getFullControllerName() == 'search.index') {
    array_unshift($aBlocks[$iId], array('type_id' => 0, 'component' => 'core.template-menusub', 'params' => array()));
}
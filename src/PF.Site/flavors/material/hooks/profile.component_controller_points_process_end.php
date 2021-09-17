<?php
$aIcons = [];
foreach ($aModules as $sModuleId => $aModule) {
    foreach ($aModule as $sPhrase => $sLink) {
        if (function_exists('materialParseIcon')) {
            $aIcons[$sPhrase] = materialParseIcon($sModuleId);
        }
    }
}
$this->template()->assign([
    'aIcons' => $aIcons,
    'iTotalItems' => $aActivites[_p('total_items')],
    'iTotalPoints' => $aUser['activity_points']
]);
unset($aActivites[_p('total_items')]);
unset($aActivites[_p('activity_points')]);
$this->template()->assign('aActivites', $aActivites);

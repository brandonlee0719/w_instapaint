<?php
if (isset($aMenus)) {
    $aFilterMenusIcons = [
        _p('all_results') => materialParseIcon('all_results')
    ];
    foreach ($aMenus as $sKey => $aMenu) {
        $aFilterMenusIcons[$aMenu['name']] = materialParseIcon($sKey);
    }

    $this->template()->assign(compact('aFilterMenusIcons'));
}

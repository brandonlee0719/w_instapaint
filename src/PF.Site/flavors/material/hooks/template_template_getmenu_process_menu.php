<?php
if ($sConnection == 'main') {
    if (function_exists('materialParseIcon') && !empty($aMenus[$iKey]['module'])) {
        $aMenus[$iKey]['mobile_icon'] = materialParseMobileIcon($aMenus[$iKey]['mobile_icon']);
    }

    if (!empty($aMenus[$iKey]['is_selected'])) {
        $this->assign('aMainSelectedMenu', $aMenus[$iKey]);
    }
}
<?php
if (!Phpfox::getUserParam('pages.can_view_browse_pages')) {
    foreach ($aMenus as $index => $aMenu) {
        if ($aMenu['m_connection'] == 'main' && $aMenu['module'] == 'pages') {
            unset($aMenus[$index]);
        }
    }
}

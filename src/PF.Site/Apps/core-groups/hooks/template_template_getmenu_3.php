<?php
if (!Phpfox::getUserParam('groups.pf_group_browse')) {
    foreach ($aMenus as $index => $aMenu) {
        if ($aMenu['m_connection'] == 'main' && $aMenu['module'] == 'groups') {
            unset($aMenus[$index]);
        }
    }
}

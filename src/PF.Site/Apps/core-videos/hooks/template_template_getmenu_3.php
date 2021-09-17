<?php
if ((!(user('pf_video_view', 1))) && $sConnection == 'main') {
    foreach ($aMenus as $key => $menu) {
        if ($menu['module'] == 'v') {
            unset($aMenus[$key]);
        }
    }
}

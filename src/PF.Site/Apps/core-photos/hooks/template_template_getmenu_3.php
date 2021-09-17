<?php

if ((!(Phpfox::getUserParam('photo.can_view_photos'))) && $sConnection == 'main') {
	foreach ($aMenus as $key => $menu) {
		if ($menu['module'] == 'photo') {
			unset($aMenus[$key]);
		}
	}
}

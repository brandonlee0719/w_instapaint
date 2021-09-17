<?php

$controller_name = \Phpfox_Module::instance()->getFullControllerName();
if (isset(flavor()->active->blocks) && isset(flavor()->active->blocks->{$controller_name})) {
	foreach (flavor()->active->blocks->{$controller_name} as $location => $html) {
		foreach ($html as $file) {
			if (substr($file, 0, 1) == '@') {
				new Core\Block($controller_name, $location, function () use ($file) {
					list($namespace, $module, $block) = explode('/', $file);

					\Phpfox::getBlock($module . '.' . $block);

					return @ob_get_clean();
				});

				continue;
			}

			new Core\Block($controller_name, $location, function () use ($file) {
				return view('@Flavor/' . $file);
			});
		}
	}
}
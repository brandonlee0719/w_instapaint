<?php

$flavor_default = '';
$default = storage()->get('flavor/default');
if (isset($default->value)) {
	$flavor_default = $default->value;
}
$themes = [];
$default_theme = [];
foreach (flavor()->all() as $flavor) {
	if ($flavor->id == $flavor_default) {
		$default_theme = [
			'theme_id' => $flavor->id,
			'is_default' => true,
			'image' => ($flavor->icon ? ' class="image_load has_image" data-src="' . $flavor->url . 'theme.png?v=' . uniqid() . '" ' : ''),
			'name' => $flavor->name
		];

		continue;
	}

	$themes[] = [
		'theme_id' => $flavor->id,
		'is_default' => false,
		'image' => ($flavor->icon ? ' class="image_load has_image" data-src="' . $flavor->url . 'theme.png?v=' . uniqid() . '" ' : ''),
		'name' => $flavor->name
	];
}

if ($default_theme) {
	$themes = array_merge([$default_theme], $themes);
}
else {
	$themes[0]['is_default'] = true;
}
<?php

$content = '';
$banners = flavor()->active->banners();
if (count($banners)) {
	$usage = request()->get('image');
	if ($usage) {
		$image = [
			'image' => $usage,
			'info' => ''
		];
	} else {
		$total = rand(1, (count($banners)));
		$image = [];
		$cnt = 0;
		foreach ($banners as $banner) {
			$image = [
				'image' => $banner,
				'info' => ''
			];

			$cnt++;
			if ($cnt === $total) {
				break;
			}
		}
	}
}
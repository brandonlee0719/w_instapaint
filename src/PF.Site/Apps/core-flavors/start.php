<?php

event('Core\View\Loader::getSource', function(\Core\View\Loader $loader) {
    $loader->layout = file_get_contents(flavor()->active->html_path());
});

group('/flavors', function() {

    route('/manage', function() {
        if (Phpfox::demoMode()) {
            return false;
        }

        auth()->isAdmin(true);

        $flavor = flavor()->get(request()->get('id'));
        if ($flavor === false) {
            return url()->send('/admincp/theme/');
        }

        if (request()->get('type')) {
            $html = '';
            $title = '';
            $ace = false;
            $save = url()->make('/flavors/manage', ['id' => $flavor->id, 'type' => request()->get('type')]);
            $mode = 'html';
            switch (request()->get('type')) {
                case 'revert':
                    if (request()->get('process')) {
                        $flavor->revert();

                        return url()->send('/flavors/manage', ['id' => $flavor->id], _p('Theme successfully reverted'));
                    }

                    title(_p('Revert'));

                    return view('revert.html', [
                        'flavor' => $flavor
                    ]);
                    break;
                case 'default':
                    storage()->del('flavor/default');
                    storage()->set('flavor/default', $flavor->id);
                    flavor()->set_active($flavor->id);
                    flavor()->rebuild_bootstrap(true);
                    cache()->purge();

                    return [
                        'run' => 'location.reload();'
                    ];
                    break;
                case 'export':
                    $flavor->export();
                    break;
                case 'delete_banner':
                    $dir = $flavor->path . 'assets/banners/';
                    $banner = str_replace(home() . 'PF.Site/flavors/' . $flavor->id . '/assets/banners/', '', request()->get('banner'));
                    $file = $dir . $banner;
                    if (file_exists($file)) {
                        unlink($file);
                    }

                    return [
                        'success' => 'true'
                    ];
                    break;
                case 'delete':
                    if (request()->get('process')) {
                        $flavor->delete();

                        return url()->send('/admincp/theme/', _p('Theme successfully deleted'));
                    }

                    title(_p('Delete'));

                    return view('delete.html', [
                        'flavor' => $flavor
                    ]);
                    break;
                case 'settings':
                    title(_p('Advanced Settings'));

                    if (request()->isPost()) {
                        $flavor->save('settings', request()->get('content'));

                        return [
                            'run' => "$('.js_box_content').html('<div class=\"message\">" . _p('Settings successfully saved.') . "</div>'); setTimeout(tb_remove, 2000);"
                        ];
                    }

                    return view('settings.html', [
                        'flavor' => $flavor,
                        'json' => $flavor->json()
                    ]);
                    break;
                case 'icon':
                    if (!empty($_FILES['ajax_upload'])) {
                        $url = $flavor->save('icon', $_FILES['ajax_upload']);

                        return [
                            'run' => 'Theme_Manager.icon(\'' . $url . '\')'
                        ];
                    }
                    break;
                case 'content':
                    $flavor->save('content', request()->get('content'));

                    return [
                        'success' => true
                    ];
                    break;
                case 'homepage':
                    if (!empty($_FILES['ajax_upload'])) {
                        $banner = $flavor->save('banners', $_FILES['ajax_upload']);

                        $params = [
                            'banner' => rawurlencode($banner)
                        ];
                        return [
                            'run' => "Theme_Manager.banner(" . json_encode($params) . ");"
                        ];
                    }

                    $title = 'Homepage';
                    $html = view('@PHPfox_Flavors/homepage.html', [
                        'flavor' => $flavor,
                        'banners' => $flavor->banners(),
                        'content' => $flavor->content()
                    ]);
                    break;
                case 'logo':
                    if (!empty($_FILES['ajax_upload'])) {
                        $flavor->save('logos', $_FILES['ajax_upload'], request()->get('sub_type'));

                        $params = [
                            'type' => request()->get('sub_type'),
                            'logo' => $flavor->logo_url(),
                            'favicon' => $flavor->favicon_url()
                        ];
                        return [
                            'run' => "Theme_Manager.logo(" . json_encode($params) . ");"
                        ];
                    }

                    $title = 'Logos';
                    $html = view('@PHPfox_Flavors/logo.html', [
                        'flavor' => $flavor,
                        'logo' => $flavor->logo_url(),
                        'favicon' => $flavor->favicon_url()
                    ]);
                    break;

                // support set default photo for theme
                case 'default_photo':
                    $aPhotos = [
                        'user_cover_default' => [
                            'title' => _p('User Cover Default Photo'),
                            'value' => $flavor->default_photo('user_cover_default', true),
                        ]
                    ];

                    if (\Phpfox::isModule('pages')) {
                        $aPhotos['pages_cover_default'] = [
                            'title' => _p('Pages Cover Default Photo'),
                            'value' => $flavor->default_photo('pages_cover_default', true),
                        ];
                    }

                    if (\Phpfox::isModule('groups')) {
                        $aPhotos['groups_cover_default'] = [
                            'title' => _p('Groups Cover Default Photo'),
                            'value' => $flavor->default_photo('groups_cover_default', true),
                        ];
                    }

                    (($sPlugin = \Phpfox_Plugin::get('theme_get_default_photos_list')) ? eval($sPlugin) : false);

                    if (!empty($_FILES['ajax_upload'])) {
                        $flavor->save('default_photo', $_FILES['ajax_upload'], request()->get('sub_type'));
                        $params = [
                            'type' => request()->get('sub_type'),
                            'file' => $flavor->default_photo(request()->get('sub_type'), true) . '?v=' . uniqid(),
                        ];
                        return [
                            'run' => "Theme_Manager.default_photo(" . json_encode($params) . ");"
                        ];
                    }

                    $title = 'Default Photos';
                    $html = view('@PHPfox_Flavors/default_photo.html', [
                        'flavor' => $flavor,
                        'photos' => $aPhotos
                    ]);
                    break;

                // support set default photo for theme
                case 'remove_default':
                    $flavor->save('remove_default', '', request()->get('sub_type'));
                    $params = [
                        'type' => request()->get('sub_type'),
                        'file' => '',
                    ];
                    return [
                        'run' => "Theme_Manager.default_photo(" . json_encode($params) . ");"
                    ];
                    break;

                case 'design':
                    if (request()->isPost()) {
                        $flavor->save(request()->get('type'), request()->get('var'));

                        return [
                            'run' => 'Theme_Manager.design();'
                        ];
                    }

                    $title = 'Design';
                    $html = $flavor->design();
                    break;
                case 'css':
                    $title = 'CSS';
                    if (request()->isPost()) {
                        $flavor->save('css', request()->get('content', '', false));

                        return [
                            'run' => 'Theme_Manager.success();'
                        ];
                    }

                    $ace = $flavor->css();
                    $mode = 'css';
                    break;
                case 'js':
                    if (request()->isPost()) {
                        $flavor->save('js', request()->get('content', '', false));

                        return [
                            'run' => 'Theme_Manager.success();'
                        ];
                    }

                    $title = 'Javascript';
                    $ace = $flavor->js();
                    $mode = 'javascript';
                    break;
                case 'html':
                    if (request()->isPost()) {
                        $flavor->save('html', request()->get('content', '', false));

                        return [
                            'run' => 'Theme_Manager.success();'
                        ];
                    }

                    $title = 'HTML';
                    $ace = $flavor->html(true);
                    break;
            }

            return [
                'type' => request()->get('type'),
                'html' => $html,
                'title' => $title,
                'ace' => $ace,
                'save' => $save,
                'mode' => $mode
            ];
        }

        \Core\View::$template = 'blank';

        asset('<link href="' . home() . 'PF.Base/static/jscript/colorpicker/css/colpick.css" rel="stylesheet">');
        asset('@static/colorpicker/js/colpick.js');

        $store = null;
        $has_upgrade = false;
        if (isset($flavor->store_id) && $flavor->store_id) {
            $store = json_decode(@fox_get_contents('https://store.phpfox.com/product/' . $flavor->store_id . '/view.json'), true);
            if (isset($store['id']) && version_compare($flavor->version, $store['version'], '<')) {
                $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
                $response = $Home->admincp(['return' => url('admincp.app.add')]);
                $store['install_url'] = $store['url'] . '/installing?iframe-mode=' . $response->token;
                $has_upgrade = true;
            }
        }

        /**
         * Commented by Frank to fix this issue http://prntscr.com/hqv6kt
         */
//        Phpfox::getLib('cache')->remove();
        Phpfox::getLib('template.cache')->remove();
        Phpfox::getLib('cache')->removeStatic();

        return view('manage.html', [
            'flavor' => $flavor,
            'show_design' => (isset($flavor->vars) && count((array) $flavor->vars) ? true : false),
            'show_js' => $flavor->has_js(),
            'has_upgrade' => $has_upgrade,
            'store' => $store,
            'active_flavor_id' => flavor()->active->id
        ]);
    });
});

event('view_cache_path', function() {
    Core\View::$cache_path = PHPFOX_DIR_CACHE . 'twig' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS;
});

group('/admincp/theme', function() {

	route('/merge', function() {
		auth()->isAdmin(true);
		title(_p('Merge Legacy Theme'));

		if (($theme_id = request()->get('theme'))) {
			list($theme_id, $flavor) = explode('/', $theme_id);
			$path = PHPFOX_DIR_SITE . 'themes/' . $theme_id . '/';
			$html = file_get_contents($path . 'html/layout.html');
			flavor()->active->save('html', $html);
			flavor()->active->save('css', '');
			$json = json_decode(file_get_contents(flavor()->active->path . 'theme.json'));
			$json->legacy = [
				'theme' => $theme_id,
				'flavor' => $flavor
			];
			file_put_contents(flavor()->active->path . 'theme.json', json_encode($json, JSON_PRETTY_PRINT));

			return [
				'run' => 'flavor_end(); tb_remove();'
			];
		}

		$themes = [];
		$path = PHPFOX_DIR_SITE . 'themes/';
		if (is_dir($path)) {
			foreach (scandir($path) as $theme) {
				if ($theme == '.' || $theme == '..' || $theme == 'bootstrap' || $theme == '2') {
					continue;
				}

				$dir = $path . $theme . '/flavor/';
				$flavors = [];
				if (!is_dir($dir)) {
					continue;
				}

				foreach (scandir($dir) as $flavor) {
					if (substr($flavor, -5) == '.less') {
						$flavors[] = str_replace('.less', '', $flavor);
					}
				}

				$themes[$theme] = [
					'name' => $theme,
					'flavors' => $flavors
				];
			}
		}

		return view('merge.html', [
			'themes' => $themes
		]);
	});

    route('/bootstrap/rebuild', function () {
        auth()->isAdmin(true);

        try {
            flavor()->rebuild_bootstrap(true);
            if (PHPFOX_IS_AJAX_PAGE) {
                return [
                    'run' => 'flavor_end();'
                ];
            } else {
                Phpfox::addMessage(_p('successfully_rebuilt_core_theme'));
                url()->send('admincp');
            }
        } catch (\Exception $ex) {
            return [
                'run' => sprintf('flavor_alert("%s")', base64_encode($ex->getMessage()))
            ];
        }

        return null;
    });

	route('/manage', function() {
		auth()->isAdmin(true);
		url()->send('/flavors/manage', ['id' => request()->get('id')]);
	});

	route('/add', function() {
        if (Phpfox::demoMode()) {
            return false;
        }

		auth()->isAdmin(true);
		title(_p('New Theme'));

		$file = null;
		$is_download = false;
		if (!empty($_FILES['ajax_upload'])) {
			$file = $_FILES['ajax_upload'];
		}
		else if (request()->get('download')) {
			$is_download = true;
			$path = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . uniqid() . '.zip';

			file_put_contents($path, fox_get_contents(request()->get('download','',false)));

			$file = [
				'is_local' => true,
				'tmp_name' => $path
			];
		}

		if (request()->isPost() || $file) {

			$flavor = flavor()->make(request()->get('val', []), $file, $is_download);

			return url()->send('/flavors/manage', ['id' => $flavor->id]);
		}

		$themes = [];
		$themes['__blank'] = _p('Blank Theme');
		foreach (flavor()->all() as $flavor) {
			$themes[$flavor->id] = $flavor->name;
		}
		return view('add.html', [
			'themes' => $themes
		]);
	});
});

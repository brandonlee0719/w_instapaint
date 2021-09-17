<?php

namespace Core;

class Asset
{
    private $_image = '';

    public function __construct($assets)
    {
        if (is_string($assets)) {
            $supported_image = [
                'gif',
                'jpg',
                'jpeg',
                'png',
            ];
            $ext = strtolower(pathinfo($assets, PATHINFO_EXTENSION));
            if (in_array($ext, $supported_image)) {
                $this->_image = flavor()->active->url . 'assets/' . $assets;

                return;
            }
        }

        if (!is_array($assets)) {
            $assets = [$assets];
        }

        foreach ($assets as $asset) {
            if (substr($asset, 0, 11) == '@static_css') {
                \Phpfox_Template::instance()->setHeader([str_replace('@static_css/', '', $asset) => 'static_style']);
            } elseif (substr($asset, 0, 7) == '@static') {
                \Phpfox_Template::instance()->delayedHeaders[] = [str_replace('@static/', '', $asset) => 'static_script'];
            } elseif (substr($asset, 0, 7) == '@flavor') {
                list(, $asset_name) = explode('/', $asset, 2);
                $path = str_replace(PHPFOX_DIR_SITE, home() . 'PF.Site/', flavor()->active->path) . 'assets/' . $asset_name;
                if (substr($asset_name, -3) == '.js') {
                    \Phpfox_Template::instance()->delayedHeaders[] = ['<script src="' . $path . '"></script>'];
                } else {
                    \Phpfox_Template::instance()->setHeader('<link href="' . $path . '" rel="stylesheet">');
                }
            } elseif (substr($asset, 0, 1) == '@') {
                list($app_id, $asset_name) = explode('/', $asset, 2);
                $app_id = str_replace('@', '', $app_id);
                if ((new App())->exists($app_id)) {
                    $app = (new App())->get($app_id);
                    $path = str_replace(PHPFOX_DIR_SITE, home() . 'PF.Site/', $app->path) . 'assets/' . $asset_name;
                    if (substr($asset_name, -3) == '.js') {
                        \Phpfox_Template::instance()->delayedHeaders[] = ['<script src="' . $path . '"></script>'];
                    } else {
                        \Phpfox_Template::instance()->setHeader('<link href="' . $path . '" rel="stylesheet">');
                    }
                }
            } else {
                \Phpfox_Template::instance()->setHeader($asset);
            }
        }
    }

    public function __toString()
    {
        return $this->_image;
    }
}
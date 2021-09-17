<?php

namespace Core\HTTP;

class Cache
{

    private static $_set = false;

    public function enabled()
    {
        return setting('pf_core_http_cache');
    }

    public function set($id = null, $type = 'text/html')
    {
        self::$_set = [
            'id'   => $id,
            'type' => $type,
        ];
    }

    public function run()
    {
        if (request()->segment(1) == 'admincp'
            || defined('PHPFOX_INSTALLER')
        ) {
            return;
        }

        $data = null;
        $id = self::$_set['id'];
        $type = self::$_set['type'];

        if (empty($_COOKIE['_flavor_id'])) {
            $_COOKIE['_flavor_id'] = flavor()->active->id;
        }

        if (empty($_COOKIE['_language_id'])) {
            $_COOKIE['_language_id'] = \Phpfox_Locale::instance()->getLangId();
        }

        if (setting('pf_core_http_cache') && self::$_set && isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') {
            $empty_data = false;

            if ($id === null) {
                $id = $_SERVER['REQUEST_URI'];
            }

            if ($data === null) {
                $data = ob_get_clean();
                $empty_data = true;
            }

            $id = md5($id);

            $the_folder = PHPFOX_DIR_FILE . 'http_cache' . PHPFOX_DS . date('y/m/d/') . $_COOKIE['_language_id'] . PHPFOX_DS . $_COOKIE['_flavor_id'] . PHPFOX_DS;
            $the_folder .= (PHPFOX_IS_AJAX_PAGE ? 'ajax' : 'html') . PHPFOX_DS;
            if (!is_dir($the_folder)) {
                mkdir($the_folder, 0777, true);
            }
            $the_cache = $the_folder . $id . '.json';

            file_put_contents($the_cache, json_encode([
                'hash' => $id,
                'uri'  => $_SERVER['REQUEST_URI'],
                'data' => $data,
                'type' => $type,
            ]));

            if ($empty_data) {
                echo $data;
            }
        }
    }
}
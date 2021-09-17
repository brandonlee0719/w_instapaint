<?php

namespace Core;

class Setting extends \Core\Model
{
    private static $_settings = null;

    public function __construct($settings = [])
    {
        if ($settings) {
            self::$_settings = $settings;

            return;
        }

        if (self::$_settings === null) {
            parent::__construct();

            self::$_settings = $this->cache->get('app_settings');
            if (is_bool(self::$_settings)) {
                $App = new \Core\App(true);
                foreach ($App->all() as $_app) {
                    if ($_app->settings) {
                        foreach (json_decode(json_encode($_app->settings), true) as $key => $value) {
                            $thisValue = (isset($value['value']) ? $value['value'] : null);
                            $value = $this->db->select('*')->from(':setting')->where(['var_name' => $key])->get();
                            if (isset($value['value_actual'])) {
                                $thisValue = \Phpfox::getLib('setting')->getActualValue($value['type_id'],
                                    $value['value_actual']);

                            }
                            self::$_settings[$key] = $thisValue;
                        }
                    }
                }

                $this->cache->set('app_settings', self::$_settings);
                self::$_settings = $this->cache->get('app_settings');
            }
        }
    }

    public function get($key, $default = null)
    {
        if (strpos($key, '.')) {
            return \Phpfox::getParam($key);
        }

        return (isset(self::$_settings[$key]) ? $this->_get(self::$_settings[$key]) : $default);
    }

    public function set($key, $value)
    {
        self::$_settings[$key] = $value;
    }

    private function _get($key)
    {
        $server_host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $key = str_replace('{HTTP_HOST}', $server_host, $key);
        return $key;
    }
}
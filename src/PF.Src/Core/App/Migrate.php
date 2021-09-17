<?php

namespace Core\App;

use Core\Database\Apps;

/**
 * Class Migrate this class only available on this version
 *
 * @author  Neil
 * @version 4.5.0
 * @package Core\App
 */
class Migrate
{
    public static function getApps()
    {
        $base = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS;
        if (!is_dir($base)) {
            return false;
        }
        $aApps = [];
        foreach (scandir($base) as $app) {
            if ($app == '.' || $app == '..') {
                continue;
            }

            $path = $base . $app . PHPFOX_DS;

            if (file_exists($path . 'Install.php')) {
                continue;
            }
            if (file_exists($path . 'app.json')) {
                $jsonData = file_get_contents($path . 'app.json');
                $jsonData = preg_replace_callback('/{{ ([a-zA-Z0-9_]+) }}/is', function ($matches) use ($jsonData) {
                    $_data = json_decode($jsonData);
                    if (!isset($_data->{$matches[1]})) {
                        return $matches[0];
                    }

                    return $_data->{$matches[1]};
                }, $jsonData);

                $data = json_decode($jsonData);
                $data->path = $path;

                $aApps[] = $data;
            }
        }
        return $aApps;
    }

    public static function run()
    {
        $table = new Apps();
        $table->install();
        $aApps = self::getApps();
        foreach ($aApps as $app) {
            self::migrate($app->id);
        }
        $base = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS;
        foreach (scandir($base) as $app) {
            if (!$appClass = \Core\Lib::appInit($app)) {
                continue;
            }
            $iCnt = db()->select('COUNT(*)')
                ->from(':apps')
                ->where([
                    'apps_id' => $appClass->id,
                ])
                ->executeField();
            if (!$iCnt) {
                db()->insert(':apps', [
                    'apps_id'     => $appClass->id,
                    'version'     => $appClass->version ? $appClass->version : '4.0.1',
                    'apps_alias'  => $appClass->alias ? $appClass->alias : null,
                    'author'      => $appClass->_publisher ? $appClass->_publisher : 'n/a',
                    'vendor'      => $appClass->_publisher_url ? $appClass->_publisher_url : '',
                    'apps_icon'   => $appClass->icon ? $appClass->icon : '',
                    'description' => '',
                    'apps_name'   => $appClass->name,
                    'type'        => $appClass->isCore() ? 1 : 2,
                    'is_active'   => 1,
                ]);
            }
        }
    }

    private static function array2String($array)
    {
        if (!is_array($array)) {
            return "'$array'";
        }
        $string = '[';
        foreach ($array as $key => $value) {
            $string .= "'" . $key . "' => " . self::array2String($value) . ",";
        }
        $string .= ']';
        return $string;
    }

    public static function migrate($app_id, $bNew = false)
    {
        $json_path = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $app_id . PHPFOX_DS . 'app.json';
        if (!$bNew && !file_exists($json_path)) {
            return false;
        }
        if ($bNew) {
            if (!is_dir(dirname($json_path))) {
                mkdir(dirname($json_path), 0777, true);
            }
            $json = ['id' => $app_id, 'name' => $app_id];
        } else {
            $json = json_decode(file_get_contents($json_path), true);
        }
        $code
            = '<?php
            namespace Apps\\' . $app_id . ';

            use Core\App;

            /**
             * Class Install
             * @author  Neil J. <neil@phpfox.com>
             * @version 4.6.0
             * @package Apps\\' . $app_id . '
             */
            class Install extends App\App
            {
                private $_app_phrases = [
        
                ];
                protected function setId()
                {
                    $this->id = \'' . $app_id . '\';
                }
                protected function setAlias() 
                {
            ';
        if (isset($json['alias'])) {
            $code
                .= '
                $this->alias = \'' . $json['alias'] . '\';
             ';
            unset($json['alias']);
        }
        $code .= '}';
        unset($json['id']);

        //name
        $code
            .= '
            protected function setName()
            {
                $this->name = \'' . $json['name'] . '\';
            }';
        unset($json['name']);

        //version
        $code .= ' protected function setVersion() {';

        if (isset($json['version'])) {
            $code
                .= '
                $this->version = \'' . $json['version'] . '\';
            ';
            unset($json['version']);
        } else {
            $code
                .= '
                $this->version = \'4.1.0\';
            ';
        }
        $code .= '}';

        //support version
        $code .= ' protected function setSupportVersion() {';
        if ($bNew) {
            $sVersion = \Phpfox::getVersion();
        } else {
            $sVersion = "4.4.0";
        }
        $code
            .= '
            $this->start_support_version = \'' . $sVersion . '\';
            $this->end_support_version = \'' . $sVersion . '\';
        ';
        $code .= '}';

        //settings
        $code .= ' protected function setSettings() {';
        if (isset($json['settings'])) {
            $code
                .= '
                $this->settings = ' . self::array2String($json['settings']) . ';
            ';
            unset($json['settings']);
        }
        $code .= '}';

        //user_group_settings
        $code .= ' protected function setUserGroupSettings() {';
        if (isset($json['user_group_settings'])) {
            $code
                .= '
                $this->user_group_settings = ' . self::array2String($json['user_group_settings']) . ';
            ';
            unset($json['user_group_settings']);
        }
        $code .= '}';

        //component
        $code .= ' protected function setComponent() {';
        if (isset($json['component'])) {
            $code
                .= '
                $this->component = ' . self::array2String($json['component']) . ';
            ';
            unset($json['component']);
        }
        $code .= '}';

        //component
        $code .= ' protected function setComponentBlock() {';
        if (isset($json['component_block'])) {
            $code
                .= '
                $this->component_block = ' . self::array2String($json['component_block']) . ';
            ';
            unset($json['component_block']);
        }
        $code .= '}';

        //phrase
        $code .= ' protected function setPhrase() {';
        $code
            .= '
            $this->phrase = $this->_app_phrases;
        ';
        $code .= '}';

        //others
        $code .= ' protected function setOthers() {';
        if (isset($json['notifications'])) {
            $code
                .= '
                $this->notifications = ' . self::array2String($json['notifications']) . ';
            ';
            unset($json['notifications']);
        }
        if (isset($json['admincp_route'])) {
            $code
                .= '
                $this->admincp_route = ' . self::array2String($json['admincp_route']) . ';
            ';
            unset($json['admincp_route']);
        }
        if (isset($json['admincp_menu'])) {
            $code
                .= '
                $this->admincp_menu = ' . self::array2String($json['admincp_menu']) . ';
            ';
            unset($json['admincp_menu']);
        }
        if (isset($json['admincp_help'])) {
            $code
                .= '
                $this->admincp_help = ' . self::array2String($json['admincp_help']) . ';
            ';
            unset($json['admincp_help']);
        }
        if (isset($json['admincp_action_menu'])) {
            $code
                .= '
                $this->admincp_action_menu = ' . self::array2String($json['admincp_action_menu']) . ';
            ';
            unset($json['admincp_action_menu']);
        }
        if (isset($json['map'])) {
            $code
                .= '
                $this->map = ' . self::array2String($json['map']) . ';
            ';
            unset($json['map']);
        }
        if (isset($json['map_search'])) {
            $code
                .= '
                $this->map_search = ' . self::array2String($json['map_search']) . ';
            ';
            unset($json['map_search']);
        }
        if (isset($json['menu'])) {
            $code
                .= '
                $this->menu = ' . self::array2String($json['menu']) . ';
            ';
            unset($json['menu']);
        }
        if (isset($json['icon'])) {
            $code
                .= '
                $this->icon = ' . self::array2String($json['icon']) . ';
            ';
            unset($json['icon']);
        }
        $code .= '}';

        if (count($json)) {
            foreach ($json as $key => $value) {
                $code
                    .= '
                public $' . $key . ' = ' . self::array2String($value) . ';';
            }
        }

        //End of class
        $code .= '}';

        $installPath = $json_path = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $app_id . PHPFOX_DS . 'Install.php';
        file_put_contents($installPath, $code);

        $autoloader = include PHPFOX_PARENT_DIR . 'PF.Base/vendor/autoload.php';
        $autoloader->addPsr4('\Apps\\' . $app_id . '\\', dirname($installPath));

        if (file_exists($installPath)) {
            include_once $installPath;
        }

        if ($bNew) {
            db()->insert(':apps', [
                'apps_id'    => $app_id,
                'apps_name'  => $app_id,
                'apps_alias' => isset($json['alias']) ? $json['alias'] : '',
                'type'       => 2,
                'is_active'  => 1,
            ]);
        }
        return true;
    }
}
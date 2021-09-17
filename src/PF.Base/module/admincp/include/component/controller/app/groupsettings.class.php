<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @author Neil <neil@phpfox.com>
 * Class Admincp_Component_Controller_App_Settings
 */
class Admincp_Component_Controller_App_Groupsettings extends Phpfox_Component {

    public function process() {

        $sAppId = $this->request()->get('id');
        $App = \Core\Lib::appInit($sAppId);

        $sModule = isset($App->alias)? $App->alias : $App->id;
        $iGroupId = $this->request()->get('group_id', 2);

        Phpfox::getLib('request')->set('module',$sModule);
        Phpfox::getLib('request')->set('product-id', $sAppId);
        Phpfox::getLib('request')->set('setting', 1);
        Phpfox::getLib('request')->set('group_id', $iGroupId);
        $this->setParam('bInAppDetail', $sAppId);
        Phpfox::getLib('module')->setController('user.admincp.group.add');

        return true;

        $uninstallUrl = $this->url()->makeUrl('admincp.app', ['id' => $App->id, 'uninstall' => 'yes']);
        $aCurrencies = Phpfox::getService('core.currency')->get();
        if (!$App->is_module) {

            if (($val = $this->request()->get('val')) && $this->request()->get('uninstall')) {
                if (!($error = Phpfox::getService('user.auth')->loginAdmin($val['email'], $val['password']))) {
                    throw new \Exception(implode('', Phpfox_Error::get()));
                }
                if ($appInit = \Core\Lib::appInit($this->request()->get('id'))) {
                    $appInit->uninstall();
                }
                $val['password'] = $val['ftp_password'];
                $App->delete($val);

                Phpfox::addMessage(_p('app_successfully_uninstalled_dot'));
                return [
                    'redirect' => $this->url()->makeUrl('admincp.apps')
                ];
            }

            if (($settings = $this->request()->get('setting'))) {
                $Setting = new Core\Setting\Service($App);
                try {
                    $Setting->save($settings);

                    Core\Event::trigger('app_settings', $settings);

                } catch (\Exception $e) {
                    return [
                        'error' => $e->getMessage()
                    ];
                }

                return [
                    'updated' => true,
                    'message' => _p('Your changes have been saved!')
                ];
            }

            if (($settings = $this->request()->get('user_group_setting'))) {
                $UserGroupSetting = new Core\User\Setting();
                $UserGroupSetting->save($App, $settings);

                return [
                    'updated' => true,
                    'message' => _p('Your changes have been saved!')
                ];
            }

            if ($this->request()->get('export')) {
                $App->export();
                exit;
            }

            $menus = [];

            if ($App->admincp_action_menu) {
                $App->admincp_action_menu = (array) $App->admincp_action_menu;

                $name = _p(array_values($App->admincp_action_menu)[0]);
                $this->template()->setActionMenu([
                    $name => [
                        'class' => 'popup',
                        'url' => url(array_keys($App->admincp_action_menu)[0])
                    ]
                ]);
            }

            if ($App->id == 'PHPfox_Core') {
                $cache = null;
                $path = PHPFOX_DIR_SETTINGS . 'cache.sett.php';
                if (file_exists($path)) {
                    $cache = require($path);
                }

                $redis = null;
                $path = PHPFOX_DIR_SETTINGS . 'redis.sett.php';
                if (file_exists($path)) {
                    $redis = require($path);
                }
            }

            $settings = [];

            if (count($App->settings)) {
                foreach ($App->settings as $key => $value) {
                    if (!isset($value->type)) {
                        $value->type = 'input:text';
                    }

                    if (!isset($value->value)) {
                        $value->value = '';
                    }

                    if (setting($key) !== null) {
                        $value->value = setting($key);
                    }

                    if (!isset($value->options)) {
                        $value->options = [];
                    }

                    if (!isset($value->description)) {
                        $value->description = '';
                    }

                    if ($App->id == 'PHPfox_Core' && $cache !== null) {
                        switch ($key) {
                            case 'pf_core_cache_driver':
                                $value->value = $cache['driver'];
                                break;
                            case 'pf_core_cache_memcached_host':
                                if (isset($cache['memcached'][0]) && !empty($cache['memcached'][0][0])) {
                                    $value->value = $cache['memcached'][0][0];
                                }
                                break;
                            case 'pf_core_cache_memcached_port':
                                if (isset($cache['memcached'][0]) && !empty($cache['memcached'][0][1])) {
                                    $value->value = $cache['memcached'][0][1];
                                }
                                break;
                            case 'pf_core_cache_redis_host':
                                if (isset($cache['redis']) && !empty($cache['redis']['host'])) {
                                    $value->value = $cache['redis']['host'];
                                }
                                break;
                            case 'pf_core_cache_redis_port':
                                if (isset($cache['redis']) && !empty($cache['redis']['port'])) {
                                    $value->value = $cache['redis']['port'];
                                }
                                break;
                        }
                    }

                    if ($App->id == 'PHPfox_Core' && $redis !== null) {
                        switch ($key) {
                            case 'pf_core_redis_host':
                                $value->value = (!empty($redis['host']) ? $redis['host'] : '');
                                break;
                            case 'pf_core_redis':
                                $value->value = (!empty($redis['enabled']) ? $redis['enabled'] : 0);
                                break;
                        }
                    }

                    $settings[$key] = [
                        'info' => _p($value->info),
                        'value' => $value->value,
                        'type' => $value->type,
                        'group_class' => (isset($value->group_class) ? $value->group_class : false),
                        'option_class' => (isset($value->option_class) ? $value->option_class : false),
                        'options' => $value->options,
                        'description' => (empty($value->description) ? '' : _p($value->description))
                    ];
                }
            }

            $userGroups = Phpfox::getService('user.group')->get();
            $userGroupSettings = [];
            if ($App->user_group_settings) {
                foreach ($userGroups as $group) {

                    $userGroupSettings[$group['user_group_id']] = [
                        'id' => $group['user_group_id'],
                        'name' => $group['title'],
                        'settings' => []
                    ];

                    foreach ($App->user_group_settings as $key => $value) {
                        if (!isset($value->type)) {
                            $value->type = 'input:text';
                        }

                        if (!isset($value->value)) {
                            $value->value = '';
                        }
                        if (user($key) !== null) {
                            $userGroupValue = user($key, null, $group['user_group_id']);
                        }   else {
                            if (is_object($value->value) && isset($value->value->{$group['user_group_id']})){
                                $userGroupValue = $value->value->{$group['user_group_id']};
                            } elseif (is_object($value->value)){
                                $userGroupValue = $value->value->{2};
                            } else {
                                $userGroupValue = $value->value;
                            }
                        }
                        if ($value->type == 'currency') {
                            //Get all currency
                            $aCurrencyValue = unserialize($userGroupValue);
                            foreach ($aCurrencies as $sCurrencyKey => $aCurrency) {
                                if (!isset($aCurrencyValue[$sCurrencyKey])) {
                                    $aCurrencyValue[$sCurrencyKey] = [
                                        'name' => $aCurrency['name'],
                                        'value' => 0,
                                        'symbol' => $aCurrency['symbol'],
                                    ];
                                } else {
                                    $aCurrencyValue[$sCurrencyKey] = [
                                        'name' => $aCurrency['name'],
                                        'value' => $aCurrencyValue[$sCurrencyKey],
                                        'symbol' => $aCurrency['symbol'],
                                    ];
                                }
                            }
                            $userGroupValue = $aCurrencyValue;
                        }
                        $userGroupSettings[$group['user_group_id']]['settings'][$key] = [
                            'info' => _p($value->info),
                            'value' => $userGroupValue,
                            'type' => $value->type,
                            'group_class' => (isset($value->group_class) ? $value->group_class : false)
                        ];
                    }
                }
            }
            if ($settings) {
                $menus[_p('Settings')] = [
                    'url' => $this->url()->makeUrl('admincp.app.settings', ['id' => $sAppId])
                ];
            }

            if ($userGroupSettings) {
                $menus[_p('User Group Settings')] = [
                    'url' => $this->url()->makeUrl('admincp.app.groupsettings', ['id' => $sAppId]),
                    'is_active' => true
                ];
            }

            if ($App->admincp_menu) {
                foreach ($App->admincp_menu as $key => $value) {
                    $menus[$key] = [
                        'url' => ($value == '#' ? $this->url()->makeUrl('admincp.app', ['id' => $sAppId]) : $this->url()->makeUrl('admincp.' . trim($value, '/')))
                    ];
                }
            }

            $this->template()->assign([
                'sSectionTitle' => $App->name,
                'aSectionAppMenus' => $menus,
                'ActiveApp' => $App,
                'settings' => $settings,
                'userGroupSettings' => $userGroupSettings
            ]);

            if ($App->id == 'PHPfox_Core') {
                $this->template()->clean('aSectionAppMenus');
                $this->template()->setSectionTitle(_p('Settings'));
            }

            $export_path = $this->url()->makeUrl('admincp.app', ['id' => $App->id, 'export' => '1']);

        } else {

            $moduleName = str_replace('__module_', '', $App->id);
            if (Phpfox::getService('admincp.apps')->isDefault($moduleName)){
                $this->url()->send('admincp.' . $moduleName);
            }
            $moduleName = Phpfox_Database::instance()->escape($moduleName);
            $productName = Phpfox_Database::instance()->select('product_id')
                ->from(':module')
                ->where('module_id="' . $moduleName . '"')
                ->execute('getSlaveField');

            $xml_path = PHPFOX_DIR_MODULE . $moduleName . '/phpfox.xml';
            if (file_exists($xml_path)) {
                $this_module = \Phpfox_Xml_Parser::instance()->parse($xml_path);
                if (isset($this_module['data']) && isset($this_module['data']['product_id'])) {
                    $product_xml_path = PHPFOX_DIR_INCLUDE . 'xml/' . $this_module['data']['product_id'] . '.xml';
                    if (file_exists($product_xml_path)) {
                        $this_product = \Phpfox_Xml_Parser::instance()->parse($product_xml_path);
                        if (isset($this_product['data']) && isset($this_product['data']['store_id'])) {
                            $App->store_id = $this_product['data']['store_id'];
                            $App->version = $this_product['data']['version'];
                        }
                    }
                }
            }

            if ($productName) {
                $export_path = $this->url()->makeUrl('admincp.product.file', ['export' => $productName, 'extension' => 'xml']);
                $uninstallUrl = $this->url()->makeUrl('admincp.product', ['delete' => $productName, 'app' => 1]);
            } else {
                $export_path = '';
            }
        }

        $customContent = $App->admincp_route;
        if ($customContent) {
            $customContent = url($customContent, $this->request()->getArray('val'));
        }
        //Ftp block
        $listMethod = [
            "sftp_ssh" => _p('sftp_ssh'),
            "ftp" => _p('ftp'),
            "file_system" => _p('file_system')
        ];
        $currentUploadMethod = Phpfox::getParam('core.upload_method');
        $currentHostName = Phpfox::getParam('core.ftp_host_name');
        $currentPort = Phpfox::getParam('core.ftp_port');
        $currentUsername = Phpfox::getParam('core.ftp_user_name');
        $currentPassword = Phpfox::getParam('core.ftp_password');
        $this->template()->assign([
            'listMethod' => $listMethod,
            'currentUploadMethod' => $currentUploadMethod,
            'currentHostName' => $currentHostName,
            'currentPort' => $currentPort,
            'currentUsername' => $currentUsername,
            'currentPassword' => $currentPassword,
        ]);
        //End Ftp block

        $store = null;
        $has_upgrade = false;
        if (isset($App->store_id) && $App->store_id) {
            $store = json_decode(@fox_get_contents('https://store.phpfox.com/product/' . $App->store_id . '/view.json'), true);
            if (isset($store['id']) && version_compare($App->version, $store['version'], '<')) {
                $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
                $response = $Home->admincp(['return' => $this->url()->makeUrl('admincp.app.add')]);
                $store['install_url'] = $store['url'] . '/installing?iframe-mode=' . $response->token;
                $has_upgrade = true;
            }
        }
        $aVals = $this->request()->getArray('val');

        $extra = '';
        if ($this->request()->get('group') == 'core_cache_driver') {
            $driver = \Phpfox_Cache::driver();
            $extra = '<div class="message" style="display:inline-block;">' . _p('Active Cache Driver:') . ' ' . $driver . '</div>';
        }

        $this->template()
            ->setTitle($App->name)
            ->setBreadCrumb(_p('User Group Settings'), $this->url()->makeUrl('admincp.app.groupsettings', ['id' => $App->id]))
            ->assign([
                'App' => $App,
                'uninstall' => $this->request()->get('uninstall'),
                'uninstallUrl' => $uninstallUrl,
                'disableUrl' => $this->url()->makeUrl('admincp.app', ['id' => $App->id, 'disable' => 'yes']),
                'enableUrl' => $this->url()->makeUrl('admincp.app', ['id' => $App->id, 'enable' => 'yes']),
                'customContent' => $customContent,
                'store' => $store,
                'has_upgrade' => $has_upgrade,
                'export_path' => $export_path,
                'group_class' => $this->request()->get('group'),
                'extraParams' => empty($aVals) ? 0 : 1,
                'appUrl' => url('admincp.app', ['id' => $App->id]),
                'extra' => $extra,
                'aCurrencies' => $aCurrencies,
                'bNoAjaxMenu' => $bNoAjaxMenu
            ]);
        return null;
    }
}
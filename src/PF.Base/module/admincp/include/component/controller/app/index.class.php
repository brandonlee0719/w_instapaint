<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Admincp_Component_Controller_App_Index
 */
class Admincp_Component_Controller_App_Index extends Phpfox_Component
{
    public function process()
    {

        $sAppId = $this->request()->get('id');

        //Verify upgrade
        if ($this->request()->get('verify')) {
            if ($appInit = \Core\Lib::appInit($this->request()->get('id'))) {
                $appInit->processInstall();
            }
            if ($this->request()->get('home')) {
                Phpfox::addMessage(_p('the_app_has_been_re_validated'));
                $this->url()->send('admincp.apps');
            } else {
                $this->url()->send('admincp.app', ['id' => $sAppId]);
            }
        }

        if (empty($sAppId)) {
            $sAppId = Phpfox::isAppAlias($this->request()->get('req2'), true);
        }

        if ((strpos('__module_', $sAppId) !== false) && !Phpfox::isApps($sAppId)) {
            $this->url()->send('admincp.apps');
        }

        try {
            $App = (new Core\App())->get($sAppId);
        } catch (\Exception $e) {
            return Phpfox_Error::display($e->getMessage());
        }

        $uninstallUrl = $this->url()->makeUrl('admincp.app', ['id' => $App->id, 'uninstall' => 'yes']);
        if (!$App->is_module) {
            if (($val = $this->request()->get('val')) && $this->request()->get('uninstall')) {
                $bRemoveDb = isset($val['rmdb']) ? $val['rmdb'] : false;
                if (!($error = Phpfox::getService('user.auth')->loginAdmin($val['email'], $val['password']))) {
                    throw new \Exception(implode('', Phpfox_Error::get()));
                }
                if ($appInit = \Core\Lib::appInit($this->request()->get('id'))) {
                    $appInit->uninstall($bRemoveDb);
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

            if ($App->id == 'PHPfox_Core') {
                $this->template()->clean('aSectionAppMenus');
                $this->template()->setSectionTitle(_p('Settings'));
            }

            $export_path = $this->url()->makeUrl('admincp.app', ['id' => $App->id, 'export' => '1']);
        } else {
            $moduleName = str_replace('__module_', '', $App->id);
            if (Phpfox::getService('admincp.apps')->isDefault($moduleName)) {
                $this->url()->send('admincp.' . $moduleName);
            }

            $moduleName = Phpfox_Database::instance()->escape($moduleName);
            $productName = Phpfox_Database::instance()->select('product_id')
                ->from(':module')
                ->where('module_id="' . $moduleName . '"')
                ->execute('getSlaveField');

            if ($productName && $this->request()->get('uninstall')) {
                $this->url()->send('admincp.product', ['delete' => $productName, 'app' => 1]);
            }

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
                $export_path = $this->url()->makeUrl('admincp.product.file',
                    ['export' => $productName, 'extension' => 'xml']);
                $uninstallUrl = $this->url()->makeUrl('admincp.product', ['delete' => $productName, 'app' => 1]);
            } else {
                $export_path = '';
            }
        }



        $customContent = $App->getAdmincpRoute();

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
            $store = json_decode(@fox_get_contents('https://store.phpfox.com/product/' . $App->store_id . '/view.json'),
                true);
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
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb($App->name, $this->url()->makeUrl('admincp.app', ['id' => $App->id]))
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
            ]);

        return null;
    }
}
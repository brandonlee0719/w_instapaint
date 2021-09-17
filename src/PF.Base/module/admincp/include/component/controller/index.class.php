<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Module_Admincp
 * @version        $Id: index.class.php 7202 2014-03-18 13:38:56Z Raymond_Benc $
 */
class Admincp_Component_Controller_Index extends Phpfox_Component
{
    private $_sController = 'index';

    private $_sModule;

    /**
     * Controller
     */
    public function process()
    {
        $aSkipModules = [
            'user',
            'admincp',
            'theme',
            'like',
            'core',
            'language'
        ];

        $sCoreModules = [
            'user',
            'feed',
            'theme',
            'core',
            'language',
            'announcement'
        ];

        $aHideSettingButtonModules = [
            'custom'
        ];

        // check authorization
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        // allow http base auth
        if (Phpfox::getParam('core.admincp_http_auth')) {
            $aAuthUsers = Phpfox::getParam('core.admincp_http_auth_users');

            if ((isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($aAuthUsers[Phpfox::getUserId()])) && (($_SERVER['PHP_AUTH_USER'] == $aAuthUsers[Phpfox::getUserId()]['name']) && ($_SERVER['PHP_AUTH_PW'] == $aAuthUsers[Phpfox::getUserId()]['password']))) {
            } else {
                header("WWW-Authenticate: Basic realm=\"AdminCP\"");
                header("HTTP/1.0 401 Unauthorized");
                exit("NO DICE!");
            }
        }

        // check root url
        if ('admincp' != $this->request()->get('req1')) {
            return Phpfox_Module::instance()->setController('error.404');
        }

        // check active login session
        if (!Phpfox::getService('user.auth')->isActiveAdminSession()) {
            return Phpfox_Module::instance()->setController('admincp.login');
        }

        // check if upgrade process
        if ($this->request()->get('upgraded')) {
            Phpfox::getLib('cache')->remove();
            Phpfox::getLib('template.cache')->remove();

            $this->url()->send('admincp');
        }

        $this->_sModule = (($sReq2 = $this->request()->get('req2')) ? strtolower($sReq2) : 'admincp');

        if ($this->_sModule == 'logout') {
            $this->_sController = $this->_sModule;
            $this->_sModule = 'admincp';
        } else {
            $this->_sController = (($sReq3 = $this->request()->get('req3')) ? $sReq3 : $this->_sController);
        }
        if ($sReq4 = $this->request()->get('req4')) {
            $sReq4 = str_replace(' ', '', strtolower(str_replace('-', ' ', $sReq4)));
        }
        $sReq5 = $this->request()->get('req5');

        $bPass = false;
        if (file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . '.class.php')) {
            $this->_sController = 'admincp.' . $this->_sController;
            $bPass = true;
        }

        if (!$bPass && $sReq5 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . PHPFOX_DS . $sReq5 . '.class.php')) {
            $this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4 . '.' . $sReq5;
            $bPass = true;
        }

        if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . '.class.php')) {
            $this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4;
            $bPass = true;
        }

        if (!$bPass && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $this->_sController . '.class.php')) {
            $this->_sController = 'admincp.' . $this->_sController . '.' . $this->_sController;
            $bPass = true;
        }

        if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . '.class.php')) {
            $this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4;
            $bPass = true;
        }

        if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . PHPFOX_DS . 'index.class.php')) {
            $this->_sController = 'admincp.' . $this->_sController . '.' . $sReq4 . '.index';
            $bPass = true;
        }

        if (!$bPass && file_exists(PHPFOX_DIR_MODULE . $this->_sModule . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . 'admincp' . PHPFOX_DS . $this->_sController . PHPFOX_DS . 'index.class.php')) {
            $this->_sController = 'admincp.' . $this->_sController . '.index';
            $bPass = true;
        }

        if (!$bPass && file_exists(PHPFOX_DIR_MODULE . 'admincp' . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . $this->_sModule . PHPFOX_DS . $this->_sController . '.class.php')) {
            $this->_sController = $this->_sModule . '.' . $this->_sController;
            $this->_sModule = 'admincp';
            $bPass = true;
        }

        if (!$bPass && $sReq4 && file_exists(PHPFOX_DIR_MODULE . 'admincp' . PHPFOX_DS . PHPFOX_DIR_MODULE_COMPONENT . PHPFOX_DS . 'controller' . PHPFOX_DS . $this->_sModule . PHPFOX_DS . $this->_sController . PHPFOX_DS . $sReq4 . '.class.php')) {
            $this->_sController = $this->_sModule . '.' . $this->_sController . '.' . $sReq4;
            $this->_sModule = 'admincp';
            $bPass = true;
        }

        if (!$bPass && Phpfox::isAppAlias($this->request()->get('req2'))) {
            $this->_sController = 'admincp.' . $this->_sController;
            $bPass = true;
        }

        if (!$bPass && Phpfox::isModule($this->request()->segment('req2'))) {
            $this->_sModule = 'admincp';
            $this->_sController = 'app.index';
            $bPass = true;
        }

        list($aGroups, $aModules,) = Phpfox::getService('admincp.setting.group')->get();

        $aCache = $aGroups;
        $aGroups = [];

        foreach ($aCache as $key => $value) {

            $n = $key;
            switch ($value['group_id']) {
                case 'cookie':
                    $n = _p('browser_cookies');
                    break;
                case 'site_offline_online':
                    $n = _p('toggle_site');
                    break;
                case 'general':
                    $n = _p('site_settings');
                    break;
                case 'mail':
                    $n = _p('mail_server');
                    break;
                case 'spam':
                    $n = _p('spam_assistance');
                    break;
                case 'registration':
                    continue 2;
                    break;
            }

            $aGroups[$n] = $value;
        }
        ksort($aGroups);

        $aSettings = [];
        foreach ($aGroups as $sGroupName => $aGroupValues) {
            $aSettings[$sGroupName] = $this->url()->makeUrl('admincp.setting.edit',
                ['group-id' => $aGroupValues['group_id']]);
        }

        (($sPlugin = Phpfox_Plugin::get('admincp.component_controller_index_process_menu')) ? eval($sPlugin) : false);

        $aUser = Phpfox::getUserBy();

        $sSectionTitle = '';
        $app = $this->request()->get('req2');
        if ($app == 'app') {
            $app = str_replace('__module_', '', $this->request()->get('id'));
        }

        $is_settings = false;
        $is_group_settings = false;
        if ($this->url()->getUrl() == 'admincp/setting/edit') {
            $app = $this->request()->get('module-id');
            $is_settings = true;
        } elseif ($this->url()->getUrl() == 'admincp/user/group/add') {
            $app = $this->request()->get('module');
            $is_group_settings = true;
        }

        $searchSettings = Phpfox::getService('admincp')->getForSearch();
        $this->template()->setHeader('<script>var admincpSettings = ' . json_encode($searchSettings) . ';</script>');

        if (in_array($app, $aSkipModules) && !(in_array($app, $sCoreModules))) {
            $iGroupId = $this->request()->get('group_id');
            $bSetting = $this->request()->get('setting');
            if (!$iGroupId || !$bSetting) {
                $this->url()->send('admincp');
            }
        }

        if (Phpfox::isAppAlias($app) or Phpfox::isApps($app)) {
            $sAppId = $app;
            if (!$is_settings && $this->url()->getUrl() == 'admincp/app/settings') {
                $is_settings = true;
            } elseif (!$is_group_settings && $this->url()->getUrl() == 'admincp/app/groupsettings') {
                $is_group_settings = true;
            }

            if (Phpfox::isAppAlias($app)) {
                $sAppId = Phpfox::getAppId($app);
            }

            $App = (new Core\App())->get($sAppId);
            $oAppInit = Core\Lib::appInit($sAppId);
            $aSectionMenus = [];
            $aActionMenus = [];

            $sAppAlias = $oAppInit->alias ? $oAppInit->alias : $oAppInit->id;
            if ($App->settings) {
                $aSectionMenus[_p('Settings')] = [
                    'url' => $this->url()->makeUrl('admincp.setting.edit', ['module-id' => $sAppAlias]),
                    'is_active' => $is_settings,
                ];
            }

            if ($App->user_group_settings) {
                $aSectionMenus[_p('User Group Settings')] = [
                    'url' => $this->url()->makeUrl('admincp.user.group.add',
                        ['group_id' => 2, 'module' => $sAppAlias, 'setting' => 1, 'hide_app' => 1]),
                    'is_active' => $is_group_settings,
                ];
            }

            if ($App->admincp_menu) {
                foreach ($App->admincp_menu as $key => $value) {
                    $sUrl = ($value == '#' ? $this->url()->makeUrl('admincp.app',
                        ['id' => $sAppId]) : $this->url()->makeUrl('admincp.' . trim($value, '/')));
                    $aSectionMenus[_p($key)] = [
                        'cmd'=> $App->admin_cp_menu_ajax?'admincp.ajax_menu': null,
                        'url' => $sUrl,
                        'is_ajax'=>true,
                    ];
                    if ($this->url()->current() == $sUrl) {
                        $aSectionMenus[_p($key)]['is_active'] = true;
                    }
                }
            }

            if ($App->admincp_action_menu) {
                foreach ($App->admincp_action_menu as $key => $value) {
                    $aActionMenus[$value] = [
                        'url' => $this->url()->makeUrl($key),
                        'class' => 'popup'
                    ];
                }
            }

            if((count($aSectionMenus) == 1 and $App->settings)){
//                $aSectionMenus = [];
            }

            $this->template()
                ->setActionMenu($aActionMenus)
                ->assign([
                    'sSectionTitle' => $App->name,
                    'aSectionAppMenus' => $aSectionMenus,
                    'ActiveApp' => $App,
                ]);
        } elseif ($app && Phpfox::isModule($app) && !in_array($app, $aSkipModules)) {

            $oApp = (new Core\App())->get('__module_' . $app);
            $app = Phpfox_Module::instance()->get($app);
            $name = ($oApp && $oApp->name) ? $oApp->name : Phpfox_Locale::instance()->translate($app['module_id'],
                'module');
            $sSectionTitle = $name;
            $menu = unserialize($app['menu']);
            $aSectionMenus = [];
            $current = $this->url()->getUrl();
            $infoActive = false;

            if ($this->request()->get('req2') == 'app') {
                $infoActive = true;
            }

            if (!in_array($app['module_id'],
                    $aHideSettingButtonModules) && Phpfox::getService('admincp.setting')->moduleHasSettings($app['module_id'])) {
                $aSectionMenus[_p('settings')] = [
                    'is_active' => $is_settings,
                    'url' => $this->url()->makeUrl('admincp.setting.edit', ['module-id' => $app['module_id']])
                ];
            }

            if (is_array($menu) && count($menu)) {
                foreach ($menu as $key => $value) {
                    $is_active = false;
                    $url = 'admincp.' . implode('.', $value['url']);
                    if ($current == str_replace('.', '/', $url)) {
                        $is_active = true;
                        if ($infoActive) {
                            $aSectionMenus['Info']['is_active'] = false;
                        }
                    }

                    $aSectionMenus[_p($key)] = [
                        'url' => $url,
                        'is_active' => $is_active
                    ];
                }
            }
            $this->template()->assign([
                'aSectionAppMenus' => $aSectionMenus,
                'ActiveApp' => (new Core\App())->get('__module_' . $app['module_id'])
            ]);
        }



        $this->template()->assign([
            'sSectionTitle' => $sSectionTitle,
            'aModulesMenu' => $aModules,
            'sLastOpenMenuId' => Phpfox::getCookie('admin_open_sub_menu'),
            'aUserDetails' => $aUser,
            'sPhpfoxVersion' => Phpfox::getVersion(),
            'sSiteTitle' => Phpfox::getParam('core.site_title'),
        ])->setHeader([
            'menu.css' => 'style_css',
            'menu.js' => 'style_script',
            'admin.js' => 'static_script',
            'drag.js' => 'static_script',
            'jquery/plugin/jquery.mosaicflow.min.js' => 'static_script'
        ]);

        if(!$this->request()->getHeader('popup')){
          $this->template()->setTitle(_p('admin_cp'));
        }

        if (Phpfox::demoMode()) {
            return Phpfox_Module::instance()->setController('admincp.demo');
        }

        if (in_array($app, ['plugin', 'module', 'component'])) {
            $this->template()->setSectionTitle(_p('techie') . ': ' . ucwords($app));
            $this->template()->setActionMenu([
                _p('New ') . ucwords($app) => [
                    'url' => $this->url()->makeUrl('admincp.' . $app . '.add'),
                    'class' => 'popup'
                ]
            ]);
        }
        if ($bPass) {
            if (Phpfox::isModule($this->_sModule) || Phpfox::isAppAlias($this->_sModule)) {
                Phpfox_Module::instance()->setController($this->_sModule . '.' . $this->_sController);
            } else {
                $this->url()->send('admincp.apps');
            }

            $sMenuController = str_replace(array('.index', '.phrase'), '',
                'admincp.' . ($this->_sModule != 'admincp' ? $this->_sModule . '.' . str_replace('admincp.', '',
                        $this->_sController) : $this->_sController));
            $aCachedSubMenus = array();
            $sActiveSideBar = '';

            if ($sMenuController == 'admincp.setting.edit') {
                $sMenuController = 'admincp.setting';
            }

            if ($this->_getMenuName() !== null) {
                $sMenuController = $this->_getMenuName();
            }

            $this->template()->assign([
                'aCachedSubMenus' => $aCachedSubMenus,
                'sActiveSideBar' => $sActiveSideBar,
                'bIsModuleConnection' => false,
                'sMenuController' => $sMenuController,
                'aActiveMenus' => ((false && isset($aCachedSubMenus[$sActiveSideBar])) ? $aCachedSubMenus[$sActiveSideBar] : array())
            ]);
        } else {
            if ($this->_sModule != 'admincp') {
                Phpfox_Module::instance()->setController('error.404');
            } else {
                Phpfox::getService('admincp')->check();

                $expires = 0;
                if (defined('PHPFOX_TRIAL_EXPIRES')) {
                    $expires = PHPFOX_TRIAL_EXPIRES;
                }

                $this->template()->setBreadCrumb(_p('dashboard'))
                    ->setTitle(_p('dashboard'))
                    ->assign(array(
                            'bIsModuleConnection' => false,
                            'bIsDashboard' => true,
                            'aNewProducts' => Phpfox::getService('admincp.product')->getNewProductsForInstall(),
                            'is_trial_mode' => defined('PHPFOX_TRIAL_MODE'),
                            'expires' => $expires
                        )
                    );
            }
        }

        if('admincp.index' == Phpfox::getLib('module')->getFullControllerName()){
            $this->template()->clearBreadCrumb()->setActiveMenu('admincp.dashboard');
        }

        $this->template()->setHeader([
            'bootstrap.min.js' => "static_script"
        ]);


        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('admincp.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
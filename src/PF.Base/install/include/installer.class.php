<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_NO_APPS', true);

class Phpfox_Installer
{
    private $_oTpl = null;
    private $_oReq = null;
    private $_sUrl = 'install';
    private $_sStep = 'start';
    private $_sSubStep = '';
    private $_bUpgrade = false;
    private $logFilename;
    private $logMessageFilename;
    private $bHandleFatalError = true;

    private static $_aPhrases = [];

    /**
     * @var array
     */
    private $_aVersions = [];

    /**
     * @var array
     */
    private $_aRequiredApps = [];

    /**
     * @var array
     */
    private $_aDefaultApps = [];

    private $_sTempDir = '';

    private $_sSessionFile = '';

    private $_hFile = null;

    private $_aOldConfig = [];

    private $_sPage = '';

    private static $_sSessionId = null;

    /**
     * @var Phpfox_Database_Driver_Mysql
     */
    public $db;

    public function __construct()
    {
        // increase execution time

        @header('Cache-Control: no-cache');
        @header('Pragma: no-cache');

        $this->logFilename = PHPFOX_DIR . 'file' . PHPFOX_DS . 'log' . PHPFOX_DS . 'installation.log';
        $this->logMessageFilename = PHPFOX_DIR . 'file' . PHPFOX_DS . 'log' . PHPFOX_DS . 'installation_message.log';

        if (!session_id()) {
            session_start();
        }

        if (function_exists('ini_set')) {
            ini_set('memory_limit', -1);
            ini_set('log_errors_max_len', 2048);
        }

        if (function_exists('set_time_limit')) {
            set_time_limit(600);
        }

        $this->initErrorHandlers();

        // load default apps
        $this->_aVersions =  include __DIR__ . PHPFOX_DS . 'version.php';
        $this->_aDefaultApps = include __DIR__ . PHPFOX_DS . 'app.default.php';
        $this->_aRequiredApps =  include __DIR__ . PHPFOX_DS . 'app.require.php';


        $this->_oTpl = Phpfox_Template::instance();
        $this->_oReq = Phpfox_Request::instance();
        $this->_oUrl = Phpfox_Url::instance();


        if (file_exists(PHPFOX_DIR_SETTINGS . 'license.sett.php')) {
            require_once PHPFOX_DIR_SETTINGS . 'license.sett.php';
        } elseif (file_exists(PHPFOX_DIR_SETTINGS . 'license.php')) {
            require_once PHPFOX_DIR_SETTINGS . 'license.php';
        }

        $this->_sTempDir = Phpfox_File::instance()->getTempDir();

        $this->_sPage = $this->_oReq->get('page');
        $this->_sUrl = ($this->_oReq->get('req1') == 'upgrade' ? 'upgrade' : 'install');
        self::$_sSessionId = ($this->_oReq->get('sessionid') ? $this->_oReq->get('sessionid') : uniqid());

        if (isset($_GET['phpfox-upgrade']) && !defined('PHPFOX_IS_UPGRADE')) {
            define('PHPFOX_IS_UPGRADE', true);
        }

        if (defined('PHPFOX_IS_UPGRADE')) {
            $this->_oTpl->assign('bIsUprade', true);
            $this->_bUpgrade = true;

            if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php')) {
                $_CONF = [];
                require_once(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php');

                $this->_aOldConfig = $_CONF;
            }
        }

        if (!Phpfox_File::instance()->isWritable($this->_sTempDir)) {
            if (PHPFOX_SAFE_MODE) {
                $this->_sTempDir = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS;
                if (!Phpfox_File::instance()->isWritable($this->_sTempDir)) {
                    exit('Unable to write to temporary folder: ' . $this->_sTempDir);
                }
            } else {
                exit('Unable to write to temporary folder: ' . $this->_sTempDir);
            }
        }

        $this->_sSessionFile = $this->_sTempDir . 'installer_' . ($this->_bUpgrade ? 'upgrade_' : '') . '_'
            . self::$_sSessionId . '_' . 'phpfox.log';

        $this->_hFile = fopen($this->_sSessionFile, 'a');

        if ($this->_sUrl == 'install' && $this->_oReq->get('req2') == '') {
            if (file_exists(PHPFOX_DIR_SETTING . 'server.sett.php')) {
                require(PHPFOX_DIR_SETTING . 'server.sett.php');

                if (isset($_CONF['core.is_installed']) && $_CONF['core.is_installed'] === true) {
                    $this->_oUrl->forward('../install/index.php?' . PHPFOX_GET_METHOD . '=/upgrade/');
                }
            }

            if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php')) {
                $this->_oUrl->forward('../install/index.php?' . PHPFOX_GET_METHOD . '=/upgrade/');
            }
        }

        // Define some needed params
        Phpfox::getLib('setting')->setParam([
                'core.path'              => self::getHostPath(),
                'core.url_static_script' => self::getHostPath() . 'static/jscript/',
                'core.url_static_css'    => self::getHostPath() . 'static/style/',
                'core.url_static_image'  => self::getHostPath() . 'static/image/',
                'sCookiePath'            => '/',
                'sCookieDomain'          => '',
                'bAllowHtml'             => false,
                'core.url_rewrite'       => '2',
            ]
        );

        if ((!defined('PHPFOX_LICENSE_ID') || !PHPFOX_LICENSE_ID) && file_exists(PHPFOX_DIR_SETTINGS . 'license.php')) {
            require_once PHPFOX_DIR_SETTINGS . 'license.php';
        }

        $this->_includeAutoLoad();
    }

    public static function getSessionId()
    {
        return self::$_sSessionId;
    }

    /**
     * Get host path
     *
     * @since 4.6.0 fix issue install from https on ec2, ...
     *
     * @return string
     */
    public static function getHostPath()
    {
        $protocol = 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $protocol = 'https';
        } elseif (isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] == 443) {
            $protocol = 'https';
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https';
        } elseif (isset($_SERVER['HTTP_CF_VISITOR']) and strpos($_SERVER['HTTP_CF_VISITOR'], 'https')) {
            $protocol = 'https';
        }

        $parts = explode('index.php', $_SERVER['PHP_SELF']);

        return $protocol . '://' . $host . $parts[0];
    }

    public static function getPhrase($sVar)
    {
        return (isset(self::$_aPhrases[$sVar]) ? self::$_aPhrases[$sVar] : '');
    }

    public function run()
    {


        // support index.php instead of template

        if (!isset($_GET['_ajax'])) {
            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'index.php';
            include($file);
            exit;
        }

        if (!is_dir(PHPFOX_DIR . 'file/')) {
            mkdir(PHPFOX_DIR . 'file/', 0777);
            file_put_contents(PHPFOX_DIR . 'file/.htaccess', 'Options -Indexes');
            touch(PHPFOX_DIR . 'file/index.html');
        }

        if ($this->_bUpgrade
            && (int)substr($this->_getCurrentVersion(), 0, 1) < 2
            && file_exists(PHPFOX_DIR . '.htaccess')
        ) {
            $sHtaccessContent = file_get_contents(PHPFOX_DIR . '.htaccess');
            if (preg_match('/RewriteEngine/i', $sHtaccessContent)) {
                exit('In order for us to continue with the upgrade you will need to rename or remove the file ".htaccess".');
            }
        }

        $sStep = ($this->_oReq->get('step') ? strtolower($this->_oReq->get('step')) : 'start');
        $this->log('running step: ' . $sStep);
        $this->log(var_export($_REQUEST,true));

        if (!$sStep) {
            $sStep = 'start';
        }

        if (strpos($sStep, '.')) {
            list($sStep, $sSubStep) = explode('.', $sStep, 2);
        } else {
            $sSubStep = '';
        }

        $sMethod = '_' . $sStep;
        $this->_sStep = $sStep;
        $this->_sSubStep = $sSubStep;

        $this->_oTpl->assign([
            'sUrl' => $this->_sUrl,
        ]);

        if (method_exists($this, $sMethod)) {
            $data = call_user_func([&$this, $sMethod]);
            if (!Phpfox_Error::isPassed()) {
                $data = [
                    'errors' => Phpfox_Error::get(),
                ];
            }

            if ($sStep != 'start' && !is_array($data)) {
                $content = $this->_oTpl->getLayout($sStep, true);
                $data = [
                    'content' => $content,
                ];
            }

        } else {
            $data = [
                'errors' => 'Invalid steps',
            ];
        }

        if (is_array($data)) {
            header('Content-type: application/json');
            echo json_encode($data);
            exit;
        }
    }

    ########################
    # Special Module Install Routines
    ########################

    public function _video($bInstall = false)
    {
        $sFfmpeg = '';
        $sMencoder = '';
        $iPass = 0;
        if (!PHPFOX_SAFE_MODE) {
            if (($aVals = $this->_oReq->getArray('val'))) {
                if (!empty($aVals['ffmpeg'])) {
                    exec($aVals['ffmpeg'] . ' 2>&1', $aOutput);

                    if (preg_match("/FFmpeg version/", $aOutput[0])) {
                        if ($bInstall === true) {
                            $this->_db()->update(Phpfox::getT('setting'), ['value_actual' => $aVals['ffmpeg']],
                                'module_id = \'video\' AND var_name = \'ffmpeg_path\'');
                        } else {
                            $_SESSION[Phpfox::getParam('core.session_prefix')]['installer_ffmpeg'] = $aVals['ffmpeg'];
                        }

                        $iPass++;
                    } else {
                        Phpfox_Error::set($aOutput[0]);
                    }
                    unset($aOutput);
                }

                if (!empty($aVals['mencoder'])) {
                    exec($aVals['mencoder'] . ' 2>&1', $aOutput);

                    if (preg_match("/MPlayer Team/", $aOutput[0])) {
                        if ($bInstall === true) {
                            $this->_db()->update(Phpfox::getT('setting'), ['value_actual' => $aVals['mencoder']],
                                'module_id = \'video\' AND var_name = \'mencoder_path\'');
                        } else {
                            $_SESSION[Phpfox::getParam('core.session_prefix')]['installer_mencoder']
                                = $aVals['mencoder'];
                        }

                        $iPass++;
                    } else {
                        Phpfox_Error::set($aOutput[0]);
                    }
                    unset($aOutput);
                }
            }

            if (PHP_OS == 'Linux' && !preg_match('/shell_exec/', ini_get('disable_functions'))) {
                $sOutput = shell_exec('whereis ffmpeg 2>&1');
                $aOutput = explode("\n", $sOutput);
                if (isset($aOutput[0])) {
                    $aParts = explode('ffmpeg:', $aOutput[0]);
                    if (isset($aParts[1])) {
                        $aSubParts = explode(' ', trim($aParts[1]));
                        if (isset($aSubParts[0]) && !empty($aSubParts[0])) {
                            if (PHPFOX_OPEN_BASE_DIR || (!PHPFOX_OPEN_BASE_DIR && file_exists($aSubParts[0]))) {
                                $sFfmpeg = $aSubParts[0];
                            }

                        }
                    }
                }
                unset($aOutput);

                $sOutput = shell_exec('whereis mencoder 2>&1');
                $aOutput = explode("\n", $sOutput);
                if (isset($aOutput[0])) {
                    $aParts = explode('mencoder:', $aOutput[0]);
                    if (isset($aParts[1])) {
                        $aSubParts = explode(' ', trim($aParts[1]));
                        if (isset($aSubParts[0]) && !empty($aSubParts[0])) {
                            if (PHPFOX_OPEN_BASE_DIR || (!PHPFOX_OPEN_BASE_DIR && file_exists($aSubParts[0]))) {
                                $sMencoder = $aSubParts[0];
                            }

                        }
                    }
                }
                unset($aOutput);
            }
        }

        if (!empty($_SESSION[Phpfox::getParam('core.session_prefix')]['installer_ffmpeg'])) {
            $sFfmpeg = $_SESSION[Phpfox::getParam('core.session_prefix')]['installer_ffmpeg'];
        }

        if (!empty($_SESSION[Phpfox::getParam('core.session_prefix')]['installer_mencoder'])) {
            $sMencoder = $_SESSION[Phpfox::getParam('core.session_prefix')]['installer_mencoder'];
        }

        $aForms = [
            'ffmpeg'   => $sFfmpeg,
            'mencoder' => $sMencoder,
        ];

        return $aForms;
    }

    ########################
    # Install/Upgrade Steps
    ########################
    public function _start()
    {
        $this->_oTpl->setTitle('phpFox ' . Phpfox::getVersion());
        $this->bHandleFatalError = false;

        if (!$this->_bUpgrade) {
            $this->log('Installation New PhpFox Site');
        } else {
            $this->log('Upgrading New PhpFox Site');
        }


        if (defined('PHPFOX_TRIAL_MODE') && PHPFOX_TRIAL_MODE) {
            $this->log("Trial Mode Package");
        }

        $errors = $this->_requirement();
        if (is_array($errors)) {
            $this->_oTpl->assign([
                'requirementErrors' => $errors,
            ]);
        }

        if ($_POST && is_array($errors)) {
            foreach ($errors as $error) {
                Phpfox_Error::set($error);
            }
        }

        if (!is_array($errors) && $_POST) {
            return [
                'message' => 'Checking requirements',
                'next'    => 'configuration',
            ];
        }
    }

    public function _key()
    {
        $this->initLog();
        $oValid = Phpfox_Validator::instance()->set([
                'sFormName' => 'js_form',
                'aParams'   => [
                    'license_id' => 'Provide a license ID.',
                    'license_key' => 'Provide a license key.',
                ],
            ]
        );

        $aResult = [];
        if($this->_isTrial()){
            $aResult = [
                'is_valid'=>true,
                'is_trial' => true,
                'license_id' => '',
                'license_key' => '',
                'package_id'=>3,
            ];
        }elseif (($aVals = $this->_oReq->getArray('val')) and $oValid->isValid($aVals)) {
            if ($aVals['license_id'] == 'techie' && $aVals['license_key'] == 'techie') {
                $aResult = [
                    'is_valid'=>true,
                    'license_id' => 'techie',
                    'license_key' => 'techie',
                    'package_id'=>3,
                ];
            } else {
                try{
                    $Home = new Core\Home($aVals['license_id'], $aVals['license_key']);
                    $response = $Home->verify([
                        'url' => $this->getHostPath(),
                    ]);

                    if (!is_object($response)) {
                        $aResult =  [
                            'is_valid'=>false,
                            'error'=> (string)$response,
                        ];
                    }else{
                        $aResult = [
                            'is_valid'    => isset($response->valid) ? !!$response->valid : false,
                            'license_id'  => $aVals['license_id'],
                            'license_key' => $aVals['license_key'],
                            'package_id'  => isset($response->license)?$response->license->package_id:3,
                        ];
                    }
                }catch (\Exception $e){
                    $error = $e->getMessage();
                    $aResult = [
                        'is_valid'=>false,
                        'error'=> $error,
                    ];
                }

            }
        }

        // Connect to phpFox and verify the license
        if (!empty($aResult) and $aResult['is_valid']) {
            $license_id = $aResult['license_id'];
            $license_key = $aResult['license_key'];
            $package_id = $aResult['package_id'];

            $data
                = "<?php define('PHPFOX_LICENSE_ID', '{$license_id}'); define('PHPFOX_LICENSE_KEY', '{$license_key}');";
            $data .= "\n\nif (!defined('PHPFOX_PACKAGE_ID')) {define('PHPFOX_PACKAGE_ID', '{$package_id}');}";

            if(!file_put_contents(PHPFOX_DIR_SETTINGS . 'license.php', $data)){
                $aResult['is_valid'] =  false;
                $aResult['error'] = sprintf('unable to write to configuration file "%s", please check permission and try again.', PHPFOX_DIR_SETTINGS . 'license.php');
            }

            if($this->_bUpgrade){
                if(!@file_put_contents(PHPFOX_DIR_SETTINGS . 'license.sett.php', $data)){
                    $aResult['is_valid'] =  false;
                    $aResult['error'] = sprintf('unable to write to configuration file "%s", please check permission and try again.', PHPFOX_DIR_SETTINGS . 'license.sett.php');
                }
            }
        }

        if(!empty($aResult) and $aResult['is_valid']){
            if ($this->_bUpgrade) {
                return [
                    'message' => 'Check Apps Compatible',
                    'next'    => 'apps',
                ];
            }

            return [
                'message' => 'Verifying license',
                'next'    => 'configuration',
            ];
        }

        if(!empty($aResult) and !$aResult['is_valid']){
            $this->_oTpl->assign([
                    'sError' => $aResult['error']?$aResult['error']: 'Oops, failed to verify license information.',
                ]
            );
        }

        $this->_oTpl->assign([
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'bHasCurl'   => (function_exists('curl_init') ? true : false),
            ]
        );
    }

    public function getInformationFromStore($appIds){
        if ($this->_isTrial()) {
            return [];
        }
        $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
        $response = $Home->products(['products' => ['apps'=>$appIds]]);
        $response  = json_decode(json_encode($response),true);

        return $response['products']['apps'];
    }

    public function _apps()
    {
        (new \Core\Database\Apps())->install();
        $aDefaultApps = [
            'PHPfox_Core',
            'PHPfox_Flavors',
        ];
        if ($aVals = $this->_oReq->getArray('val')) {
            if (isset($aVals['upgrade']) && count($aVals['upgrade'])) {
                $aAppUpgrade = array_keys($aVals['upgrade']);
                $aAppSerialized  = json_decode($aVals['apps_serialized'],true);

                // download app from store or NOT ?
                foreach ($aAppUpgrade as $sKeyAppId) {
                    $info = isset($aAppSerialized[$sKeyAppId])?$aAppSerialized[$sKeyAppId]: null;

                    if(!$info){
                        continue;
                    }

                    $aSteps[] = [
                        'name' => 'install_store_app.download.' . $sKeyAppId,
                        'msg'  => ($info['download']== '#'? 'Extracting': 'Downloading ' ). $info['name'],
                        'data' => [
                            'appid'  => $info['id'],
                            'url'    => $info['download'],
                            'status' => 'download',
                            'name'   => $info['name'],
                        ],
                    ];
                    $aSteps[] = [
                        'name' => 'install_store_app.upgrade.' . $sKeyAppId,
                        'msg'  => 'Upgrading ' . $info['name'],
                        'data' => [
                            'appid'  => $info['id'],
                            'url'    => '#',
                            'status' => 'upgrade',
                            'name'   => $info['name'],
                        ],
                    ];


                }
                //Save options state of App
                return [
                    'steps'   => $aSteps,
                    'message' => 'Entering Ftp',
                    'next'    => 'ftp',
                ];
            } else {
                return [
                    'message' => 'Updating',
                    'next'    => 'update',
                ];
            }
        }
        $Apps = [];
        $aAllApps = (new \Core\App())->getAllAppFromDatabase(true);

        //Check install from version < 4.4.0
        if (!count($aAllApps)) {
            foreach (scandir(PHPFOX_DIR_SITE_APPS) as $appDir) {
                $sLockPath = realpath(PHPFOX_DIR_SITE_APPS . PHPFOX_DS . $appDir . PHPFOX_DS . 'app.lock');
                $sJsonPath = realpath(PHPFOX_DIR_SITE_APPS . PHPFOX_DS . $appDir . PHPFOX_DS . 'app.json');

                if (file_exists($sJsonPath)) {
                    $aJsonApp = json_decode(file_get_contents($sJsonPath));
                    if (array_search($aJsonApp->id, array_column($aAllApps, 'apps_id'))) {
                        continue;
                    }
                    if (in_array($aJsonApp->id, $aDefaultApps)) {
                        continue;
                    }
                    $aAllApps[] = [
                        'apps_key'    => '',
                        'apps_id'     => $aJsonApp->id,
                        'apps_dir'    => $aJsonApp->id,
                        'apps_name'   => $aJsonApp->name,
                        'version'     => $aJsonApp->version,
                        'apps_alias'  => '',
                        'author'      => '',
                        'vendor'      => '',
                        'description' => '',
                        'apps_icon'   => $aJsonApp->icon,
                        'type'        => '',
                        'is_active'   => file_exists($sLockPath),
                        'legacy'      => true,
                    ];
                }
            }
        }

        //Save current state of App
        file_put_contents(PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'upgrade_app_state.log', json_encode($aAllApps));
        //Get Latest versions from store
        $aListCheck = array_merge($aDefaultApps, $aAllApps);
        $appIdList = array_map(function ($item) use ($aListCheck) {
            //Update legacy app
            if (isset($item['legacy']) && $item['legacy']) {
                return $item['apps_id'];
            }

            if (isset($item['apps_id']) and !($item = \Core\Lib::appInit($item['apps_id']))) {
                return null;
            }

            if(!isset($item->id))
                return null;

            return (in_array($item->id, $aListCheck)) ? null : $item->id;
        }, $aListCheck);
        foreach ($appIdList as $keyApp => $value) {
            if (!isset($value) || empty($value)) {
                unset($appIdList[$keyApp]);
            }
        }
        $sendData = ['apps' => $appIdList];

        $response = [];
        if (count($appIdList)) {
            $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
            $response = $Home->products(['products' => $sendData]);
        }
        //End get latest versions

        foreach ($aAllApps as $app) {
            //Update legacy app
            if (isset($app['legacy']) && $app['legacy']) {
                $id = $app['apps_id'];
                if (isset($response->products) && isset($response->products->apps)
                    && isset($response->products->apps->$id)
                    && isset($response->products->apps->$id->version)) {
                    $sAppNewVersion = $response->products->apps->$id->version;
                } else {
                    $sAppNewVersion = 'n/a';
                }
                $Apps[$app['apps_id']] = [
                    'id'                => $app['apps_id'],
                    'name'              => $app['apps_name'],
                    'status'            => $app['is_active'],
                    'is_compatible'     => "No",
                    'required'          => false,
                    'current_version'   => $app['version'],
                    'latest_version'    => $sAppNewVersion,
                    'bUpgradeAvailable' => ($sAppNewVersion != 'n/a'
                        && (version_compare($app['version'],
                            $sAppNewVersion, '<'))) ? true : false,
                ];
                continue;
            }

            if (!$appClass = \Core\Lib::appInit($app['apps_id'])) {
                continue;
            }
            if ($appClass->isCore()) {
                continue;
            }
            //Save new version
            $id = $appClass->id;
            if (isset($response->products) && isset($response->products->apps) && isset($response->products->apps->$id)
                && isset($response->products->apps->$id->version)) {
                $sAppNewVersion = $response->products->apps->$id->version;
            } else {
                $sAppNewVersion = 'n/a';
            }

            $Apps[$id] = [
                'id'                => $id,
                'name'              => $appClass->name,
                'status'            => $appClass->isActive(),
                'is_compatible'     => $appClass->isCompatible() ? "Yes" : "No",
                'current_version'   => $app['version'],
                'latest_version'    => $sAppNewVersion,
                'required'          => false, //!$appClass->isCompatible(),
                'bUpgradeAvailable' => ($sAppNewVersion != 'n/a'
                    && (version_compare($app['version'],
                        $sAppNewVersion, '<'))) ? true : false,
            ];

            //Disable all apps
//            $appClass->disable();
        }

        $this->getShouldUpgradeBuiltInApps($Apps);

        $excludes = $this->getExcludedAppsByPhpFoxPackageId(PHPFOX_PACKAGE_ID);

        foreach($Apps as $index=>$app){
            if(in_array($index, $excludes)){
                $Apps[$index]['hide']  = 1;
            }
        }

        if (count($Apps)) {
            $this->_oTpl->assign([
                'apps' => $Apps,
                'apps_serialized'=> json_encode($Apps),
            ]);
        } else {
            return [
                'next'    => 'done',
            ];
        }
    }

    public function _ftp()
    {
        $listMethod = [
            "file_system" => 'File System (default)',
            "ftp"         => 'FTP',
            "sftp_ssh"    => 'SFTP',
            "key"         => 'SFTP with key',
        ];

        $this->_oTpl->assign([
            'listMethod' => $listMethod,
        ]);

        //get account info
        if ($aVals = $this->_oReq->getArray('val')) {
            if (isset($aVals['back']) && $aVals['back']) {

                return [
                    'message' => 'Check Apps Compatible',
                    'next'    => 'apps',
                ];
            }
            $manager = new \Core\Installation\Manager($aVals);
            if ($manager->verifyFtpAccount()) {
                file_put_contents(PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'upgrade_app_ftp.log', json_encode($aVals));
                return [
                    'message' => 'Updating',
                    'next'    => 'update',
                ];
            } else {
                $sMessage = 'Your ftp account doesn\'t work';
            }
        }
        if (isset($sMessage)) {
            Phpfox_Error::set($sMessage);
            $this->_oTpl->assign([
                'sError' => $sMessage,
            ]);
        }
    }

    public function _license()
    {
        if ($this->_oReq->get('agree')) {
            $this->_pass('requirement');
        }

        $this->_oTpl->assign([
                'bIsUpgrade' => ($this->_sUrl == 'upgrade' ? true : false),
            ]
        );
    }

    /**
     * @param bool $returnErrorMessage
     *
     * @return array|string
     */
    private function _validateDirectoryAndPermissions($returnErrorMessage = false){

        $failed = [];

        $testValidDirs = function($dirs, $fixRoot = false, $target = null) use(&$failed){
            foreach ($dirs as $dir) {
                $dir = $fixRoot ? PHPFOX_ROOT. $dir : $dir;
                if(is_dir($dir) and !is_writable($dir)){
                    $failed[] = $target?: str_replace(PHPFOX_ROOT,'/',$dir);
                    if($target) return;
                }
            }
        };

        $testValidFiles = function($files, $fixRoot = false, $target = null) use(&$failed){
            foreach ($files as $file) {
                $file = $fixRoot ? PHPFOX_ROOT. $file: $file;
                if(file_exists($file) and !is_writable($file)){
                    $failed[] =  $target?:str_replace(PHPFOX_ROOT,'/',$file);
                    if($target) return;
                }
            }
        };

        $testValidDirs([
            PHPFOX_DIR,
            PHPFOX_DIR . 'file',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'log',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'cache',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'settings',
            PHPFOX_DIR_SITE,
            PHPFOX_DIR_SITE . 'Apps',
            PHPFOX_DIR_SITE . 'flavors',
            PHPFOX_DIR_SITE . 'flavors'. PHPFOX_DS . 'bootstrap',
            PHPFOX_DIR_SITE . 'flavors'. PHPFOX_DS . 'bootstrap' .PHPFOX_DS . 'html',
            PHPFOX_DIR_SITE . 'flavors'. PHPFOX_DS . 'bootstrap' .PHPFOX_DS . 'assets',
            PHPFOX_DIR_SITE . 'flavors'. PHPFOX_DS . 'bootstrap' .PHPFOX_DS . 'flavor',
        ],false);

        $testValidFiles([
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'log'. PHPFOX_DS . 'installation.log',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'log'. PHPFOX_DS . 'installer_modules.php',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'settings'. PHPFOX_DS . 'server.sett.php',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'settings'. PHPFOX_DS . 'timezones.sett.php',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'settings'. PHPFOX_DS . 'version.sett.php',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'settings'. PHPFOX_DS . 'license.sett.php',
            PHPFOX_DIR . 'file'. PHPFOX_DS  .'settings'. PHPFOX_DS . 'version.php',
        ],false);


        if(file_exists($directoryInfo = PHPFOX_DIR . 'install'. PHPFOX_DS .'package'. PHPFOX_DS. 'info.directory.php')){
            /** @noinspection PhpIncludeInspection */
            $directoryInfo = include $directoryInfo;
            foreach($directoryInfo as $target=>$info){
                $testValidDirs($info['dirs'],true, $target);
                $testValidFiles($info['files'],true, $target . '/*');
            }
        }

        $failed = array_unique($failed);
        asort($failed);

        if($returnErrorMessage){
            if(empty($failed))
                return null;

            return 'Failed to open directory/files for writing, Change file permission then try again!<br/>'. implode('<br />', array_map(function($v){
                    return '<small>'. trim($v,DIRECTORY_SEPARATOR) . '</small>';
                },$failed));

        }
        return $failed;
    }

    public function _requirement()
    {
        $errors = [];
        $aVerify = [
            '<a href="http://php.net/manual/en/book.mbstring.php" target="_blank">Multibyte String</a>'     => (function_exists('mb_substr')
                ? true : false),
            '<a href="http://php.net/manual/en/book.xml.php" target="_blank">XML Parser</a>'                => (function_exists('xml_set_element_handler')
                ? true : false),
            '<a href="http://php.net/manual/en/book.image.php" target="_blank">PHP GD</a>'                  => ((extension_loaded('gd')
                && function_exists('gd_info')) ? true : false),
            '<a href="http://php.net/manual/en/function.mysqli-connect.php" target="_blank">PHP Mysqli</a>' => ((function_exists('mysqli_connect'))
                ? true : false),
            '<a href="http://php.net/manual/en/class.ziparchive.php" target="_blank">PHP ZipArchive</a>'    => ((class_exists('ZipArchive'))
                ? true : false),
            '<a href="http://php.net/manual/en/book.exec.php" target="_blank">PHP Exec</a>'                 => function_exists('exec'),
            '<a href="http://php.net/manual/en/book.curl.php" target="_blank">PHP CURL</a>'                 => (extension_loaded('curl')
                && function_exists('curl_init')),
        ];

        foreach ($aVerify as $sCheck => $bPassed) {
            if ($bPassed === false) {
                $errors[] = 'PHP module "' . $sCheck . '" is missing.';
            }
        }

        $memory = @ini_get('memory_limit');
        $subString = substr($memory, -1);
        $iString = (int)$memory;
        switch ($subString) {
            case 'K':
                $iString = $iString / 1000;
                break;
            case 'G':
                $iString = $iString * 1000;
                break;
            default:
                break;
        }

        if ($iString > 0 && $iString < 64) {
            $errors[] = 'Your servers memory limit is ' . $memory . '. We require 64MB or higher.';
        }

        $aDrivers = Phpfox::getLib('database.support')->getSupported();
        $aDbChecks = [];
        $iDbs = 0;
        foreach ($aDrivers as $aDriver) {
            $aDbChecks[$aDriver['label']] = $aDriver['available'];
            if ($aDriver['available']) {
                $iDbs++;
            }
        }

        if (!$iDbs) {
            $errors[] = 'No database driver found.';
        }

        if ($this->_bUpgrade && version_compare($this->_getCurrentVersion(), '4.0.0', '>')) {

        }

        $aModuleLists = Phpfox_Module::instance()->getModuleFiles();
        $aModules = array_merge($aModuleLists['core'], $aModuleLists['plugin']);
        foreach ($aModules as $aModule) {
            if (($aFiles = Phpfox_Module::instance()->init($aModule['name'], 'aInstallWritable'))) {
                foreach ($aFiles as $sDir) {
                    $sDir = str_replace('/', PHPFOX_DS, $sDir);
                    if (!is_dir(PHPFOX_DIR . $sDir)) {
                        Phpfox_File::instance()->mkdir(PHPFOX_DIR . $sDir, true, 0777);
                    }
                }
            }
        }

        $aFailed = $this->_validateDirectoryAndPermissions(true);

        if($aFailed){
            $errors[] = $aFailed;
        }

        $errors = array_unique($errors);

        if (!count($errors)) {
            return [
                'next'  => 'done',
            ];
        }

        $this->_oTpl->assign('aErrors', $errors);

    }

    function _load_general_steps(){

        $aSteps =  [];

        if ($this->_bUpgrade) {
            $aSteps[] = [
                'name'    => 'apps',
                'msg'     => 'Checking apps',
                'okLabel' => 'Continue',
            ];
        } else {
            $aSteps[] = [
                'name' => 'configuration',
                'msg' => 'Database Configuration',
                'okLabel' => 'Continue'
            ];
        }

        $aSteps[] = [
            'name' => 'load_more_steps',
            'msg' => 'load_more_steps',
            'okLabel' => 'load_more_steps',
            'showProgress' => false];

        if ($this->_bUpgrade) {
            $aSteps[] = [
                'name'    => 'ftp',
                'msg'     => 'FTP Information',
                'okLabel' => 'Continue',
            ];
        }

        // this step must be before "getdefault" step
        $aSteps[] = [
            'name'=>'extract_builtin_packages',
            'msg'=> 'Extracting built-in packages, please wait ...',
        ];

        if ($this->_bUpgrade) {
            $aSteps[] = [
                'name' => 'upgrade_phpfox_version',
                'msg'  => 'Prepare to upgrade phpFox',
            ];
        }

        if (!$this->_bUpgrade) {
            $aSteps[] = [
                'name'    => 'getdefault',
                'msg'     => 'Getting Default Apps',
                'okLabel' => 'Continue',
            ];
        }

        if (!$this->_bUpgrade) {
            $aSteps[] = [
                'name'    => 'account',
                'msg'     => 'Create Administrator Account',
                'okLabel' => 'Continue',
            ];
        }

        $aSteps[] = [
            'name' => 'prepare_database',
            'msg'  => 'Checking Database',
        ];
        $aSteps[] = [
            'name' => 'import',
            'msg'  => 'Import Languages',
        ];
        $aSteps[] = [
            'name' => 'language',
            'msg'  => 'Import Languages',
        ];
        $aSteps[] = [
            'name' => 'install_module_app',
            'msg'  => 'Preparing Core Apps',
        ];
        $aSteps[] = [
            'name' => 'verify_module_app',
            'msg'  => 'Verifying Apps',
        ];

        if (!$this->_bUpgrade) {
            $aSteps[] = [
                'name' => 'final',
                'msg'  => 'Updating Admin Accounts',
            ];
        }

        $aSteps[] = [
            'name'=> 'update_settings',
            'msg'=> 'Generate Settings',
        ];

        $aSteps[] = [
            'name'=> 'update_db',
            'msg'=> 'Update Database',
        ];

        $aSteps[] =  [
            'name'=> 'install_core_app',
            'msg'=> 'Preparing Apps',
        ];

        foreach ($this->_aRequiredApps as $sAppKey => $aAppInfo) {
            $aSteps[] =  [
                'name' => 'install_core_app.' . $sAppKey,
                'msg'  => 'Installing ' . $aAppInfo['name'],
                'data' => ['appid' => $sAppKey]
            ];
        }

        $aSteps[] = [
            'name'=> 'generate_default_theme',
            'msg'=> 'Generating Default Theme',
        ];

        $aSteps[] = [
            'name'=> 'generate_admin_account',
            'msg'=> 'Generating Admin Account',
        ];

        $aSteps[] = [
            'name'=> 'generate_timezone',
            'msg'=> 'Generating Timezone',
        ];

        $aSteps[] = [
            'name' => 'install_store_app',
            'msg'  => 'Checking selected apps',
        ];

        $aSteps[] = [
            'name'=> 'verify_app_state',
            'msg'=> 'Verifying App State',
        ];

        if(!$this->_bUpgrade){
            $aSteps[] = [
                'name'=> 'generate_material_theme',
                'msg'=> 'Install Material Theme',
            ];
            $aSteps[] = [
                'name'=> 'rebuild_material_theme',
                'msg'=> 'Rebuild Material Theme, Please wait in minutes ...',
            ];
        }else{
            $aSteps[] = [
                'name'=> 'rebuild_bootstrap',
                'msg'=>'Rebuild Bootstrap, please wait in minutes ... ',
            ];
        }

        $aSteps[] = [
            'name'=> 'verify_setting_state',
            'msg'=> 'Verifying Settings',
        ];

        $aSteps[] = [
            'name'    => 'all_done',
            'msg'     => 'All done',
            'okLabel' => 'View Your Site!',
        ];

        return [
            'steps'=>$aSteps,
            'next'=>'done',
        ];
    }

    /**
     * @param $dir
     *
     * @return bool
     */
    function ensureDirectoryWritable($dir)
    {
        $pass = true;
        if (!is_dir($dir)) {
            if (!@mkdir($dir)) {
                $pass = false;
            }

            if (!@chmod($dir, 0777)) {
                $pass = false;
            }
        }
        if (!is_dir($dir)) {
            $pass = false;
        }

        if (!@is_writeable($dir)) {
            $pass = false;

        }
        return $pass;
    }

    /**
     * the host name only if its not oracle.
     *
     */
    public function _configuration()
    {
        Phpfox::getLib('cache')->remove();

        $aExists = [];
        $aForms = [];

        if (defined('PHPFOX_INSTALL_HOST')) {
            $aForms['host'] = PHPFOX_INSTALL_HOST;
            $aForms['name'] = PHPFOX_INSTALL_NAME;
            $aForms['user_name'] = PHPFOX_INSTALL_USER;
        }

        // Get supported database drivers
        $aDrivers = Phpfox::getLib('database.support')->getSupported(true);

        $oValid = Phpfox_Validator::instance()->set([
                'sFormName' => 'js_form',
                'aParams'   => [
                    'prefix'   => 'No database prefix provided.',
                    'sitename' => 'Provide your Site name',
                ],
            ]
        );
        $this->bHandleFatalError = false;
        if ($aVals = $this->_oReq->getArray('val')) {
            if ($oValid->isValid($aVals)) {
                Phpfox::getLibClass('phpfox.database.dba');
                $sDriver = 'phpfox.database.driver.' . strtolower(preg_replace("/\W/i", "", $aVals['driver']));
                if (Phpfox::getLibClass($sDriver)) {
                    $oDb = Phpfox::getLib($sDriver);

                    if ($oDb->connect($aVals['host'], $aVals['user_name'], $aVals['password'], $aVals['name'],
                        $aVals['port'])
                    ) {
                        Phpfox::getLib('session')->set('installer_db', $aVals);
                        // Drop database tables, only if user allows us too
                        if (isset($aVals['drop']) && ($aDrops = $this->_oReq->getArray('table'))) {
                            $oDb->dropTables($aDrops, $aVals);
                        }

                        $oDbSupport = Phpfox::getLib('database.support');

                        $aTables = $oDbSupport->getTables($aVals['driver'], $oDb);

                        $aSql = Phpfox_Module::instance()->getModuleTables($aVals['prefix']);

                        foreach ($aSql as $sSql) {
                            if (in_array($sSql, $aTables)) {
                                $aExists[] = $sSql;
                            }
                        }

                        if (count($aExists)) {
                            Phpfox_Error::set('We have found that the following table(s) already exist:');
                        } else {
                            $aForms = array_merge($this->_video(), $aForms);

                            if (Phpfox_Error::isPassed()) {
                                // Cache modules we need to install
                                $sCacheModules = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'installer_modules.php';
                                if (!empty($_POST['__is_cli'])) {
                                    $aVals['module'] = [];
                                    $base = PHPFOX_DIR . 'module/';
                                    foreach (scandir($base) as $module) {
                                        if (file_exists($base . $module . '/install/phpfox.xml.php')) {
                                            $aVals['module'][] = $module;
                                        }
                                    }
                                }

                                if (file_exists($sCacheModules)) {
                                    unlink($sCacheModules);
                                }

                                $sData = '<?php' . "\n";
                                $sData .= '$aModules = ';
                                $sData .= var_export($aVals['module'], true);
                                $sData .= ";\n\$aSiteConfig = ";
                                $aSiteConfig = [
                                    'site_name' => $aVals['sitename'],
                                ];
                                $sData .= var_export($aSiteConfig, true);
                                $sData .= ";\n?>";
                                Phpfox_File::instance()->write($sCacheModules, $sData);
                                unset($aVals['module']);
                                unset($aVals['sitename']);

                                if ($this->_saveSettings($aVals)) {
                                    return [
                                        'message' => 'Select default to install',
                                        'next'    => 'getdefault',
                                    ];
                                }
                            }
                        }
                    } else {
                        return [

                        ];
                    }
                }
            }
        } else {
            $aForms = array_merge($this->_video(), $aForms);
        }

        $aModules = Phpfox_Module::instance()->getModuleFiles();
        sort($aModules['core']);
        sort($aModules['plugin']);

        $this->_oTpl->setTitle('Configuration')
            ->setBreadCrumb('Configuration')
            ->assign([
                    'aDrivers'   => $aDrivers,
                    'sCreateJs'  => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(false),
                    'aTables'    => $aExists,
                    'aModules'   => $aModules,
                    'aForms'     => $aForms,
                ]
            );
    }

    public function _getdefault()
    {
        $aDefaultApps = $this->_aDefaultApps;

        $aSteps = [];
        // process post
        if ($aVals = $this->_oReq->getArray('val')) {
            $aAppUpgrade = isset($aVals['apps']) ? $aVals['apps'] : [];
            $aAppSerialized = json_decode($aVals['apps_serialized'],true);
            foreach ($aAppUpgrade as $sKeyAppId) {
                $info = $aAppSerialized[$sKeyAppId];

                $sName = $info['name'];

                $aSteps[] = [
                    'name' => 'install_store_app.download.' . $sKeyAppId,
                    'data' => [
                        'appid'  => $sKeyAppId,
                        'url'    => $info['download'],
                        'status' => 'download',
                        'name'   => $sName,
                    ],
                ];
                $aSteps[] = [
                    'name' => 'install_store_app.upgrade.' . $sKeyAppId,
                    'msg'  => 'Installing ' . $sName,
                    'data' => [
                        'appid'  => $sKeyAppId,
                        'url'    => $info['download'],
                        'status' => 'upgrade',
                        'name'   => $sName,
                    ],
                ];
            }
            return [
                'steps'   => $aSteps,
                'next'    => 'done',
            ];
        }

        if(file_exists($filename = PHPFOX_DIR_SETTINGS .'license.php')){
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
        }

        foreach($this->getExcludedAppsByPhpFoxPackageId(PHPFOX_PACKAGE_ID) as $excludePackageId){
            unset($aDefaultApps[$excludePackageId]);
        }

        //Get app license
        $response =  $this->getInformationFromStore(array_keys($aDefaultApps));

        $Apps = [];
        $buildInfo  = $this->getBuildInfo();
        // compare version from build and info.
        foreach ($aDefaultApps as $id => $info) {
            $downloadUrl =  '#';
            $version    = isset($buildInfo[$id])? $buildInfo[$id]['apps_version']: '';

            if(isset($response[$id])
                and isset($response[$id]['link'])
                and isset($response[$id]['version'])
                and version_compare($version, $response[$id]['version'],'<')){
                $downloadUrl =  $response[$id]['link'];
                $version =  $response[$id]['version'];
            }

            $Apps[$id] = [
                'id'=> $id,
                'name'=> $info['name'],
                'version'=> $version,
                'download'=>$downloadUrl,
            ];
        }


        //End get app version
        $this->_oTpl->assign(['aDefaultApps' => $Apps, 'apps_serialized'=> json_encode($Apps)]);
    }

    public function _prepare_database(){
        $sModule =  $this->_oReq ->get('module');

        if ($this->_bUpgrade) {
            return ['next' => 'done',];
        }

        if($sModule == 'phpfoxsample'){
            return ['next' => 'done',];
        }

        if($sModule == 'phpfox'){
            return ['next' => 'done',];
        }

        $connected = false;
        Phpfox::getLibClass('phpfox.database.dba');

        if (strtolower(preg_replace("/\W/i", "", Phpfox::getParam(['db', 'driver']))) == 'database_driver') {
            $aVals = Phpfox::getLib('session')->get('installer_db');

            if (isset($aVals['driver'])) {
                unset($aVals['module']);
                unset($aVals['drop']);
                $aVals['user'] = $aVals['user_name'];
                $aVals['pass'] = $aVals['password'];
                $aT = [];
                $aT['db'] = $aVals;
                Phpfox::getLib('setting')->setParam($aT);
                unset($aT);
            }
            unset($aVals);
            Phpfox::getLib('session')->remove('installer_db');
        }
        $sDriver = 'phpfox.database.driver.' . strtolower(preg_replace("/\W/i", "",
                Phpfox::getParam(['db', 'driver'])));

        if (Phpfox::getLibClass($sDriver)) {
            $oDb = Phpfox::getLib($sDriver);

            if ($oDb->connect(Phpfox::getParam(['db', 'host']), Phpfox::getParam(['db', 'user']),
                Phpfox::getParam(['db', 'pass']), Phpfox::getParam(['db', 'name']),
                Phpfox::getParam(['db', 'port']))
            ) {
                $connected = true;
            }
        }

        if (!$connected) {
            $this->log('Can not connect get installer_modules.php');
            Phpfox_Error::set('Can not connect to database');
        }

        $oModuleProcess = Phpfox::getService('admincp.module.process');

        $oModuleProcess->install($sModule, [
                'table' => true,
            ]
        );

        return [
            'next'    => 'done',
        ];
    }

    public function _import()
    {
        Phpfox::getLib('phpfox.process')->import(Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_XML . 'version'
            . PHPFOX_XML_SUFFIX));

        if (!$this->_bUpgrade) {
            Phpfox::getService('core.country.process')->importForInstall(Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_XML
                . 'country' . PHPFOX_XML_SUFFIX));
        }
        //Add table phpfox_apps
        (new \Core\Database\Apps())->install();
        return [
            'message' => 'Importing language package',
            'next'    => 'language',
        ];
    }

    public function _language()
    {
        if (!$this->_bUpgrade) {
            $this->_db()->insert(Phpfox::getT('language'), [
                    'language_id'   => 'en',
                    'title'         => 'English (US)',
                    'user_select'   => '1',
                    'language_code' => 'en',
                    'charset'       => 'UTF-8',
                    'direction'     => 'ltr',
                    'flag_id'       => 'png',
                    'time_stamp'    => '1184048203',
                    'created'       => 'N/A (Core)',
                    'site'          => '',
                    'is_default'    => '1',
                    'is_master'     => '1',
                ]
            );

            $themeId = $this->_db()->insert(Phpfox::getT('theme'), [
                'name'       => 'Default',
                'folder'     => 'default',
                'created'    => PHPFOX_TIME,
                'is_active'  => 1,
                'is_default' => 0,
            ]);

            $this->_db()->insert(Phpfox::getT('theme_style'), [
                'theme_id'   => $themeId,
                'is_active'  => 1,
                'is_default' => 1,
                'name'       => 'Default',
                'folder'     => 'default',
                'created'    => PHPFOX_TIME,
            ]);
        }

        return [
            'message' => 'Setting up apps',
            'next'    => 'module',
        ];
    }

    public function _install_module_app()
    {
        $sModule = $this->_oReq->get('module');

        if (!$sModule) {
            return [
                'next' => 'done',
            ];
        }
        $oModuleProcess = Phpfox::getService('admincp.module.process');
        $oModuleProcess->install($sModule, ['insert' => true]);

        return [
            'message' => 'Install module ' . $sModule,
            'next'    => 'done',
        ];
    }

    /**
     * Load detail steps
     * @return array
     */
    public function _load_more_steps()
    {
        $aSteps = [];
        $base = PHPFOX_DIR . 'module/';

        if ($this->_bUpgrade) {
            $sCurrentVersion = $this->_getCurrentVersion();
            foreach ($this->_aVersions as $sVersion) {
                if (version_compare($sVersion, $sCurrentVersion) > 0) {
                    $aSteps[] = [
                        'name'    => 'upgrade_phpfox_version.' . $sVersion,
                        'msg'     => 'Upgrading phpFox to ' . $sVersion,
                        'data' => ['version'=>$sVersion],
                    ];
                }
            }
        }

        if(!$this->_bUpgrade){
            $aSteps[] = [
                'name' => 'prepare_database.core',
                'msg'  => 'Checking selected apps',
                'data'=>['module'=>'core'],
            ];
        }


        if(!$this->_bUpgrade){
            foreach (scandir($base) as $module) {
                if (file_exists($base . $module . '/install/phpfox.xml.php')) {

                    if($module != 'core'){
                        $aSteps[] = [
                            'name' => 'prepare_database.'. $module,
                            'msg'  => 'Checking selected apps',
                            'data'=>['module'=>$module],
                        ];
                    }

                    $aSteps[] = [
                        'name' => 'install_module_app.' . $module,
                        'msg'  => 'Installing ' . ucfirst($module),
                        'data' => ['module' => $module],
                    ];
                    $aSteps[] = [
                        'name' => 'verify_module_app.' . $module,
                        'msg'  => 'Verifying App ' . ucfirst($module),
                        'data' => ['module' => $module],
                    ];
                }
            }
        }

        return [
            'steps' => $aSteps,
            'next'  => 'done',
        ];
    }

    public function _verify_module_app()
    {
        if ($this->_bUpgrade) {
            return [
                'next' => 'done',
            ];
        }
        $sModule = $this->_oReq->get('module');

        if (!$sModule) {
            return [
                'next' => 'done',
            ];
        }
        $oModuleProcess = Phpfox::getService('admincp.module.process');
        $oModuleProcess->install($sModule, ['post_install' => true]);

        return [
            'message' => 'Verifying App ' . ucfirst($sModule),
            'next'    => 'done',
        ];
    }


    public function _install_store_app()
    {
        $this->bHandleFatalError = false;

        /** @var \Composer\Autoload\ClassLoader $autoloader */
        $autoloader =  include PHPFOX_DIR  .'vendor/autoload.php';

        try {
            $sStatus = $this->_oReq->get('status');
            $sUrl = $this->_oReq->get('url');
            $sAppId = $this->_oReq->get('appid');
            $sAppName = $this->_oReq->get('name');
            if (file_exists(PHPFOX_DIR_SETTINGS . 'license.sett.php')) {
                require_once PHPFOX_DIR_SETTINGS . 'license.sett.php';
            } elseif (file_exists(PHPFOX_DIR_SETTINGS . 'license.php')) {
                require_once PHPFOX_DIR_SETTINGS . 'license.php';
            }

            if ($sStatus == 'download') {
                $this->download_app($sUrl, $sAppId);
                return [
                    'next' => 'done',
                ];
            } elseif ($sStatus == 'upgrade') {
                if ($oApp = \Core\Lib::appInit($sAppId)) {
                    $autoloader->addPsr4("Apps\\$sAppId\\", [$oApp->path]);
                    $autoloader->addPsr4("Apps\\$sAppId\\Install\\", [$oApp->path .'\Install']);
                    $oApp->processInstall();
                }else{
                    $this->log(sprintf('Failed to process install "%s", invoked (\Core\Lib::appInit("%s")) return false;', $sAppId, $sAppId));
                }
            }
//            $this->_db()->update(':apps', ['is_active' => 0], 'TRUE');

        } catch (\Exception $exception) {
            $this->log('Can not install app ' . $sAppName . ': ' . $exception->getMessage());
        } finally {
            Phpfox_Error::reset(); // clear error
        }

        return [
            'next' => 'done',
        ];
    }

    public function _account()
    {
        $aForms = [];
        $aValidation = [
            'email'    => [
                'def'   => 'email',
                'title' => 'Provide a valid email.',
            ],
            'password' => [
                'def'   => 'password',
                'title' => 'Provide a valid password.',
            ],
        ];
        $oValid = Phpfox_Validator::instance()->set(['sFormName' => 'js_form', 'aParams' => $aValidation]);
        if ($aVals = $this->_oReq->getArray('val')) {
            $aVals['full_name'] = 'Admin';
            $aVals['user_name'] = 'admin';
            if ($oValid->isValid($aVals)) {
                return [
                    'next'  => 'done',
                    'steps' => [
                        [
                            'name' => 'final.add',
                            'data' => ['val' => $aVals],
                        ],
                    ],
                ];
            }
        } else {
            $aForms = array_merge($this->_video(), $aForms);
        }
        $this->_oTpl->assign([
                'sCreateJs'  => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(false),
                'aForms'     => $aForms,
            ]
        );
    }

    public function _final()
    {
        $aVals = $this->_oReq->getArray('val');

        if ($this->_bUpgrade || !$aVals) {
            return [
                'next' => 'done',
            ];
        }

        $aVals['full_name'] = 'Admin';
        $aVals['user_name'] = 'admin';

        if (($iUserId = Phpfox::getService('user.process')->add($aVals, ADMIN_USER_ID))) {
            list($bLogin, $aUser) = Phpfox::getService('user.auth')
                ->login($aVals['email'], $aVals['password'], true, 'email');
            if ($bLogin || isset($aVals['skip_user_login'])) {
                define('PHPFOX_FEED_NO_CHECK', true);
                //Add default value for contact.contact_staff_emails
                $this->_db()->update(':setting', [
                    'value_actual'  => $aVals['email'],
                    'value_default' => $aVals['email'],
                ], 'var_name="contact_staff_emails" AND module_id="contact"');
                Phpfox::getService('user.auth')->setUserId($iUserId);
                $this->_db()->update(Phpfox::getT('user_field'), ['in_admincp' => PHPFOX_TIME],
                    'user_id = ' . $iUserId);
                $this->_db()->update(Phpfox::getT('setting'), ['value_actual' => Phpfox::getVersion()],
                    'var_name = \'phpfox_version\'');
                if (!$this->_db()->isField(Phpfox::getT('feed'), 'total_view')) {
                    $this->_db()->addField([
                        'table'     => Phpfox::getT('feed'),
                        'field'     => 'total_view',
                        'type'      => 'INT',
                        'attribute' => 'UNSIGNED',
                        'null'      => false,
                        'default'   => '0',
                        'after'     => 'content',
                    ]);
                }
                $this->_video(true);
                Phpfox::getService('user.process')->updateStatus([
                    'user_status' => 'Hello World!',
                ]);

                $_SESSION['admin_email'] = $aVals['email'];
                //Set all apps to active
                $this->_db()->update(':apps', ['is_active' => 1], 'TRUE');
            }
        }
        return ['next' => 'done'];
    }

    /**
     * Upgrade phpfox version
     *
     * @return array
     */
    public function _upgrade_phpfox_version()
    {
        $sVersion = $this->_oReq->get('version');

        if ($sVersion && file_exists(__DIR__ . PHPFOX_DS . 'version' . PHPFOX_DS . $sVersion . '.php')) {
            $callback = require(__DIR__ . PHPFOX_DS . 'version' . PHPFOX_DS . $sVersion . '.php');
            if ($callback instanceof Closure) {
                $this->db = Phpfox_Database::instance();

                $reset = false;
                $return = call_user_func($callback, $this);
                if (is_array($return) && isset($return)) {
                    $reset = true;
                }

                $this->_upgradeDatabase($sVersion, $reset);
            }
        }

        return [
            'next' => 'upgrades',
        ];
    }

    public function _upgrades()
    {
        $sExtra = $this->_oReq->get('extra');
        $aExtra = [];
        if (!empty($sExtra)) {
            $aExtra = json_decode($sExtra, true);
        }
        $sStatus = isset($aExtra['status']) ? $aExtra['status'] : '';
        $sUrl = isset($aExtra['url']) ? base64_decode($aExtra['url']) : '';
        $sAppId = isset($aExtra['appid']) ? $aExtra['appid'] : '';
        $sUpgradeAppsPath = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'upgrade_app_options.log';
        if (file_exists($sUpgradeAppsPath) && $sStatus != 'completed') {
            $aUpgradeApps = json_decode(file_get_contents($sUpgradeAppsPath), true);
            if (count($aUpgradeApps)) {
                if (empty($sAppId)) {
                    $aNextApp = current($aUpgradeApps);
                    if (isset($aNextApp['appid'])) {
                        return [
                            'next'    => 'upgrades',
                            'extra'   => 'extra=' . json_encode([
                                    'appid'  => $aNextApp['appid'],
                                    'status' => 'download',
                                    'url'    => base64_encode($aNextApp['url']),
                                ]),
                            'message' => 'Downloading ' . $aNextApp['name'],
                        ];
                    }
                } else {
                    $aCurrentApp = isset($aUpgradeApps[$sAppId]) ? $aUpgradeApps[$sAppId] : false;
                    $aNextApp = [];
                    $bNext = false;
                    foreach ($aUpgradeApps as $sKeyAppId => $aAppValue) {
                        if ($bNext) {
                            $aNextApp = $aAppValue;
                            break;
                        }
                        $bNext = ($sAppId == $sKeyAppId);
                    }
                    if ($aCurrentApp) {
                        if ($sStatus == 'download') {
                            $this->download_app($sUrl, $sAppId);
                            return [
                                'next'    => 'upgrades',
                                'message' => 'Upgrading ' . $aCurrentApp['name'],
                                'extra'   => 'extra=' . json_encode([
                                        'appid'  => $sAppId,
                                        'status' => 'upgrade',
                                        'url'    => $sUrl,
                                    ]),
                            ];
                        } elseif ($sStatus == 'upgrade') {
                            if ($oApp = \Core\Lib::appInit($sAppId)) {
                                $oApp->processInstall();
                            }
                            if (isset($aNextApp['appid'])) {
                                return [
                                    'next'    => 'upgrades',
                                    'message' => 'Downloading ' . $aNextApp['name'],
                                    'extra'   => 'extra=' . json_encode([
                                            'appid'  => $aNextApp['appid'],
                                            'status' => 'download',
                                            'url'    => base64_encode($aNextApp['url']),
                                        ]),
                                ];
                            }
                        }
                    }
                }
            }
        }
        @unlink($sUpgradeAppsPath);
        return [
            'next' => 'completed',
        ];
    }

    public function download_app($sUrl, $sAppId)
    {
        if($sUrl == '#'){
            return true;
        }

        $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
        $response = $Home->admincp(['return' => $this->_oUrl->makeUrl('admincp.app.add')]);

        $sStoreUrl = $sUrl . '/installing?iframe-mode=' . $response->token;

        $this->log('fetch app info ' . $sStoreUrl);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $sStoreUrl,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $a = curl_exec($ch);
        if (preg_match('#Location: (.*)#', $a, $r)) {
            $l = trim($r[1]);
            $parts = parse_url($l);
            $sDownloadToken = substr($parts['query'], strpos($parts['query'], "t=") + 2);
            $this->log('download app: ' . $sAppId . ' token: ' . $sDownloadToken);

            $token = (new Core\Home(PHPFOX_LICENSE_ID,
                PHPFOX_LICENSE_KEY))->install_token(['token' => $sDownloadToken]);

            if (empty($token) || $token == 'null') {
                $this->log('can not get token ' . $sStoreUrl);
                return false;
            }


            $downloadUrl = $token->download;
            $type = $token->type;
            $dir = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . uniqid() . PHPFOX_DS;
            $auth_id = $token->auth_id;
            $auth_key = $token->auth_key;
            $extra_info = $token->product;


            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    //continue
                }
                chmod($dir, 0777);
            }

            //Download package
            $zip = $dir . 'import.zip';

            $ch = curl_init($downloadUrl);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 3,
                CURLOPT_TIMEOUT        => 120,
            ]);

            $content = curl_exec($ch);


            if ($error = curl_errno($ch)) {
                $this->log('curl error, could not fetch content ' . $downloadUrl);
                $this->log('curl ' . curl_error($ch));
                return false;
            }

            curl_close($ch);
            file_put_contents($zip, $content);

            if (!empty($zip)) {
                $archive = new ZipArchive();
                $zipStatus = $archive->open($zip, ZipArchive::CHECKCONS);
                if ($zipStatus !== true) {
                    //continue
                }
                $json = $archive->getFromName('package.json');
                $locateName = null;
                $configWalk = [
                    'package.json'     => '',
                    '/package.json'    => '',
                    '/app/Install.php' => 'app',
                    'app/Install.php'  => 'app',
                    'Install.php'      => 'app',
                    '/Install.php'     => 'app',

                ];

                foreach ($configWalk as $tempLocaleName => $tempType) {
                    if (false !== $archive->locateName($tempLocaleName)) {
                        $locateName = $tempLocaleName;
                        if ($tempType != '') {
                            $type = $tempType;
                        }
                        break;
                    }
                }

                if (!$locateName) {
                    $tempLocateName = $archive->getNameIndex(0);
                    if (substr($tempLocateName, -9) == '.zip.json') {
                        $locateName = $tempLocateName;
                        $type = 'theme';
                    } else {
                        if (substr($tempLocateName, -4) == '.xml') {
                            $locateName = $tempLocateName;
                            $type = 'language';
                        }
                    }
                }


                $productId = '';
                $appDir = '';

                if ($locateName) {
                    $data = json_decode($archive->getFromName($locateName), true);

                    if ($type == 'theme' && isset($data['name'])) {
                        Phpfox_Template::instance()->setTemplate('blank');
                        $this->template()->assign([
                            'error' => 'This theme is incompatible with this products version.',
                        ]);

                        return false;
                    }

                    if (!$type) {
                        $type = $data['type'];
                    }

                    if ($type == 'app' && isset($data['type']) && $data['type'] == 'product') {
                        $type = 'module';
                    }

                    if (!empty($data['id'])) {
                        $productId = !empty($data['id']) ? $data['id'] : null;
                    }

                    if (!empty($data['name'])) {
                        $name = !empty($data['name']) ? $data['name'] : null;
                    }

                    if (!empty($data['version'])) {
                        $version = !empty($data['version']) ? $data['version'] : null;
                    }

                    if (!empty($data['vendor'])) {
                        $vendor = strip_tags(!empty($data['vendor']) ? $data['vendor'] : '');
                    }
                    if (!empty($data['apps_dir'])) {
                        $appDir = base64_encode(strip_tags(!empty($data['apps_dir']) ? $data['apps_dir'] : ''));
                    }
                }

                $archive->close();
                $sFtpInfo = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'upgrade_app_ftp.log';
                $aFtpInfo = [];
                if (file_exists($sFtpInfo)) {
                    $aFtpInfo = json_decode(file_get_contents($sFtpInfo), true);
                    @unlink($sFtpInfo);
                }
                //Extract
                $param = [
                    'type'       => $type,
                    'filename'   => $zip,
                    'productId'  => $productId,
                    'apps_dir'   => $appDir,
                    'extra'      => json_decode($extra_info, true),
                    'method'     => isset($aFtpInfo['method']) ? $aFtpInfo['method'] : 'file_system',
                    'host_name'  => isset($aFtpInfo['host_name']) ? $aFtpInfo['host_name'] : 'localhost',
                    'port'       => isset($aFtpInfo['port']) ? $aFtpInfo['port'] : '21',
                    'user_name'  => isset($aFtpInfo['user_name']) ? $aFtpInfo['user_name'] : '',
                    'password'   => isset($aFtpInfo['password']) ? $aFtpInfo['password'] : '',
                    'key'        => isset($aFtpInfo['key']) ? $aFtpInfo['key'] : '',
                    'passphrase' => isset($aFtpInfo['passphrase']) ? $aFtpInfo['passphrase'] : '',
                ];
                $manager = new \Core\Installation\Manager();
                $result = $manager->verifyFilesystem($param);
                $aVals = [
                    'type'        => $type,
                    'productName' => isset($productId) ? $productId : '',
                    'productId'   => isset($productId) ? $productId : '',
                    'apps_dir'    => isset($appDir) ? $appDir : '',
                    'method'      => isset($aFtpInfo['method']) ? $aFtpInfo['method'] : 'file_system',
                    'host_name'   => isset($aFtpInfo['host_name']) ? $aFtpInfo['host_name'] : 'localhost',
                    'port'        => isset($aFtpInfo['port']) ? $aFtpInfo['port'] : '21',
                    'user_name'   => isset($aFtpInfo['user_name']) ? $aFtpInfo['user_name'] : '',
                    'password'    => isset($aFtpInfo['password']) ? $aFtpInfo['password'] : '',
                    'extra'       => json_decode($extra_info, true),
                    'key'         => isset($aFtpInfo['key']) ? $aFtpInfo['key'] : '',
                    'passphrase'  => isset($aFtpInfo['passphrase']) ? $aFtpInfo['passphrase'] : '',
                ];

                $manager = new \Core\Installation\Manager($aVals);
                $url = $manager->install($aVals);
            }
        }
    }

    /**
     * Get built-in app information.
     * @see PF.Base/install/package/info.build.php
     *
     * @return array
     */
    public function getBuildInfo()
    {
        $filename = PHPFOX_DIR .'install'. PHPFOX_DS . 'package'. PHPFOX_DS . 'info.build.php';

        if(!file_exists($filename)){
            return [];
        }

        /** @noinspection PhpIncludeInspection */
        $tmp =  include $filename;
        $build = [];

        // check existing app
        foreach($tmp as $key=>$value){
            if(!empty($value['apps_id'])){
                $build[$value['apps_id']] = $value;
            }
        }
        return $build;
    }

    /**
     * @param $iPackageId
     *
     * @return array
     */
    public function getExcludedAppsByPhpFoxPackageId($iPackageId)
    {
        if($iPackageId == 1){
            return [
                'blog',
                'Core_Blogs',
                'poll',
                'Core_Polls',
                'quiz',
                'Core_Quizzes',
                'subscribe',
                'marketplace',
                'Core_Marketplace',
                'ad',
                'forum',
                'Core_Forums',
            ];
        }elseif($iPackageId==2){
            return [
                'subscribe','marketplace','ad','Core_Marketplace'
            ];
        }
        return [];
    }

    /**
     * Take care about using this method,
     * phpfox_apps is available since 4.5.*
     *
     * @param $Apps
     *
     * @return true
     */
    public function getShouldUpgradeBuiltInApps(&$Apps)
    {

        $build =  $this->getBuildInfo();

        if(empty($build)){
            return true;
        }

        $aRequired = include 'app.default.php';

        foreach(include 'app.require.php' as $key=>$value){
            $aRequired[$key] =  $value;
        }

        $shouldUpgradeAppIds = [];

        $rows = Phpfox::getLib('database')
            ->select('*')
            ->from(':apps')
            ->execute('getRows');

        // remap build
        foreach($rows as $app){
            $appId =  $app['apps_id'];

            if(!isset($build[$appId])) {
                continue;
            }

            $shouldUpgradeAppIds[$appId] =  [
                'id'=>$appId,
                'name'=> $build[$appId]['apps_name'],
                'download'=> '#',
                'status'=> $app['is_active'],
                'latest_version'=>$build[$appId]['apps_version'],
                'current_version'=>$build[$appId]['apps_version'],
                'required'=>array_key_exists($appId, $aRequired),
            ];

        }

        // check the app is exist in module/apps tables.
        $portingAppIds = [
            'blog'        => 'Core_Blogs',
            'egift'       => 'Core_eGifts',
            'event'       => 'Core_Events',
            'forum'       => 'Core_Forums',
            'marketplace' => 'Core_Marketplace',
            'music'       => 'Core_Music',
            'newsletter'  => 'Core_Newsletter',
            'pages'       => 'Core_Pages',
            'photo'       => 'Core_Photos',
            'poke'        => 'Core_Poke',
            'poll'        => 'Core_Polls',
            'quiz'        => 'Core_Quizzes',
            'rss'         => 'Core_RSS',
            'groups'      => 'PHPfox_Groups',
            'v'           => 'PHPfox_Videos',
            'captcha'     => 'Core_Captcha',
            'announcement'=> 'Core_Announcement',
            'facebook'    => 'Core_Facebook',
        ];

        // check module status
        $tmp = Phpfox::getLib('database')
            ->select('*')
            ->from(':module')
            ->execute('getRows');

        $aModuleId = [];

        foreach($tmp as $row){
            $aModuleId[$row['module_id']] =  $row;
        }

        // unset by package id
        foreach($this->getExcludedAppsByPhpFoxPackageId(PHPFOX_PACKAGE_ID) as $excludePackageId){
            unset($portingAppIds[$excludePackageId]);
        }

        foreach($portingAppIds as $moduleId=>$appId){
            if(!isset($build[$appId])){
                continue;
            }

            $shouldUpgradeAppIds[$appId] =  [
                'id'=>$appId,
                'name'=> $build[$appId]['apps_name'],
                'status'=> 0,
                'latest_version'=>$build[$appId]['apps_version'],
                'current_version'=>$build[$appId]['apps_version'],
                'bUpgradeAvailable'=> isset($build[$appId]),
                'download'=> '#',
                'required'=> array_key_exists($appId, $aRequired),
            ];

            if(isset($aModuleId[$moduleId])){
                $shouldUpgradeAppIds[$appId]['status'] =  $aModuleId[$moduleId]['is_active'];
                $shouldUpgradeAppIds[$appId]['current_version'] =  'n/a';
            }
        }

        foreach($shouldUpgradeAppIds as $id=>$info){

            if(!isset($Apps[$id])){
                $Apps[$id] = [
                    'id'=>$id,
                    'name'=>$info['name'],
                    'status'=> $info['status'],
                    'is_compatible'=>"No",
                    'current_version'=>$info['current_version'],
                    'latest_version'=>$info['latest_version'],
                    'bUpgradeAvailable'=>true,
                    'required'=> array_key_exists($id, $aRequired),
                ];
            }elseif(version_compare($Apps[$id]['latest_version'], $info['latest_version'],'<')){
                $Apps[$id]['latest_version'] =$info['latest_version'];
                $Apps[$id]['required'] =array_key_exists($id, $aRequired);
                $Apps[$id]['bUpgradeAvailable'] =true;
            }
        }

        $aStoreInfo  = $this->getInformationFromStore(array_keys($Apps));

        foreach($Apps as $id=>$info){

            $downloadUrl =  '#';

            if(isset($build[$id])){
                $buildInfo =  $build[$id];
                if(isset($aStoreInfo[$id]) and isset($aStoreInfo[$id]['version']) and $aStoreInfo[$id]['link']
                    and version_compare($buildInfo['apps_version'], $aStoreInfo[$id]['version'],'<')){
                    $downloadUrl =  $aStoreInfo[$id]['link'];
                }
                if(version_compare($Apps[$id]['latest_version'], $buildInfo['apps_version'],'<=')){
                    $Apps[$id]['latest_version'] = $buildInfo['apps_version'];
                    $Apps[$id]['bUpgradeAvailable'] = true;
                    $Apps[$id]['download'] = $downloadUrl;
                    $Apps[$id]['is_compatible'] = 'No';
                }
            }elseif(isset($aStoreInfo[$id])){
                $storeInfo = $aStoreInfo[$id];
                if(isset($storeInfo['version'])
                    and isset($storeInfo['link'])
                    and version_compare($Apps[$id]['latest_version'], $storeInfo['version'],'<'))
                {
                    $Apps[$id]['latest_version'] = $storeInfo['version'];
                    $Apps[$id]['bUpgradeAvailable'] = true;
                    $Apps[$id]['download'] = $storeInfo['link'];
                    $Apps[$id]['is_compatible'] = 'No';
                }
            }

            if(!isset($Apps[$id]['download']) or !$Apps[$id]['download']){
                $Apps[$id]['download'] = '#';
            }
            if(false === $Apps[$id]['required']){
                $Apps[$id]['required'] = array_key_exists($id, $aRequired);
            }
        }

        // check from build but
        unset($Apps['PHPfox_reCAPTCHA']);
    }

    /**
     *
     * @return mixed
     */
    public function _update_settings()
    {
        if (file_exists(PHPFOX_DIR_SETTINGS . 'license.php')) {
            $license = file_get_contents(PHPFOX_DIR_SETTINGS . 'license.php');
            $filename = PHPFOX_DIR_SETTINGS . 'license.sett.php';
            file_put_contents($filename, $license);
            unlink(PHPFOX_DIR_SETTINGS . 'license.php');
        }

        $old = realpath(PHPFOX_DIR . '..' . PHPFOX_DS . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS
            . 'server.sett.php');

        if (file_exists($old)) {
            unlink($old);
        }

        $versionSettingFilename = PHPFOX_DIR_SETTINGS . 'version.sett.php';
        file_put_contents($versionSettingFilename,
            "<?php\nreturn " . var_export(['version' => Phpfox::getVersion(), 'timestamp' => PHPFOX_TIME],
                true) . ";\n");

        chmod($versionSettingFilename, 0777);

        file_put_contents(PHPFOX_DIR_SETTINGS . 'install.sett.php', '<?php ');

        return [
            'next'    => 'done',
        ];
    }

    public function update_db_installed_state(){
        if (Phpfox_File::instance()->isWritable(PHPFOX_DIR_SETTINGS . 'server.sett.php')) {
            $sContent = file_get_contents(PHPFOX_DIR_SETTINGS . 'server.sett.php');
            $sContent = preg_replace("/\\\$_CONF\['core.db_table_installed'\] = (.*?);/i",
                "\\\$_CONF['core.db_table_installed'] = true;", $sContent);
            if ($hServerConf = @fopen(PHPFOX_DIR_SETTINGS . 'server.sett.php', 'w')) {
                fwrite($hServerConf, $sContent);
                fclose($hServerConf);
            }
        }
    }

    /**
     * @return array
     */
    public function _verify_setting_state()
    {
        if (Phpfox_File::instance()->isWritable(PHPFOX_DIR_SETTINGS . 'server.sett.php')) {
            $sContent = file_get_contents(PHPFOX_DIR_SETTINGS . 'server.sett.php');
            $sContent = preg_replace("/\\\$_CONF\['core.is_installed'\] = (.*?);/i",
                "\\\$_CONF['core.is_installed'] = true;", $sContent);
            if ($hServerConf = @fopen(PHPFOX_DIR_SETTINGS . 'server.sett.php', 'w')) {
                fwrite($hServerConf, $sContent);
                fclose($hServerConf);
            }
        }

        return [
            'next'    => 'done',
        ];
    }

    /**
     *
     * @return array
     */
    public function _update_db()
    {
        $this->_db()->update(Phpfox::getT('user_group_setting'), ['is_hidden' => '1'], ['name' => 'custom_table_name']);
        $columns = Phpfox::getLib('database.support')->getColumns(Phpfox::getT('product'));
        if (!array_key_exists('icon', $columns)) {
            $this->_db()->addField([
                'table' => Phpfox::getT('product'),
                'field' => 'icon',
                'type'  => 'VCHAR:250',
                'null'  => true,
            ]);
        }
        if (!array_key_exists('vendor', $columns)) {
            $this->_db()->addField([
                'table' => Phpfox::getT('product'),
                'field' => 'vendor',
                'type'  => 'VCHAR:250',
                'null'  => true,
            ]);
        }

        //TODO we will bring back the settings on 4.6.0
        $this->_db()->update(':user_group_setting', ['is_hidden' => '1'],
            ['module_id' => 'mail', 'name' => 'can_message_self']);

        $this->_db()->update(':user_group_setting', ['is_hidden' => '1'],
            ['module_id' => 'mail', 'name' => 'send_message_to_max_users_each_time']);

        // TODO temporary hide this setting, it will be removed in future
        $this->_db()->update(':user_group_setting', ['is_hidden' => '1'],
            ['module_id' => 'user', 'name' => 'force_cropping_tool_for_photos']);

        if (!$this->_bUpgrade) {
            $this->_db()->update(Phpfox::getT('setting'), ['value_actual' => date('j/n/Y', PHPFOX_TIME)],
                ['var_name' => 'official_launch_of_site']);

            $this->_db()->addField([
                'table'     => Phpfox::getT('user_space'),
                'field'     => 'space_groups',
                'type'      => 'INT:10',
                'attribute' => 'UNSIGNED',
                'null'      => false,
                'default'   => '0',
            ]);
            $this->_db()->addField([
                'table'     => Phpfox::getT('user_field'),
                'field'     => 'total_groups',
                'type'      => 'INT:10',
                'attribute' => 'UNSIGNED',
                'null'      => false,
                'default'   => '0',
            ]);
            $this->_db()->addField([
                'table'     => Phpfox::getT('user_activity'),
                'field'     => 'activity_groups',
                'type'      => 'INT:10',
                'attribute' => 'UNSIGNED',
                'null'      => false,
                'default'   => '0',
            ]);

            foreach (scandir(PHPFOX_DIR_MODULE) as $module) {
                if ($module == '.' || $module == '..') {
                    continue;
                }

                $on_success = PHPFOX_DIR_MODULE . $module . PHPFOX_DS . 'install' . PHPFOX_DS . 'on_success.php';
                if (file_exists($on_success)) {
                    $on_success = require($on_success);

                    if (!$this->db) {
                        $this->db = Phpfox_Database::instance();
                    }

                    call_user_func($on_success, $this);
                }
            }
        } else {
            $this->_db()->update(Phpfox::getT('setting'), ['value_actual' => Phpfox::getVersion()],
                'var_name = \'phpfox_version\'');
        }
        return [
            'next'    => 'done',
        ];
    }

    public function _install_core_app(){
        $app = $this->_oReq->get('appid');

        if (($appInit = \Core\Lib::appInit($app)) != false) {
            $appInit->processInstall();
        }

        return ['next'=>'done'];

    }

    /**
     * Make new theme
     *
     * @return array
     */
    public function _generate_default_theme()
    {
        $dir = PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . 'bootstrap' . PHPFOX_DS;

        $bootstrap = json_decode(file_get_contents(PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . 'core-flavors' . PHPFOX_DS
            . 'flavors' . PHPFOX_DS . 'bootstrap.json'));
        foreach ($bootstrap as $file => $content) {
            if (preg_match('/^(.*)\.(gif|jpg|jpeg|png)$/i', $file)) {
                $content = base64_decode($content);
            }
            $file = str_replace('/', PHPFOX_DS, $file);
            file_put_contents($dir . ltrim($file, PHPFOX_DS), $content);
        }

        // Install bootstrap template
        $Theme = new Core\Theme();
        $newTheme = $Theme->make([
            'name' => 'Bootstrap',
        ], null, false, 'bootstrap');

        $this->_db()->update(Phpfox::getT('theme'), ['is_default' => 1], ['theme_id' => $newTheme->theme_id]);

        return [
            'next'    => 'done',
        ];
    }

    public function _extract_builtin_packages()
    {
        $packageDir =  PHPFOX_DIR . 'install'. PHPFOX_DS .'package'. PHPFOX_DS;

        if(file_exists($build = $packageDir. 'info.build.php')){
            /** @noinspection PhpIncludeInspection */
            $build = include $build;
            foreach($build as $item){
                $target = PHPFOX_ROOT . $item['target'];
                $filename =  $packageDir.  $item['filename'];

                if(!file_exists($filename)){
                    continue;
                }

                if((!is_dir($target) and !mkdir($target,0755,true)) or !is_writeable($target)){
                    // handle log here.
                    $this->log(sprintf('Can not write to directory "%s"', $target));
                    continue;
                }

                $zip = new ZipArchive();
                $zip->open($filename);
                $zip->extractTo($target);
                $zip->close();
            }
        }

        return ['next'=>'done'];
    }

    /**
     * Make new theme
     *
     * @return array
     */
    public function _generate_material_theme()
    {
        // validate directory is writable.
        $directory = PHPFOX_DIR_SITE . 'flavors'. PHPFOX_DS. 'material' . PHPFOX_DS;

        if(!is_dir($directory)){ // ensure material exists
            return ['next'=>'done'];
        }

        try{
            if(file_exists($filename = $directory . 'phrase.json')){
                $phrases = json_decode(file_get_contents($filename), true);
                if($phrases){
                    (new \Core\Phrase())->addPhrase($phrases);
                }
            }
        }catch (\Exception $exception){

        }

        return [
            'next'    => 'done',
        ];
    }

    public function _rebuild_material_theme()
    {
        if(!is_dir(PHPFOX_DIR_SITE .'flavors'. PHPFOX_DS . 'material')){ // ensure material exists
            return ['next'=>'done'];
        }
        try{
            $flavor =  flavor();
            storage()->del('flavor/default');
            storage()->set('flavor/default', 'material');
            $flavor->set_active('material');
            $flavor->rebuild_material(true);

        }catch (\Exception $exception){
            $flavor =  flavor();
            storage()->del('flavor/default');
            storage()->set('flavor/default', 'bootstrap');
            $flavor->set_active('bootstrap');
        }

        return [
            'next'    => 'done',
        ];
    }

    public function _rebuild_bootstrap()
    {
        try{
            flavor()->rebuild_bootstrap(true);
        }catch(\Exception $exception){
            $this->log($exception->getMessage());
        }

        return [
            'next'=>'done',
        ];
    }

    /**
     * @return array
     */
    public function _generate_admin_account()
    {
        if ($this->_bUpgrade) {
            return ['next' => 'done'];
        }

        unset($_SESSION['admin_email']);

        return [
            'next'    => 'done',
        ];
    }

    /**
     * call only when completed
     *
     * @return array
     */
    public function _generate_timezone()
    {
        // generate time zones list
        Phpfox::getService('core')->generateTimeZones();

        return [
            'next'    => 'done',
        ];
    }

    /**
     * @return array
     */
    public function _verify_app_state()
    {
        $this->bHandleFatalError = false;
        try {
            //Enable all apps again, then upgrade app if available
            $sAppStateLog = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'upgrade_app_state.log';
            $sEnableErrorMessage = '';
            if (file_exists($sAppStateLog)) {
                $aStatesData = json_decode(file_get_contents($sAppStateLog), true);
                foreach ($aStatesData as $iKey => $aStatesDatum) {
                    if (!$aStatesDatum['is_active']) {
                        continue;
                    }
                    Phpfox::getLib('database')->update(Phpfox::getT('apps'), [
                        'is_active' => 1,
                    ], 'apps_id="' . $aStatesDatum['apps_id'] . '"');
                    if (!$appInit = \Core\Lib::appInit($aStatesDatum['apps_id'])) {
                        continue;
                    }
                    try {
                        $appInit->enable();
                    } catch (\Exception $e) {
                        $appInit->disable();
                        $sEnableErrorMessage .= $e->getMessage() . '\n';
                    }
                    unset($appInit);
                }
                @unlink($sAppStateLog);
            }
            if (!empty($sEnableErrorMessage)) {
                Phpfox_Error::set($sEnableErrorMessage);
            }
            //End Enable apps
        } catch (\Exception $exception) {

        }
        return [
            'next'    => 'done',
        ];
    }

    public function _all_done()
    {
        if ($this->_isTrial()) {
            $this->postReport([
                'status'   => 'success',
                'email'    => isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '',
                'is_trial' => true,
                'url'      => $this->getHostPath(),
            ]);
        }

        $this->remove_ftp_logs();

        unlink(PHPFOX_DIR_SETTINGS . 'install.sett.php');

        Phpfox::getLib('cache')->remove();
        Phpfox::getLib('template.cache')->remove();
        Phpfox::getLib('cache')->removeStatic();

        $errors = false;

        if (file_exists($this->logMessageFilename)) {
            $errors = nl2br(file_get_contents($this->logMessageFilename));
        }

        $this->_oTpl->assign([
                'bIsUpgrade'      => $this->_bUpgrade,
                'errors'          => $errors,
                'sUpgradeVersion' => Phpfox::getVersion(),
            ]
        );
    }

    ########################
    # Private Methods
    ########################

    public function _getCurrentVersion()
    {
        static $sVersion = null;

        if ($sVersion !== null) {
            return $sVersion;
        }

        $fileVersion = PHPFOX_DIR_SETTINGS . 'version.sett.php';
        if (file_exists($fileVersion)) {
            $version = require($fileVersion);

            // remove these lines on 4.6.0 official.
            if($version['version'] == '4.6.0' and Phpfox::VERSION == '4.6.0-rc1'){ // rc-only.
                return '4.6.0-beta1';
            }
        }

        $newFile = PHPFOX_DIR_SETTINGS . 'version.sett.php';
        if (file_exists($newFile)) {
            $object = (object)require($newFile);
            if (isset($object->version)) {
                $sVersion = $object->version;

                return $sVersion;
            }
        }

        $bIsLegacy = true;
        if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'server.sett.php')) {
            $_CONF = [];
            require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'server.sett.php');

            if ($_CONF['core.is_installed'] === true) {
                $aRow = Phpfox_Database::instance()->select('value_actual')->from(Phpfox::getT('setting'))
                    ->where('var_name = \'phpfox_version\'')->execute('getRow');
                if (isset($aRow['value_actual'])) {
                    $sVersion = $aRow['value_actual'];

                    return $aRow['value_actual'];
                }
            }
        }

        if (file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'version.php')) {
            $_CONF = [];
            require_once(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'version.php');

            $sVersion = $_CONF['info.version'];

            return $_CONF['info.version'];
        } else {
            $aRow = Phpfox_Database::instance()->select('value_actual')->from(Phpfox::getT('setting'))
                ->where('var_name = \'phpfox_version\'')->execute('getRow');
            if (isset($aRow['value_actual'])) {
                $sVersion = $aRow['value_actual'];

                return $aRow['value_actual'];
            }
        }

        return Phpfox_Error::set('Unknown version.', E_USER_ERROR);
    }

    /**
     * @todo We need to work on this routine, not working very well.
     */
    public function _isPassed($sStep)
    {
        return true;
    }

    public function _pass($sForward = null)
    {
        fwrite($this->_hFile, "\n" . $this->_sStep);

        if ($sForward !== null) {
            fclose($this->_hFile);

            $this->_oUrl->forward($this->_step($sForward));
        }

        fclose($this->_hFile);

        return true;
    }

    public function _getOldT($sTable)
    {
        return (isset($this->_aOldConfig['db']['prefix']) ? $this->_aOldConfig['db']['prefix'] : '') . $sTable;
    }

    public function _db()
    {
        return Phpfox_Database::instance();
    }

    public function _step($aParams)
    {
        if (is_array($aParams)) {
            $aParams['sessionid'] = self::$_sSessionId;
        } else {
            $aParams = [$aParams, 'sessionid' => self::$_sSessionId];
        }

        return $this->_oUrl->makeUrl($this->_sUrl, $aParams);
    }

    public function _saveSettings($aVals)
    {
        // Get sub-folder
        $sSubfolder = str_replace(['index.php/', 'index.php'], '', $_SERVER['PHP_SELF']);

        // Get the settings content
        $sContent = file_get_contents(PHPFOX_DIR_SETTING . 'server.sett.php.new');

        // Trim and add slashes to each value since we are writing to a file
        foreach ($aVals as $iKey => $sVal) {
            $aVals[$iKey] = addslashes(trim($sVal));
        }

        $aFind = [
            "/\\\$_CONF\['db'\]\['driver'\] = (.*?);/i",
            "/\\\$_CONF\['db'\]\['host'\] = (.*?);/i",
            "/\\\$_CONF\['db'\]\['user'\] = (.*?);/i",
            "/\\\$_CONF\['db'\]\['pass'\] = (.*?);/i",
            "/\\\$_CONF\['db'\]\['name'\] = (.*?);/i",
            "/\\\$_CONF\['db'\]\['prefix'\] = (.*?);/i",
            "/\\\$_CONF\['db'\]\['port'\] = (.*?);/i",
            "/\\\$_CONF\['core.host'\] = (.*?);/i",
            "/\\\$_CONF\['core.folder'\] = (.*?);/i",
            "/\\\$_CONF\['core.url_rewrite'\] = (.*?);/i",
            "/\\\$_CONF\['core.salt'\] = (.*?);/i",
            "/\\\$_CONF\['core.cache_suffix'\] = (.*?);/i",
        ];

        $aReplace = [
            "\\\$_CONF['db']['driver'] = '{$aVals['driver']}';",
            "\\\$_CONF['db']['host'] = '{$aVals['host']}';",
            "\\\$_CONF['db']['user'] = '{$aVals['user_name']}';",
            "\\\$_CONF['db']['pass'] = '{$aVals['password']}';",
            "\\\$_CONF['db']['name'] = '{$aVals['name']}';",
            "\\\$_CONF['db']['prefix'] = '" . (!empty($aVals['prefix']) ? $aVals['prefix'] : 'phpfox_') . "';",
            "\\\$_CONF['db']['port'] = '{$aVals['port']}';",
            "\\\$_CONF['core.host'] = '{$_SERVER['HTTP_HOST']}';",
            "\\\$_CONF['core.folder'] = '{$sSubfolder}';",
            "\\\$_CONF['core.url_rewrite'] = '" . ((isset($aVals['rewrite']) && $aVals['rewrite'] === true) ? '1' : '2')
            . "';",
            "\\\$_CONF['core.salt'] = '" . md5(uniqid(rand(), true)) . "';",
            "\\\$_CONF['core.cache_suffix'] = '.php';",
        ];

        $sContent = preg_replace($aFind, $aReplace, $sContent);
        if ($hServerConf = @fopen(PHPFOX_DIR_SETTINGS . 'server.sett.php', 'w')) {
            fwrite($hServerConf, $sContent);
            fclose($hServerConf);

            return true;
        }

        return Phpfox_Error::set('Unable to open config file.');
    }

    public function _upgradeDatabase($sVersion, $reset = false)
    {
        if ((int)substr($this->_getCurrentVersion(), 0, 1) <= 1) {
            return;
        }

        if (!defined('PHPFOX_UPGRADE_MODULE_XML')) {
            define('PHPFOX_UPGRADE_MODULE_XML', true);
        }

        if ($reset) {
            define('PHPFOX_PRODUCT_UPGRADE_CHECK', true);
        }

        $hDir = opendir(PHPFOX_DIR_MODULE);
        while ($sModule = readdir($hDir)) {
            if ($sModule == '.' || $sModule == '..') {
                continue;
            }

            if ($sModule == 'phpfox' || $sModule == 'emoticon' || $sModule == 'facebook') {
                continue;
            }

            if (file_exists(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'phpfox.xml.php')) {
                $aModule = Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install'
                    . PHPFOX_DS . 'phpfox.xml.php');

                if (isset($aModule['tables'])) {
                    $oPhpfoxDatabaseExport = Phpfox::getLib('database.support');
                    $aTables = unserialize(trim($aModule['tables']));
                    if (empty($aTables)) {
                        continue;
                    }
                    $sQueries = Phpfox::getLib('database.export')->process(Phpfox::getParam(['db', 'driver']),
                        $aTables);
                    $aDriver = $oPhpfoxDatabaseExport->getDriver(Phpfox::getParam(['db', 'driver']));

                    $sQueries = preg_replace('#phpfox_#i', Phpfox::getParam(['db', 'prefix']), $sQueries);

                    if ($aDriver['comments'] == 'remove_comments') {
                        $oPhpfoxDatabaseExport->removeComments($sQueries);
                    } else {
                        $oPhpfoxDatabaseExport->removeRemarks($sQueries);
                    }

                    $aSql = $oPhpfoxDatabaseExport->splitSqlFile($sQueries, $aDriver['delim']);

                    foreach ($aSql as $sSql) {
                        $sSql = preg_replace('/CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $sSql);

                        $this->_db()->query($sSql);
                    }
                }
            }
        }

        $hDir = opendir(PHPFOX_DIR_MODULE);
        while ($sModule = readdir($hDir)) {
            if ($sModule == '.' || $sModule == '..') {
                continue;
            }

            if ($sModule == 'phpfox' || $sModule == 'emoticon' || $sModule == 'facebook') {
                continue;
            }

            $bIsNewModule = false;
            if (file_exists(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'phpfox.xml.php')) {
                $aModule = Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install'
                    . PHPFOX_DS . 'phpfox.xml.php');
                if ($reset) {
                    Phpfox::getService('admincp.module.process')->install($sModule, ['insert' => true], 'phpfox',
                        $aModule);
                    continue;
                }

                if (isset($aModule['data']['module_id'])) {
                    $iIsModule = $this->_db()->select('COUNT(*)')
                        ->from(Phpfox::getT('module'))
                        ->where('module_id = \'' . $this->_db()->escape($aModule['data']['module_id']) . '\'')
                        ->execute('getField');

                    if (!$iIsModule) {
                        $bIsNewModule = true;
                        $this->_db()->insert(Phpfox::getT('module'), [
                                'module_id'       => $aModule['data']['module_id'],
                                'product_id'      => 'phpfox',
                                'is_core'         => $aModule['data']['is_core'],
                                'is_active'       => 1,
                                'version'         => Phpfox::VERSION,
                                'author'          => 'phpFox',
                                'vendor'          => 'https://store.phpfox.com/',
                                'is_menu'         => $aModule['data']['is_menu'],
                                'menu'            => $aModule['data']['menu'],
                                'phrase_var_name' => $aModule['data']['phrase_var_name'],
                            ]
                        );
                        Phpfox::getService('admincp.module.process')->install(null, ['insert' => true], 'phpfox',
                            $aModule);
                    }
                }

                if (!empty($aModule['data']['menu'])) {
                    $aModuleCheck = $this->_db()->select('module_id, menu')
                        ->from(Phpfox::getT('module'))
                        ->where('module_id = \'' . $this->_db()->escape($aModule['data']['module_id']) . '\'')
                        ->execute('getRow');

                    if (isset($aModuleCheck['module_id']) && $aModuleCheck['menu'] != $aModule['data']['menu']) {
                        $this->_db()->update(Phpfox::getT('module'), ['menu' => $aModule['data']['menu']],
                            'module_id = \'' . $this->_db()->escape($aModuleCheck['module_id']) . '\'');
                    }
                }
            }

            if (file_exists(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS . 'install' . PHPFOX_DS . 'version' . PHPFOX_DS
                . $sVersion . '.xml.php')) {
                $aUpgradeModule = Phpfox::getLib('xml.parser')->parse(PHPFOX_DIR_MODULE . $sModule . PHPFOX_DS
                    . 'install' . PHPFOX_DS . 'version' . PHPFOX_DS . $sVersion . '.xml.php');

                if (isset($aUpgradeModule['sql'])) {
                    $sSqlQuery = Phpfox::getLib('database.export')->processAlter(Phpfox::getParam([
                        'db',
                        'driver',
                    ]), unserialize($aUpgradeModule['sql']), false, true);
                    $aDriver = $oPhpfoxDatabaseExport->getDriver(Phpfox::getParam(['db', 'driver']));
                    /**/
                    $aSql = $oPhpfoxDatabaseExport->splitSqlFile($sSqlQuery, $aDriver['delim']);

                    foreach ($aSql as $sSql) {
                        $this->_db()->query($sSql);
                    }
                }

                if ($bIsNewModule === false) {
                    Phpfox::getService('admincp.module.process')
                        ->install($sModule, ['insert' => true, 'force_check' => true], 'phpfox',
                            $aUpgradeModule);
                }
            }
        }
        closedir($hDir);
    }

    public function initErrorHandlers()
    {
        error_reporting(E_ALL);
        register_shutdown_function([$this, 'onInstallationShutdown']);
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * Exception handler
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    public function exceptionHandler($exception)
    {
        $this->log($exception->__toString());
        $this->log($this->formatBacktrace(debug_backtrace(1)));
        return false;
    }

    /**
     * @return array
     */
    public function collectInformation()
    {
        $result = [];
        foreach (
            [
                'HTTP_HOST',
                'HTTP_USER_AGENT',
                'SERVER_SOFTWARE',
                'SERVER_NAME',
                'SERVER_ADDR',
                'SERVER_PORT',
                'REMOTE_ADDR',
                'REQUEST_SCHEME',
                'DOCUMENT_ROOT',
            ] as $key
        ) {
            $result[$key] = isset($_SERVER[$key]) ? $_SERVER[$key] : "";
        }

        $result['LICENSE_ID'] = defined('PHPFOX_LICENSE_ID') ? PHPFOX_LICENSE_ID : 'techie';
        $result['PHPFOX_PACKAGE_ID'] = defined('PHPFOX_PACKAGE_ID') ? PHPFOX_PACKAGE_ID : 3;

        if (function_exists('ini_get_all')) {
            $result['php_ini'] = json_encode(ini_get_all());
        }

        if (file_exists($this->logFilename)) {
            $result['log_content'] = file_get_contents($this->logFilename);
        } else {
            $result['log_content'] = "empty";
        }


        return $result;
    }

    public function onInstallationShutdown()
    {
        $error = error_get_last();
        if (empty($error)) {
            return;
        }
        $fatal = false;
        $level = 'error';

        switch ($error['type']) {
            case E_COMPILE_ERROR:
                $fatal = true;
                $level = 'E_COMPILE_ERROR';
                break;
            case E_CORE_ERROR:
                $fatal = true;
                $level = 'E_CORE_ERROR';
                break;
            case E_ERROR:
                $fatal = true;
                $level = 'E_ERROR';
                break;
            case E_PARSE:
                $fatal = true;
                $level = 'E_PARSE';
                break;
            case E_RECOVERABLE_ERROR:
                $fatal = true;
                $level = 'E_RECOVERABLE_ERROR';
                break;
            case E_USER_ERROR:
                $fatal = true;
                $level = 'E_USER_ERROR';
                break;
            default:
                break;
        }
        $this->log('error ' . $level . $error['message'] . PHP_EOL);
        $this->log($this->formatBacktrace(debug_backtrace()));

        if ($fatal && $this->bHandleFatalError) {
            $this->postReportFailure();
        }
    }


    /**
     * @param array $data
     *
     * @return bool
     */
    public function postReport($data)
    {
        $data['extra_info'] = $this->collectInformation();
        (new Core\Home())->trial($data);

        return true;
    }

    public function remove_ftp_logs()
    {
        if(file_exists($filename = PHPFOX_DIR_FILE . 'log' . PHPFOX_DS . 'upgrade_app_ftp.log')){
            @unlink($filename);
        }
    }

    public function postReportFailure()
    {
        $data = [
            'url'        => $this->getHostPath(),
            'failed'     => 1,
            'email'      => isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '',
            'extra_info' => $this->collectInformation(),
            'is_trial'   => (function_exists('ioncube_file_info') && is_array(ioncube_file_info())),
        ];

        $this->remove_ftp_logs();
        (new Core\Home())->trial($data);

        return true;
    }

    public function logMessage($message)
    {
        $filename = $this->logMessageFilename;

        if (file_exists($filename)) {
            if (time() - filemtime($filename) > 600) {
                @unlink($filename);
            }
        }

        if (!is_dir(dirname($filename))) {
            return;
        }

        if (null != ($fp = @fopen($filename, 'a+'))) {
            fwrite($fp, PHP_EOL);
            fwrite($fp, $message);
            fclose($fp);
        }
    }

    public function log($message)
    {

        $filename = $this->logFilename;

        if (file_exists($filename)) {
            if (time() - filemtime($filename) > 600) {
                @unlink($filename);
            }
        }

        if (!is_dir(dirname($filename))) {
            return;
        }

        if (null != ($fp = @fopen($filename, 'a+'))) {
            fwrite($fp, PHP_EOL);
            fwrite($fp, $message);
            fclose($fp);
        }
    }

    public function formatBacktrace($backtrace)
    {
        $output = '';
        $output .= 'Stack trace:' . PHP_EOL;
        $index = 0;
        foreach ($backtrace as $index => $stack) {
            // Process args
            $args = [];
            if (!empty($stack['args'])) {
                foreach ($stack['args'] as $argIndex => $argValue) {
                    if (is_object($argValue)) {
                        $args[$argIndex] = get_class($argValue);
                    } else {
                        if (is_array($argValue)) {
                            $args[$argIndex]
                                = 'Array';
                        } else {
                            if (is_string($argValue)) {
                                $args[$argIndex] = substr($argValue, 0, 2048);
                            } else {
                                $args[$argIndex] = print_r($argValue, true);
                            }
                        }
                    }
                }
            }
            // Process message
            $output .= sprintf(
                '#%1$d %2$s(%3$d): %4$s%5$s%6$s(%7$s)',
                $index,
                (!empty($stack['file']) ? $stack['file'] : '(unknown file)'),
                (!empty($stack['line']) ? $stack['line'] : '(unknown line)'),
                (!empty($stack['class']) ? $stack['class'] : ''),
                (!empty($stack['type']) ? $stack['type'] : ''),
                $stack['function'],
                join(', ', $args)
            );
            $output .= PHP_EOL;
        }

        // Throw main in there for the hell of it
        $output .= sprintf('#%1$d {main}', $index + 1);

        return $output . PHP_EOL;
    }

    public function errorHandler(
        $errno,
        $string,
        $file = null,
        $line = null,
        $context = null
    ) {
        // Force fatal errors to get reported
        $fatal = false;
        $level = $errno;

        switch ($errno) {
            case E_COMPILE_ERROR:
                $level = 'E_COMPILE_ERROR';
                $fatal = true;
                break;
            case E_CORE_ERROR:
                $level = 'E_CORE_ERROR';
                $fatal = true;
                break;
            case E_ERROR:
                $level = 'E_ERROR';
                $fatal = false;
                break;
            case E_PARSE:
                $level = 'E_PARSE';
                $fatal = true;
                break;
            case E_RECOVERABLE_ERROR:
                $level = 'E_RECOVERABLE_ERROR';
                $fatal = true;
                break;
            case E_USER_ERROR:
                $level = 'E_USER_ERROR';
                $fatal = false;
                break;
            case E_USER_NOTICE:
                $level = 'E_USER_NOTICE';
                $fatal = false;
                break;
            case E_NOTICE:
                $level = 'E_NOTICE';
                $fatal = false;
                break;
            default:
                break;
        }
        $message = sprintf(
            '[%1$d] %2$s (%3$s) [%4$d]' . PHP_EOL . '%5$s',
            $errno,
            $string,
            $file,
            $line,
            $this->formatBacktrace(array_slice(debug_backtrace(), 1)));

        $this->log('error level ' . $level . PHP_EOL . $message);


        // Handle fatal with nice response for user
        if ($fatal && $this->bHandleFatalError) {
            $this->postReportFailure();
        }

        return true;
    }

    public function sendFatalResponse()
    {

    }

    /**
     * Send html error to terminate process
     * @param string $errors
     */
    public function terminalByError($errors)
    {
        exit($errors);
    }

    public function getVersionList()
    {
        return $this->_aVersions;
    }

    public function _isTrial()
    {
        return (defined('PHPFOX_TRIAL_MODE') && PHPFOX_TRIAL_MODE);
    }

    /**
     * Define auto-loader for trial
     */
    public function _includeAutoLoad()
    {
        $autoloader = include PHPFOX_DIR . 'vendor' . PHPFOX_DS . 'autoload.php';
        $aApps = array_merge($this->_aRequiredApps, $this->_aDefaultApps);
        $allNamespaces = [];
        foreach ($aApps as $sId => $aInfo) {
            $sDir = !empty($aInfo['dir']) ? $aInfo['dir'] : $sId;
            $allNamespaces['Apps\\' . $sId . '\\'] = 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . $sDir;
        }
        foreach ($allNamespaces as $namespace => $path) {
            $autoloader->setPsr4($namespace, PHPFOX_PARENT_DIR . $path);
        }
    }

    public function removeDirectory($dir)
    {
        $dir = realpath($dir);

        if (!is_dir($dir)) {
            return;
        }

        $files
            = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,
            RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $splInfo) {
            if ($splInfo->isDir()) {
                @rmdir($splInfo->getRealpath());
            }

            if ($splInfo->isFile()) {
                @unlink($splInfo->getRealpath());
            }
        }
    }

    private function initLog(){
        if(file_exists($this->logFilename) and filesize($this->logFilename) > 2000000){
            @rename($this->logFilename, $this->logFilename.'.'. date('Y-m-d_H_i_s'));
        }

        if(!$this->_bUpgrade){
            $this->log('Installation New PhpFox Site ');
        }else{
            $this->log('Upgrading New PhpFox Site');

        }
        $this->log('Version '. Phpfox::VERSION . ' build '. Phpfox::PRODUCT_BUILD .' date '. date('Y-m-d H:i:s'));
        if (defined('PHPFOX_TRIAL_MODE') && PHPFOX_TRIAL_MODE) {
            $this->log("Trial Mode Package");
        }
    }
}

<?php

namespace Core\Installation;

use Core\App;
use Core\Cache;
use Core\Db;
use Core\Theme;
use Core\Theme\Object;
use Phpfox;
use Phpfox_Error;

/**
 * Class Manager
 *
 * @package Core\Installation
 */
class Manager
{

    /**
     *
     */
    CONST PHPFOX_STORE_ENDPOINT_URL = '';

    /**
     * @var array
     */
    private $packages = [];

    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var Vfs method
     */
    private $vfs;
    private $host;
    private $port;
    private $userName;
    private $passWord;
    private $managerControl;

    private $_is_upgrade = false;

    public function __construct($hostInfo = [])
    {
        if (isset($hostInfo['method'])) {
            $this->vfs = $hostInfo['method'];
        }
        if (isset($hostInfo['host_name'])) {
            $this->host = $hostInfo['host_name'];
        } else {
            $this->host = Phpfox::getParam('core.ftp_host_name');
        }
        if (isset($hostInfo['port'])) {
            $this->port = $hostInfo['port'];
        } else {
            $this->port = Phpfox::getParam('core.ftp_port');
        }
        if (isset($hostInfo['user_name'])) {
            $this->userName = $hostInfo['user_name'];
        } else {
            $this->userName = Phpfox::getParam('core.ftp_user_name');
        }
        if (isset($hostInfo['password'])) {
            $this->passWord = $hostInfo['password'];
        } else {
            $this->passWord = Phpfox::getParam('core.ftp_password');
        }
        $param = [
            'port' => $this->port,
            'host' => $this->host,
            'user' => $this->userName,
            'pass' => $this->passWord,
        ];
        switch ($this->vfs) {
            case 'sftp':
                $this->managerControl = new Sftp($param);
                break;
            case 'key':
                if (isset($hostInfo['key'])) {
                    $param['key'] = $hostInfo['key'];
                }
                if (isset($hostInfo['passphrase'])) {
                    $param['passphrase'] = $hostInfo['passphrase'];
                }
                $this->managerControl = new Key($param);
                break;
            case 'ftp':
                $this->managerControl = new Ftp($param);
                break;
            case 'file_system':
            default:
                $this->managerControl = new FileSystem($param);
                break;

        }
    }

    /**
     * @return Vfs
     */
    public function getVfs()
    {
        if (null == $this->vfs) {
            $this->vfs = new FileSystem([]);
        }

        return $this->vfs;
    }

    /**
     * @param Vfs $vfs
     */
    public function setVfs($vfs)
    {
        $this->vfs = $vfs;
    }

    /**
     * @return array
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param array $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     *
     */
    public function process()
    {
        foreach ($this->packages as $package) {

        }
    }

    /**
     * Follow step by step
     * + Rollback database [if step]
     * + Rollback filesystem
     */
    public function rollback()
    {


    }


    /**
     * @param array $param
     *
     * @return string
     */
    public function install($param)
    {
        $this->onBeforeInstall($param);
        $returnUrl = $this->onInstall($param);
        $this->onAfterInstall($param);

        return $returnUrl;
    }

    public function upgrade($param)
    {

    }

    public function uninstall()
    {
    }

    /**
     * @param array $param
     *
     * @return string
     */
    public function onInstall($param)
    {
        $this->copyFile($param);
        $returnUrl = $this->runInstall($param);
        $this->cleanInstall();

        return $returnUrl;
    }

    /**
     * @param $param
     *
     * @return bool
     */
    public function copyFile($param)
    {
        if (empty($param['type'])) {
            throw new \InvalidArgumentException(_p('Missing [type]'));
        }

        $bReturn = true;

        $copyResult = null;

        $result = ['files' => [], 'tempPath' => '', 'newPath' => '',];

        switch ($param['type']) {
            case 'app':
            case 'application':
                $copyResult = $this->copyFileForApp($param);
                break;
            case 'theme':
            case 'flavor':
                $copyResult = $this->copyFileForTheme($param);
                break;
            case 'module':
            case 'product':
                $copyResult = $this->copyFileForModule($param);
                break;
            case 'language':
                $copyResult = $this->copyFileForLanguage($param);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('unexpected copy file for type [%s]',
                    $param['type']));
                break;
        }

        $result = array_merge($result, $copyResult);
        if (!empty($result['files'])) {
            $this->managerControl->setFile($result['files'])
                ->setFromPath($result['tempPath'])
                ->setToPath($result['newPath'])
                ->run();
        }


        return $bReturn;
    }

    /**
     * @param $param
     *
     * @return string
     */
    public function getTemporaryExtractThemeDirectory($param)
    {
        return PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'phpfox-theme-'
            . $param['productId'] . '/';
    }

    /**
     * @param $param
     *
     * @return string
     */
    public function getTemporaryExtractProductDirectory($param)
    {
        return PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'phpfox-product-'
            . $param['productId'] . PHPFOX_DS;
    }

    /**
     * @param $param
     *
     * @return string
     */
    public function getTemporaryExtractAppDirectory($param)
    {
        return PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'phpfox-app-'
            . $param['productId'] . PHPFOX_DS;
    }

    /**
     * @param array $param
     *
     * @return array
     */
    public function copyFileForTheme($param)
    {
        // forward this method to runInstall theme.
        return [];
    }

    public function copyFileForLanguage($param)
    {
        $temporaryDir = $param['targetDirectory']
            . 'upload/include/xml/language/';
        foreach (scandir($temporaryDir) as $folder) {
            if (file_exists($temporaryDir . $folder
                . '/phpfox-language-import.xml')) {
                Phpfox::getService('language.process')
                    ->installPackFromFolder($folder,
                        $temporaryDir . $folder . '/');
                $dir = base64_encode($temporaryDir . $folder . '/');
                url()->send('/admincp/language/import', ['dir' => $dir]);
            }
        }
    }

    /**
     * @param array $param
     *
     * @return array
     */
    public function copyFileForModule($param)
    {

        $temporaryDir = $this->getTemporaryExtractProductDirectory($param);

        $fileList = json_decode(file_get_contents($temporaryDir
            . 'checksum.json'), true);

        return [
            'tempPath' => $temporaryDir,
            'newPath'  => realpath(dirname(rtrim(PHPFOX_DIR, '/'))),
            'files'    => array_values($fileList),
        ];
    }


    /**
     * Do not use this method directly.
     *
     * @param $param [type: string, name: string]
     *
     * @return array [files: [], newPath: string, tempPath: string]
     * @ignore
     */
    public function copyFileForApp($param)
    {
        if (empty($param['productName'])) {
            throw new \InvalidArgumentException(_p('Missing params [name]'));
        }
        if (isset($param['apps_dir']) && !empty($param['apps_dir'])) {
            $name = base64_decode($param['apps_dir']);
        } else {
            $name = $param['productName'];
        }
        $tempPath = $this->getTemporaryExtractAppDirectory($param);
        $newPath = realpath(PHPFOX_DIR_SITE) . PHPFOX_DS . 'Apps' . PHPFOX_DS
            . $name;

        if (is_dir($newPath)) {
            $this->_is_upgrade = true;
        }

        if (file_exists($tempPath . 'checksum.json')) {
            $fileList = json_decode(file_get_contents($tempPath
                . 'checksum.json'), true);
            if (isset($param['apps_dir']) && !empty($param['apps_dir'])) {
                foreach ($fileList as $iKey => $sFile) {
                    $fileList[$iKey] = str_replace("PF.Site/Apps/" . $param['productName'] . "/",
                        "PF.Site/Apps/" . base64_decode($param['apps_dir']) . "/", $sFile);
                }
            }
            $newPath = realpath(dirname(rtrim(PHPFOX_DIR, '/')));
        }
        if (empty($fileList)) {
            $result = $this->compareFiles($tempPath, $newPath);;
            $fileList = [];
            if (count($result['new'])) {
                foreach ($result['new'] as $file) {
                    $file = trim(str_replace($name, '', $file), PHPFOX_DS);
                    $fileList[] = $file;
                }
            }

            //case delete file we upgrade later
            if (count($result['update'])) {
                foreach ($result['update'] as $file) {
                    $file = trim(str_replace($name, '', $file), PHPFOX_DS);
                    $fileList[] = $file;
                }
            }
        }
        return [
            'files'    => array_values($fileList),
            'tempPath' => rtrim($tempPath, '/'),
            'newPath'  => rtrim($newPath),
        ];
    }

    /**
     * @param array $param
     *
     * @throws
     * @return string
     */
    public function runInstallForApp($param)
    {
        if (empty($param['productName'])) {
            throw new \InvalidArgumentException('Missing params [productName]');
        }

        $productName = $param['productName'];

        $is_upgrade = false;


        $autoloader = include PHPFOX_DIR . 'vendor' . PHPFOX_DS
            . 'autoload.php';
        if (isset($param['apps_dir']) && !empty($param['apps_dir'])) {
            $sAppDirName = base64_decode($param['apps_dir']);
        } else {
            $sAppDirName = $param['productName'];
        }
        $autoloader->setPsr4('Apps\\' . $productName . '\\',
            PHPFOX_PARENT_DIR . 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS
            . $sAppDirName);

        if ($this->_is_upgrade) {
            $is_upgrade = true;
        }

        $rename_on_upgrade = false;

        // check app rename on update
        $current = Phpfox::getLib('database')
            ->select('apps_dir')
            ->from(':apps')
            ->where("apps_id='$productName'")
            ->execute('getRow');

        if (null != $current) {

            if ($current['apps_dir'] == null) {
                $current['apps_dir'] = $productName;
            }

            if ($current['apps_dir'] != $sAppDirName) {

                Phpfox::getLib('database')
                    ->update(':apps', ['apps_dir' => $sAppDirName],
                        ['apps_id' => $productName]);
                $rename_on_upgrade = true;
            }
        }

        if ($rename_on_upgrade) {
            $url = Phpfox::getLib('url')->makeUrl('admincp.apps', [
                'rename_on_upgrade' => 1,
                'apps_dir'          => $sAppDirName,
                'apps_id'           => $productName,
                'is_upgrade'        => $is_upgrade,
            ]);

            Phpfox::getLib('cache')->remove();

            header('location: ' . $url);
            exit;
        }

        return $this->callRunInstallForApp($productName, $sAppDirName,
            $is_upgrade);

    }


    /**
     * @param array $param
     *
     * @return string
     */
    public function runInstallForTheme($param)
    {
        $temporaryDirectory = $this->getTemporaryExtractThemeDirectory($param);
        $extra = isset($param['extra']) ? $param['extra'] : [];
        $productId = $param['productId'];
        $exists = false;
        $themeService = new Theme();
        $fileList = null;
        $flavors = null;
        $name = null;

        if ($productId) {
            foreach ($themeService->all() as $theme) {
                if ($theme->internal_id == $productId) {
                    $exists = $theme;
                    break;
                }
            }
        }

        if (file_exists($temporaryDirectory . 'checksum.json')) {
            $data = json_decode(file_get_contents($temporaryDirectory
                . 'package.json'), true);
            $flavors = $data['flavors'];
            $fileList = json_decode(file_get_contents($temporaryDirectory
                . 'checksum.json'), true);
            $name = $data['name'];

        } else {
            $scanFiles = $this->scanFiles($temporaryDirectory, false);
            $jsonFile = null;

            foreach ($scanFiles as $fileName) {
                if (substr($fileName, -9) == '.zip.json') {
                    $jsonFile = $temporaryDirectory . $fileName;
                    break;
                }
            }

            $data = json_decode(file_get_contents($jsonFile), true);

            foreach ($data['files'] as $path => $content) {
                $filename = 'PF.Site/themes/0/' . $path;
                $fullname = $temporaryDirectory . $filename;
                if (!is_dir($dirname = dirname($fullname))) {
                    mkdir($dirname, 0777, true);
                    chmod($dirname, 0777);
                }
                file_put_contents($fullname, $content);
                @chmod($fullname, 0777);
                $fileList[] = $filename;
            }

            if (!isset($data['flavors'])) {
                $data['flavors'] = [$data['flavor_folder'] => $data['name']];
            }

            $flavors = $data['flavors'];
            $name = $data['name'];
        }
        $db = new Db();
        $cache = new Cache();
        $isUpdate = false;

        // copy file from theme to theme.
        if ($exists instanceof Object) {
            $isUpdate = $exists->theme_id;
            $db->update(':theme', ['website' => json_encode($extra)],
                ['theme_id' => $exists->theme_id]);
            $db->update(':setting', [
                'value_actual' => ((int)\Phpfox::getParam('core.css_edit_id')
                    + 1),
            ], 'var_name = \'css_edit_id\'');
            $cache->del('setting');
        }

        $existsTheme = null;

        if (!empty($extra)) {
            $websiteId = $extra['id'];

            $themes = $db->select('*')
                ->from(':theme', 't')
                ->all();
            foreach ($themes as $theme) {
                if (!empty($theme['website'])) {
                    $k = json_decode($theme['website'], true);

                    if ($websiteId == $k['id']) {
                        $existsTheme = $theme;
                        break;
                    }
                }
            }
        }

        if (!empty($existsTheme)) {
            $themeId = $existsTheme['theme_id'];
        } else {
            $themeId = $db->insert(':theme', [
                'name'      => $data['name'],
                'folder'    => '__',
                'website'   => (isset($extra) ? json_encode($extra) : null),
                'created'   => PHPFOX_TIME,
                'is_active' => 1,
            ]);
            $db->update(':theme', ['folder' => $themeId,],
                ['theme_id' => $themeId]);
        }

        $validateFileList = [];

        $folder = '0';

        if (!empty($fileList)) {
            $array = explode('/', $fileList[0]);
            $folder = $array[2];
        }

        foreach ($fileList as $filename) {
            $to_filename = str_replace('PF.Site/themes/' . $folder . '/',
                'PF.Site/themes/' . $themeId . '/', $filename);
            $validateFileList[$filename] = $to_filename;
        }

        // do not run vfs here, it's require only themes
        $rootPath = realpath(dirname(trim(PHPFOX_DIR, '/')));
        foreach ($validateFileList as $fromPath => $toPath) {
            $to_filename = $rootPath . $toPath;

            if (!is_dir($dir = dirname($to_filename))) {
                mkdir($dir, 0777, 1);
                chmod($dir, 0777);
            }
            if (!@copy($temporaryDirectory . $fromPath, $to_filename)) {
                $db->delete(':theme', ['theme_id' => $themeId]);
                throw new \RuntimeException(sprintf('Can not copy from "%s" to "%s"',
                    $temporaryDirectory . $fromPath, $to_filename));
            }
        }

        if (false == $isUpdate) {
            $iteration = 0;
            foreach ($flavors as $flavorId => $flavorName) {
                $iteration++;
                $db->insert(':theme_style', [
                    'theme_id'   => $themeId,
                    'name'       => $flavorName,
                    'folder'     => $flavorId,
                    'is_default' => ($iteration === 1 ? '1' : '0'),
                    'is_active'  => 1,
                    'created'    => PHPFOX_TIME,
                ]);
            }
        }

        $url = \Phpfox::getLib('url')
            ->makeUrl('admincp.theme.manage', ['id' => $themeId]);

        return $url;
    }

    /**
     * @param array $param
     *
     * @return string
     */
    public function runInstallForModule($param)
    {
        $bOverwrite = true;
        $bIsUpdate = false;
        $sProductFile = $param['productId'];
        $aCache = [];
        $aModuleInstall = [];
        $xmlFileName = PHPFOX_DIR_XML . $sProductFile . '.xml';

        if (!file_exists($xmlFileName)) {
            throw new \RuntimeException(_p('unable_to_find_xml_file_to_import_for_this_product'
                . $xmlFileName));
        }

        $aParams = Phpfox::getLib('xml.parser')
            ->parse(file_get_contents($xmlFileName));

        if (isset($aParams['modules'])) {
            $aModules = (is_array($aParams['modules']['module_id'])
                ? $aParams['modules']['module_id']
                : [$aParams['modules']['module_id']]);
            foreach ($aModules as $sModule) {
                $aModuleInstall[$sModule] = [
                    'table'     => 'true',
                    'installed' => 'false',
                ];
            }
        }


        foreach ($aCache as $sModuleCheck => $mTrue) {
            if (!Phpfox::isModule($sModuleCheck)) {
                throw new \RuntimeException(_p('the_module_name_is_required',
                    ['name' => $sModuleCheck]));
            }
        }

        if (!isset($aParams['data']['product_id'])) {
            throw new \RuntimeException(_p('not_a_valid_xml_file'));
        }


        Phpfox::getLib('cache')->lock();
        $productService = Phpfox::getService('admincp.product.process');

        $bIsProduct = $productService->isProduct($aParams['data']['product_id']);
        // Upgrade or Install ?
        if ($bIsProduct) {
            $productService->upgrade($param['productId']);
        } else {
            if (isset($aParams['dependencies'])) {
                $aDependencies
                    = (isset($aParams['dependencies']['dependency'][1])
                    ? $aParams['dependencies']['dependency']
                    : [$aParams['dependencies']['dependency']]);
                foreach ($aDependencies as $aDependancy) {
                    if (!isset($aDependancy['type_id'])
                        || !isset($aDependancy['dependency_start'])
                    ) {
                        continue;
                    }

                    switch ($aDependancy['type_id']) {
                        case 'php':
                            if (version_compare(PHP_VERSION,
                                $aDependancy['dependency_start'], '<')) {
                                throw new \RuntimeException(_p('product_requires_php_version',
                                    ['dependency_start' => $aDependancy['dependency_start']]));
                            }

                            if (isset($aDependancy['dependency_end'])
                                && $aDependancy['dependency_end'] != ''
                            ) {
                                if (version_compare(PHP_VERSION,
                                    $aDependancy['dependency_end'], '>')) {
                                    throw new \RuntimeException(_p('product_requires_php_version_up_until',
                                        [
                                            'dependency_start' => $aDependancy['dependency_start'],
                                            'dependency_end'   => $aDependancy['dependency_end'],
                                        ]));
                                }
                            }
                            break;
                        case 'phpfox':
                            if (version_compare(Phpfox::getVersion(),
                                $aDependancy['dependency_start'], '<')) {
                                throw new \RuntimeException(_p('product_requires_phpfox_version',
                                    [
                                        'dependency_start',
                                        $aDependancy['dependency_start'],
                                    ]));
                            }

                            if (isset($aDependancy['dependency_end'])
                                && $aDependancy['dependency_end'] != ''
                            ) {
                                if (version_compare(Phpfox::getVersion(),
                                    $aDependancy['dependency_end'], '>')) {
                                    throw new \RuntimeException(_p('product_requires_phpfox_version_up_until',
                                        [
                                            'dependency_start' => $aDependancy['dependency_start'],
                                            'dependency_end'   => $aDependancy['dependency_end'],
                                        ]));
                                }
                            }
                            break;
                        case 'product':
                            if (!isset($aDependancy['check_id'])) {
                                continue;
                            }

                            $aProductVersion
                                = $productService->getProductDependency($aDependancy['check_id']);

                            if (isset($aProductVersion['product_id'])) {
                                if (version_compare($aProductVersion['version'],
                                    $aDependancy['dependency_start'], '<')) {
                                    throw new \RuntimeException(_p('product_requires_check_id_version_dependency_start',
                                        [
                                            'check_id'         => $aProductVersion['title'],
                                            'dependency_start' => $aDependancy['dependency_start'],
                                        ]));
                                }

                                if (!empty($aDependancy['dependency_end'])) {
                                    if (version_compare($aProductVersion['version'],
                                        $aDependancy['dependency_end'], '>')) {
                                        throw new \RuntimeException(_p('product_requires_check_id_version_dependency_start_up_until_dependency_end',
                                            [
                                                'check_id'         => $aProductVersion['title'],
                                                'dependency_start' => $aDependancy['dependency_start'],
                                                'dependency_end'   => $aDependancy['dependency_end'],
                                            ]));
                                    }
                                }
                            } else {

                                throw new \RuntimeException(_p('product_requires_check_id_version_dependency_start',
                                    [
                                        'check_id'         => $aDependancy['check_id'],
                                        'dependency_start' => $aDependancy['dependency_start'],
                                    ]));

                            }
                            break;
                        default:

                            break;
                    }
                }
            }

            $addResult = $productService->add([
                'product_id'        => $aParams['data']['product_id'],
                'title'             => $aParams['data']['title'],
                'description'       => (empty($aParams['data']['description'])
                    ? null : $aParams['data']['description']),
                'version'           => (empty($aParams['data']['version'])
                    ? null : $aParams['data']['version']),
                'is_active'         => 1,
                'url'               => (empty($aParams['data']['url']) ? null
                    : $aParams['data']['url']),
                'url_version_check' => (empty($aParams['data']['url_version_check'])
                    ? null : $aParams['data']['url_version_check']),
                'icon'              => (empty($aParams['data']['icon']) ? null
                    : $aParams['data']['icon']),
                'vendor'            => (empty($aParams['data']['vendor']) ? null
                    : $aParams['data']['vendor']),
            ], $bIsUpdate);

            if (!$addResult) {
                throw new \RuntimeException('Can not insert product'
                    . Phpfox_Error::get());
            }


            if (!empty($aParams['dependencies'])) {
                $aDependencies
                    = (isset($aParams['dependencies']['dependency'][1])
                    ? $aParams['dependencies']['dependency']
                    : [$aParams['dependencies']['dependency']]);
                foreach ($aDependencies as $aDependancy) {
                    $aDependancy['product_id'] = $aParams['data']['product_id'];

                    $productService->addDependency($aDependancy);
                }
            }


            if (!empty($aParams['installs'])) {
                $aInstalls = (isset($aParams['installs']['install'][1])
                    ? $aParams['installs']['install']
                    : [$aParams['installs']['install']]);
                $aInstallOrder = [];
                foreach ($aInstalls as $aInstall) {
                    $aInstallOrder[$aInstall['version']] = $aInstall;

                    $aInstall['product_id'] = $aParams['data']['product_id'];

                    $productService->addInstall($aInstall);
                }

                sort($aInstallOrder);

                try {
                    $productService->invokeInstallCode($aInstallOrder);
                } catch (\Exception $ex) {
                    throw new \RuntimeException($ex->getMessage());
                }

            }


            $moduleService = Phpfox::getService('admincp.module.process');
            $moduleService->processInstall($sProductFile, $aModuleInstall,
                null);

            Phpfox::getLib('cache')->unlock();
            Phpfox::getLib('cache')->remove();
        }

        flavor()->rebuild_bootstrap();

        return \Phpfox::getLib('url')->makeUrl('admincp.apps', []);
    }

    /**
     * @param array $param
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function runInstall($param)
    {
        if (empty($param['type'])) {
            throw new \InvalidArgumentException('Missing params [name, type]');
        }

        $returnUrl = null;

        switch ($param['type']) {
            case 'app':
            case 'application':
                $returnUrl = $this->runInstallForApp($param);
                break;
            case 'theme':
            case 'flavor':
                $returnUrl = $this->runInstallForTheme($param);
                break;
            case 'module':
            case 'product':
                $returnUrl = $this->runInstallForModule($param);
                break;
            case 'language':

                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unexpected copy file for type [%s]',
                    $param['type']));
                break;
        }

        return $returnUrl;
    }

    /**
     * Clean temporary directory after install
     * TODO: correct path?
     */
    private function cleanInstall()
    {
        $path = PHPFOX_DIR_FILE . 'temp';
        $this->removeDir($path);
    }

    /**
     * @param string $path
     * @param bool   $removeRoot
     */

    private function removeDir($path, $removeRoot = false)
    {
        if (!is_dir($path)) {
            return;
        }

        $items = scandir($path);
        foreach ($items as $item) {
            if (in_array($item, ['.', '..'])) {
                continue;
            }
            $filePath = $path . PHPFOX_DS . $item;
            /**
             * Check writeable
             */
            if (!is_writable($filePath)) {
                continue;
            }
            is_dir($filePath) ? $this->removeDir($filePath, true)
                : @unlink($filePath);
        }

        if ($removeRoot && is_dir($path)) {
            rmdir($path);
        }
    }

    /**
     * Do something before install
     *
     * @param $param
     */
    private function onBeforeInstall($param)
    {

    }

    private function onAfterInstall($param)
    {
        //remove temp dir after copy
        switch ($param['type']) {
            case 'app':
            case 'application':
                $tempDir = $this->getTemporaryExtractAppDirectory($param);
                break;
            case 'theme':
            case 'flavor':
                $tempDir = $this->getTemporaryExtractThemeDirectory($param);
                break;
            case 'module':
            case 'product':
                $tempDir = $this->getTemporaryExtractProductDirectory($param);
                break;
            case 'language':
                //TODO find the temp folder when install language
//                $tempDir = $this->verifyFilesystemForLanguage($param);
                $tempDir = false;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unexpected params type: %s', $param['type']));
        }
        if (!empty($tempDir) && is_dir($tempDir)) {
            $this->managerControl->deleteDir($tempDir);
        }
    }

    /**
     * Image is app, theme, module, language ?
     *
     * @param array $param
     *
     * @return array
     */
    public function verifyFilesystem($param)
    {

        if (empty($param['type']) or empty($param['filename'])) {
            throw new \InvalidArgumentException('Missing params [type, filename]');
        }

        $result = null;

        switch ($param['type']) {
            case 'app':
            case 'application':
                $result = $this->verifyFilesystemForApp($param);
                break;
            case 'theme':
            case 'flavor':
                $result = $this->verifyFilesystemForTheme($param);
                break;
            case 'module':
            case 'product':
                $result = $this->verifyFilesystemForModule($param);
                break;
            case 'language':
                $result = $this->verifyFilesystemForLanguage($param);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unexpected params type: %s',
                    $param['type']));
        }

        return $result;

    }

    /**
     * @param array $param
     *
     * @return array
     */
    public function verifyFilesystemForTheme($param)
    {
        if (empty($param['filename'])) {
            throw new \InvalidArgumentException('Missing params [filename]');
        }

        $productId = empty($param['productId']) ? 0 : $param['productId'];

        $zip = $param['filename'];
        $temporaryDirectory = $this->getTemporaryExtractThemeDirectory($param);

        $Zip = new \ZipArchive();
        $Zip->open($zip);
        $Zip->extractTo($temporaryDirectory);
        $Zip->close();

        $themeName = null;
        $File = \Phpfox_File::instance();

        $result = [];

        foreach (scandir($temporaryDirectory) as $f) {
            if ($File->extension($f) == 'json') {
                $data = json_decode(file_get_contents($temporaryDirectory . $f),
                    true);
                $themeName = $data['name'];
                foreach ($data['files'] as $fileName => $fileData) {
                    $temporaryFilename = $temporaryDirectory . '/' . $fileName;
                    $dirname = dirname($temporaryFilename);

                    if (!is_dir($dirname)) {
                        if (!mkdir($dirname, 0777, true)) {
                            throw new \RuntimeException(sprintf('Could not open "%s" to write new file',
                                $temporaryFilename));
                        }
                        chmod($dirname, 0777);
                    }
                    file_put_contents($temporaryFilename, $fileData);
                    chmod($temporaryFilename, 0777);
                }
            }
        }

        $result['new'] = $this->scanFiles($temporaryDirectory, false);
        $result['upgrade'] = [];
        $result['remove'] = [];
        $result['productName'] = $themeName;
        $result['productId'] = $productId;
        $result['type'] = 'theme';

        return $result;
    }

    public function verifyFilesystemForLanguage($param)
    {
        $sourceFilename = $param['filename'];
        $targetDirectory = str_replace('import.zip', 'extract/',
            $sourceFilename);

        try {
            $zipArchive = new \ZipArchive();
            $zipArchive->open($sourceFilename);
            $zipArchive->extractTo($targetDirectory);
            $zipArchive->close();
        } catch (\Exception $ex) {
            throw new \RuntimeException('Missing or invalid zip filename');
        }

        // compare hashed files.
        $result = $this->compareFiles($targetDirectory, []);
        $result['targetDirectory'] = $targetDirectory;

        return $result;
    }

    /**
     * @param array $param
     *
     * @return array
     */
    public function verifyFilesystemForModule($param)
    {
        if (empty($param['filename'])) {
            throw new \InvalidArgumentException('Missing params [filename]');
        }

        $sourceFilename = $param['filename'];
        $targetDirectory = $this->getTemporaryExtractProductDirectory($param);

        if (!is_dir($targetDirectory)) {
            if (!@mkdir($targetDirectory, 0777, true)) {
                throw new \RuntimeException("Could not make dir $targetDirectory");
            }
            chmod($targetDirectory, 0777);
        }
        try {
            $zipArchive = new \ZipArchive();
            $zipArchive->open($sourceFilename);
            $zipArchive->extractTo($targetDirectory);
            $zipArchive->close();
        } catch (\Exception $ex) {
            throw new \RuntimeException('Missing or invalid zip filename');
        }

        // compare hashed files.
        $result = $this->compareFiles($targetDirectory, []);

        register_shutdown_function(function () use ($sourceFilename) {
//            unlink($filename);
        });

        return $result;
    }

    /**
     * @param array $param
     *
     * @return array
     * @throws \RuntimeException|\InvalidArgumentException
     */
    public function verifyFilesystemForApp($param)
    {

        if (empty($param['filename']) or empty($param['productId'])) {
            throw new \InvalidArgumentException('Missing params [filename, productId]');
        }

        $filename = $param['filename'];
        $appId = $param['productId'];

        $archive = new \ZipArchive();

        $openResult = $archive->open($filename);

        if ($openResult !== true) {
            throw new \RuntimeException(sprintf('Can not open "%s"',
                $filename));
        }

        $base = $this->getTemporaryExtractAppDirectory($param);

        if (!is_dir($base)) {
            if (!@mkdir($base, 0777, true)) {
                throw new \RuntimeException(_p('could_not_make_dir_base', ['base' => $base]));
            }
        }

        $archive->close();

        //Remove old package if available
        if (isset($param['apps_dir']) && !empty($param['apps_dir'])) {
            $sNewAppDir = $base . 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS
                . base64_decode($param['apps_dir']);
            if (is_dir($sNewAppDir)) {
                $this->managerControl->deleteDir($sNewAppDir);
            }
        }
        
        $newZip = new \ZipArchive();
        $newZip->open($filename);
        $newZip->extractTo($base);
        $newZip->close();
        //Rename dir
        if (isset($param['apps_dir']) && !empty($param['apps_dir'])) {
            $sAppDir = $base . 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS
                . $appId;
            $sNewAppDir = $base . 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS
                . base64_decode($param['apps_dir']);

            if (is_dir($sAppDir)) {
                @rename($sAppDir, $sNewAppDir);
            }
        }
        //Delete zip file after extract
        @unlink($filename);
        @rmdir(dirname($filename));
        $result = $this->compareFiles($base,
            $old = PHPFOX_DIR_SITE . '/PF.Site/Apps/' . $appId);

        register_shutdown_function(function () use ($filename) {
//            unlink($filename);
        });
        $result['productName'] = $appId;

        return $result;
    }

    /**
     * @param array $newFileList List of new files
     * @param array $oldFileList list of old files
     *
     * @return array [upgrade, new, remove]
     */
    public function compareFileList($newFileList, $oldFileList)
    {
        $newFile = array_diff($newFileList, $oldFileList);
        $removeFile = array_diff($oldFileList, $newFileList);
        $override = array_intersect($oldFileList, $newFileList);

        return [
            'new'    => $newFile,
            'remove' => $removeFile,
            'update' => $override,
        ];
    }


    /**
     * @param array $newDirList
     * @param array $oldDirList
     *
     * @return array  [new: array, remove: array, update: array]
     */
    public function compareFiles($newDirList, $oldDirList)
    {

        $oldFileList = $this->scanFiles($oldDirList, false);
        $newFileList = $this->scanFiles($newDirList, false);

        $newFile = array_diff($newFileList, $oldFileList);
        $removeFile = array_diff($oldFileList, $newFileList);
        $override = array_intersect($oldFileList, $newFileList);

        return [
            'new'    => $newFile,
            'remove' => $removeFile,
            'update' => $override,
        ];
    }

    /**
     * Get list all files recursive
     *
     * @param string $directories
     * @param bool   $includeParentDirectory Add parent directory to result.
     *
     * @return array
     */
    public function scanFiles($directories, $includeParentDirectory = true)
    {
        if (is_string($directories)) {
            $directories = [$directories];
        }

        $result = [];
        foreach ($directories as $directory) {
            $rootPath = $includeParentDirectory ? realpath(dirname($directory))
                : realpath($directory);
            if (!is_dir($directory)) {
                continue;
            }
            $iterator
                = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory),
                null);

            foreach ($iterator as $fileInfo) {
                if (false == $fileInfo->isFile()) {
                    continue;
                }
                $path = str_replace($rootPath, '', $fileInfo->getPathName());
                $path = trim($path, DIRECTORY_SEPARATOR);
                $result[] = $path;
            }
        }

        return $result;
    }

    public function verifyFtpAccount()
    {
        if ($this->vfs == 'file_system') {
            //file system don't need to check here
            return true;
        }

        return $this->managerControl->connect();
    }

    public function setToPath($path)
    {
        $this->managerControl->setToPath($path);
    }

    public function deleteDir($path)
    {
        $this->managerControl->connect();
        $this->managerControl->deleteDir($path);
    }

    public function deleteFile($path, $check_dir = false)
    {
        $this->managerControl->deleteFile($path, $check_dir);
    }

    /**
     * @param $productName
     * @param $sAppDirName
     * @param $is_upgrade
     *
     * @return string
     * @throws \Exception
     */
    public function callRunInstallForApp(
        $productName,
        $sAppDirName,
        $is_upgrade
    ) {
        $base = realpath(PHPFOX_DIR_SITE) . PHPFOX_DS . 'Apps' . PHPFOX_DS
            . $sAppDirName . PHPFOX_DS;;
        $baseTemp = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'phpfox-app-'
            . $productName . PHPFOX_DS;

        //Save file app.lock in temp folder
        $lockPath = $baseTemp . 'app.lock';
        if (file_exists($lockPath)) {
            unlink($lockPath);
        }


        $oApp = new App();

        if (!$json = \Core\Lib::appInit($productName, $sAppDirName)) {
            $json = file_get_contents($base . 'app.json');
            $json = json_decode($json);
            if ($is_upgrade) {
                $oApp->processUpgrade($json, $base);
            } else {
                $oApp->processJson($json, $base);
            }
        } else {
            if (!$json->isValid()) {
                throw new \Exception(implode("\n", $json->getErrorMessages()));
            }
            $json->processInstall();
        }

        $CoreApp = new App(true);
        $CoreApp->add($json->id);
        $Object = $CoreApp->get($json->id);

        if (!$is_upgrade) {
            $internalId = 0;
            $requestProduct = Phpfox::getCookie('product');
            $requestAuthId = Phpfox::getCookie('auth_id');
            $requestAuthKey = Phpfox::getCookie('auth_key');
            if ($requestProduct) {
                $product = json_decode($requestProduct);
                $internalId = $product->id;
            }
            $oApp->makeKey($Object, $requestAuthId, $requestAuthKey,
                $internalId);
        }

        return Phpfox::getLib('url')->makeUrl('admincp.app',
            ['id' => $Object->id, 'verify' => $is_upgrade]);
    }
}
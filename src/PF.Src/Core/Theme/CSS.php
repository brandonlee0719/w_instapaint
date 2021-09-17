<?php

namespace Core\Theme;

class CSS extends \Core\Model
{
    private $_theme;

    public function __construct(\Core\Theme\Object $Theme)
    {
        parent::__construct();

        $this->_theme = $Theme;
    }

    /**
     * @param        $content
     * @param null   $vars
     * @param string $more_content will not add to less files
     * @param string $themeName
     *
     * @return bool
     */
    public function set($content, $vars = null, $more_content = '', $themeName = 'default')
    {
        $less = new \lessc();
        $lessContent = trim($content);
        if ($more_content == 1) {
            $more_content = '';
        }

        $content = '@import "init"; ' . PHP_EOL . $lessContent . '@import "autoload";' . PHP_EOL . trim($more_content);
        // Compatible earlier 4.1.1
        $content = str_replace('../../../../PF.Base/less/autoload', 'compatible_old_410', $content);

        // asumptions
        $lessInputFileName = $this->_theme->getFlavorPath() . 'root.less';
        $cssOutputFileName = $this->_theme->getFlavorPath() . $this->_theme->flavor_folder . '.css';

        $optimizer = new \Core\Theme\Optimizer();
        $optimizer->setImportPaths([
            PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS . 'less' . PHPFOX_DS,
            realpath(dirname(PHPFOX_DIR_MODULE)),
            PHPFOX_DIR . 'less' . PHPFOX_DS,
        ]);
        $optimizedContent = $optimizer->optimize($content);
        file_put_contents($lessInputFileName, $optimizedContent);
        chmod($lessInputFileName, 0777);

        if($this->ping_remote()){
            $this->compile_remote($lessInputFileName, $cssOutputFileName);
            return true;
        }

        // add default import dirs.
        $less->addImportDir(PHPFOX_DIR . 'less' . PHPFOX_DS);
        $less->addImportDir(PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS . 'less' . PHPFOX_DS);
        $less->addImportDir(realpath(dirname(PHPFOX_DIR_MODULE)));
        // enable cache options
        $less->setOption('cache_dir', PHPFOX_DIR_FILE . 'cache/less');

        $parsed = null;

        try {
            $pf = \Core\Profiler::start('$less->parse');
            $parsed = $less->compileFile($lessInputFileName);
            //Add static CSS here
            $parsed .= $this->getCssData();
            file_put_contents($cssOutputFileName, $parsed);
            \Core\Profiler::end($pf);
        } catch (\Exception $ex) {
            if (PHPFOX_DEBUG) {
                \Phpfox_Error::trigger($ex->getMessage(), E_USER_ERROR);
            }
        }

        $path = $this->_theme->getFlavorPath() . $this->_theme->flavor_folder;

        $this->db->update(':setting', ['value_actual' => ((int)\Phpfox::getParam('core.css_edit_id') + 1)], 'var_name = \'css_edit_id\'');

        $this->cache->del('setting');

        return true;
    }

    public function ping_remote()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://v4.phpfox.com/lessc/?ping=1');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $content = curl_exec($ch);
        curl_close($ch);

        return $content === "1";
    }

    public function compile_remote($root_file, $target)
    {
        if (function_exists('curl_file_create')) { // php 5.5+
            $cFile = curl_file_create($root_file);
        } else { //
            $cFile = '@' . realpath($root_file);
        }
        $post = ['extra_info' => '123456', 'less' => $cFile];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://v4.phpfox.com/lessc/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        if($content and substr($content,0,4) == 'http'){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $content);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $css = curl_exec($ch);
            curl_close($ch);
            file_put_contents($target, $css);
        }
        return true;
    }

    /**
     * @param bool $returnLess
     *
     * @return string
     * @deprecated
     */
    public function get($returnLess = false)
    {
        return '';
    }

    /**
     * Get content of less files from all modules
     *
     * @param $module_list
     *
     * @return string
     */
    public function getModule($module_list)
    {
        if (is_array($module_list)) {
            $less_contain = '';
            foreach ($module_list as $module_name) {
                $file_name = PHPFOX_DIR_MODULE . $module_name . PHPFOX_DS . 'static' . PHPFOX_DS . 'css' . PHPFOX_DS . \Phpfox_Template::instance()->getThemeFolder() . PHPFOX_DS
                    . \Phpfox_Template::instance()->getStyleFolder() . PHPFOX_DS . 'main.less';
                if (file_exists($file_name)) {
                    $less_contain .= "\n/*Begin $module_name*/\n" . file_get_contents($file_name) . "\n/*End $module_name*/\n";
                }
            }

            return $less_contain;
        } else {
            return '';
        }
    }

    /**
     * @param array $moduleLists
     *
     * @throws \Exception
     */
    public function reBuildModule($moduleLists)
    {
        $buildFiles = [];
        if (is_array($moduleLists)) {
            foreach ($moduleLists as $moduleName) {
                $modulePath = PHPFOX_DIR_MODULE . $moduleName . PHPFOX_DS . 'static' . PHPFOX_DS . 'css' . PHPFOX_DS . \Phpfox_Template::instance()->getThemeFolder() . PHPFOX_DS
                    . \Phpfox_Template::instance()->getStyleFolder();
                $moduleFiles = $this->scanLessFiles($modulePath);
                $buildFiles = array_merge($buildFiles, $moduleFiles);
            }
        }
        if (count($buildFiles)) {
            foreach ($buildFiles as $fileName) {
                $path = $this->_theme->getFlavorPath() . substr(str_replace([PHPFOX_DS, '/', '\\'], ['_', '_', '_'], $fileName), 0, -4);
                $path = trim($path, '.');
                if (file_exists($path . '.css')) {
                    unlink($path . '.css');
                }
            }
        }
    }

    /**
     * @param string $fileName
     * @param string $locationBuild
     * @param string $themeName
     *
     * @throws \Exception
     */
    public function buildFile($fileName, $locationBuild = 'module', $themeName = 'default')
    {
        switch ($locationBuild) {
            case 'app':
                $suffixPath = dirname(PHPFOX_DIR_SITE) . PHPFOX_DS;//check later
                break;
            case 'static':
                $suffixPath = '';//check later
                break;
            case 'module':
                $suffixPath = PHPFOX_DIR_MODULE;
                break;
            case 'developing':
                $suffixPath = PHPFOX_DIR . 'less' . PHPFOX_DS;
                $fileName = 'developing.less';
                break;
            default:
                $suffixPath = PHPFOX_DIR_MODULE;
                break;
        }

        //get less variable and remove import
        $variable = $this->get(true);
        $aVariable = explode(';', $variable);
        foreach ($aVariable as $key => $var) {
            $string = str_replace("\r", "", $var);
            $string = str_replace("\n", "", $string);
            $string = trim(trim($string, '\n'), ' ');
            if (strpos($string, '@import') !== false) {
                $variable = str_replace($string . ';', '', $variable);
            }
        }

        $less = new \lessc();

        //build
        $lessContent = $variable . file_get_contents($suffixPath . $fileName);

        // add default import dirs.
        $less->addImportDir($this->_theme->getFlavorPath() . 'less' . PHPFOX_DS);
        $less->addImportDir(PHPFOX_DIR . 'less' . PHPFOX_DS);

        // generate import and others
        $content = '@import "init";' . PHP_EOL . $lessContent;


        $content = str_replace('../../../../PF.Base/less/autoload', 'compatible_old_410', $content);
        $parsed = null;

        // skip autoload and others.
        $content = preg_replace('/@import\s+"\.\.\/\.\.\/\.\.\/\.\.\/PF\.Base\/less\/variables";/', '', $content);

        try {
            $parsed = $less->compile($content);
        } catch (\Exception $ex) {
            if (isset($_REQUEST['debug'])) {
                exit($fileName . ':' . $ex->getMessage());
            }
            if (PHPFOX_DEBUG) {
                \Phpfox_Error::trigger($ex->getMessage(), E_USER_ERROR);
            }
        }

        $path = $this->_theme->getFlavorPath() . substr(str_replace([PHPFOX_DS, '/', '\\'], ['_', '_', '_'], $fileName), 0, -4);
        $path = trim($path, '.');
        if ($locationBuild == 'developing' && CSS_DEVELOPMENT_MODE) {
            //Include static css and autoload css
            $Apps = new \Core\App();
            $sCssData = '';
            $sCssDir = PHPFOX_DIR . 'less' . PHPFOX_DS . 'css' . PHPFOX_DS;
            if (is_dir($sCssDir)) {
                $ffs = scandir($sCssDir);
                foreach ($ffs as $ff) {
                    $fileExtension = substr($ff, -4);
                    if ($fileExtension == '.css') {
                        $sCssData .= file_get_contents($sCssDir . PHPFOX_DS . $ff);
                    }
                }
            }
            $parsed .= $sCssData;
        }
        file_put_contents($path . '.css', $parsed);
    }

    /**
     * Get content of less files from all Apps
     *
     * @return string
     */
    public function getApp()
    {
        $Apps = new \Core\App();
        $app_less_contain = '';
        foreach ($Apps->all() as $App) {
            $assets = $App->path . 'assets' . PHPFOX_DS;
            if (file_exists($assets . 'main.less')) {
                $app_less_contain .= '/*Begin ' . $App->name . '*/' . file_get_contents($assets . 'main.less') . '/*End ' . $App->name . '*/';
            }
        }

        return $app_less_contain;
    }

    /**
     * Get content of less files from all Apps
     *
     * @return string
     */
    public function getCssData()
    {
        if (CSS_DEVELOPMENT_MODE) {
            //In css development mode, these css always change, so we don't include them when rebuild. They always reload each time reload.
            return '';
        }
        $sCachedId = \Phpfox_Cache::instance()->set('css_data_content');
        $sCssData = \Phpfox_Cache::instance()->get($sCachedId);
        if (!$sCssData) {
            $Apps = new \Core\App();
            $sCssData = '';
            $sCssDir = PHPFOX_DIR . 'less' . PHPFOX_DS . 'css' . PHPFOX_DS;
            if (is_dir($sCssDir)) {
                $ffs = scandir($sCssDir);
                foreach ($ffs as $ff) {
                    $fileExtension = substr($ff, -4);
                    if ($fileExtension == '.css') {
                        $sCssData .= file_get_contents($sCssDir . PHPFOX_DS . $ff);
                    }
                }
            }
            \Phpfox_Cache::instance()->save($sCachedId, $sCssData);
        }

        return $sCssData;
    }

    public function getParsed()
    {
        $css = $this->_theme->getPath() . 'flavor/' . $this->_theme->flavor_folder . '.css';
        $css = file_get_contents($css);

        return $css;
    }

    public function scanLessFiles($path)
    {
        if (!is_dir($path)) {
            return [];
        }
        $ffs = scandir($path);
        $extension = ['.css', 'less'];
        $listFiles = [];
        foreach ($ffs as $ff) {
            if ($ff != '.' && $ff != '..' && $ff != 'main.less') {
                if (is_dir($path . PHPFOX_DS . $ff)) {
                    $sub = $this->scanLessFiles($path . PHPFOX_DS . $ff);
                    $listFiles = array_merge($listFiles, $sub);
                } else {
                    $fileExtension = substr($ff, -4);
                    $bGet = true;
                    //if have same file name in .css and .less, remove .css file
                    if ($fileExtension == '.css') {
                        $checkLess = substr($ff, 0, -4) . '.less';
                        if (in_array($checkLess, $ffs)) {
                            $bGet = false;
                        }
                    }
                    if (in_array($fileExtension, $extension) && $bGet) {
                        $listFiles[] = (str_replace(PHPFOX_DIR_MODULE, '', $path)) . PHPFOX_DS . $ff;
                    }
                }
            }
        }

        return $listFiles;
    }
}
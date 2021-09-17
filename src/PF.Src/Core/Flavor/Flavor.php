<?php

namespace Core\Flavor;

use Core\Installation\FileHelper;
use Core\Phrase;

class Object
{
    public $id;
    public $name;
    public $vars;
    public $path;
    public $url;
    public $icon = '';
    public $legacy = ['theme' => 'bootstrap', 'flavor' => 'bootstrap'];
    public $blocks = [];
    public $store_id;
    public $version;

    public function __construct($path)
    {
        $this->path = $path;
        $this->url = str_replace([PHPFOX_DIR_SITE, PHPFOX_DS], [\Phpfox::getLib('cdn')->getUrl(setting('core.path_actual')) . 'PF.Site/', '/'], $this->path);
        if (file_exists($this->path . 'theme.png')) {
            $this->icon = $this->url . 'theme.png?v=' . uniqid();
        }
        $this->legacy = (object)$this->legacy;
        $this->blocks = (object)$this->blocks;

        $json = json_decode(file_get_contents($path . 'theme.json'));
        foreach ($json as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function save($type, $values, $sub_type = '')
    {
        $dir = $this->path;
        switch ($type) {
            case 'settings':
                $file = $this->path . 'theme.json';
                file_put_contents($file, $values);
                break;
            case 'icon':
                $file = $this->path . 'theme.png';
                $url = $this->url . 'theme.png';

                move_uploaded_file($values['tmp_name'], $file);

                return $url;

                break;
            case 'content':
                storage()->del('flavor/content/' . $this->id);
                storage()->set('flavor/content/' . $this->id, $values);

                break;
            case 'html':
                $path = $this->path . 'html/layout.html';

                file_put_contents($path, $values);

                break;
            case 'js':
                $path = $this->path . 'assets/autoload.js';

                file_put_contents($path, $values);

                break;
            case 'banners':
                $dir = $this->path . 'assets' . PHPFOX_DS . 'banners' . PHPFOX_DS;
                if (!is_dir($dir)) {
                    mkdir($dir);
                }
                $hash = $values['name'];

                move_uploaded_file($values['tmp_name'], $dir . $hash);

                return str_replace(PHPFOX_DS, '/', str_replace(PHPFOX_DIR_SITE, home() . 'PF.Site/', $dir . $hash));

                break;
            case 'logos':
                $dir = $dir . 'assets' . PHPFOX_DS . $sub_type . PHPFOX_DS;
                if (!is_dir($dir)) {
                    mkdir($dir);
                }
                $ext = \Phpfox_File::instance()->getFileExt($values['name']);
                $hash = md5(uniqid()) . '.' . $ext;
                $id = 'flavor/' . $sub_type . '/' . $this->id;

                move_uploaded_file($values['tmp_name'], $dir . $hash);

                storage()->del($id);
                storage()->set($id, $hash);

                break;

            case 'default_photo':
                $dir = $dir . 'assets' . PHPFOX_DS . 'defaults' . PHPFOX_DS;
                if (!is_dir($dir)) {
                    mkdir($dir);
                }
                $ext = \Phpfox_File::instance()->getFileExt($values['name']);
                $name = $sub_type . '.' . $ext;
                $id = 'flavor/defaults/' . $this->id;

                $data = (storage()->get($id)) ? json_decode(json_encode(storage()->get($id)->value), true) : [];
                if (isset($data[$sub_type]) && file_exists($dir . $data[$sub_type])) {
                    unlink($dir . $data[$sub_type]);
                }
                $data[$sub_type] = $name;

                move_uploaded_file($values['tmp_name'], $dir . $name);

                storage()->del($id);
                storage()->set($id, $data);

                break;

            case 'remove_default':
                $dir = $dir . 'assets' . PHPFOX_DS . 'defaults' . PHPFOX_DS;
                $id = 'flavor/defaults/' . $this->id;

                $data = (storage()->get($id)) ? json_decode(json_encode(storage()->get($id)->value), true) : [];
                if (isset($data[$sub_type]) && file_exists($dir . $data[$sub_type])) {
                    unlink($dir . $data[$sub_type]);
                    unset($data[$sub_type]);
                }
                storage()->del($id);
                storage()->set($id, $data);
                break;

            case 'css':
                $less_file = $this->path . 'assets/autoload.less';
                $css_file = $this->path . 'assets/autoload.css';

                file_put_contents($less_file, $values);

                $lessc = new \lessc();
                $lessc->addImportDir($this->path . 'assets/');
                $lessc->compileFile($less_file, $css_file);

                break;
            case 'design':
                $less = '';
                $theme_suffix = request()->get('theme_suffix');
                $theme_suffix_file = null;
                $less_file = $this->path . 'assets' . PHPFOX_DS . 'variables.less';

                if ($theme_suffix) {
                    $theme_suffix_file = $this->path . 'assets' . PHPFOX_DS . 'variables' . $theme_suffix . '.less';
                    if (!file_exists($theme_suffix_file)) {
                        $theme_suffix_file = null;
                    }
                }

                if ($theme_suffix_file) {
                    $less_file = $theme_suffix_file;
                }

                if (!file_exists($less_file)) {
                    $lines = '';
                    foreach ($this->vars as $var => $value) {
                        $lines .= "@{$var}:{$value->value};\n";
                    }
                    file_put_contents($less_file, $lines);
                }


                $less_input = $this->path . 'assets/autoload.less';
                $css_output = $this->path . 'assets/autoload.css';
                if (!file_exists($less_input)) {
                    file_put_contents($less_input, "\n@import \"variables\";\n" . file_get_contents($css_output));
                }

                $lines = file($less_file);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) {
                        continue;
                    }

                    if (substr($line, 0, 1) == '@') {
                        $parts = array_map('trim', explode(':', $line));
                        $var = str_replace('@', '', $parts[0]);
                        if (!isset($parts[1])) {
                            continue;
                        }
                        $value = trim(explode(';', $parts[1])[0]);

                        if (isset($values[$var])) {
                            $value = $values[$var];
                        }

                        $less .= "@{$var}: {$value};\n";
                    }
                }

                file_put_contents($less_file, $less);

                if ($theme_suffix_file) {
                    $less_file = $this->path . 'assets' . PHPFOX_DS . 'variables.less';
                    file_put_contents($less_file, $less);
                }

                $lessc = new \lessc();
                $lessc->addImportDir($this->path . 'assets/');

                try {
                    $lessc->compileFile($less_input, $css_output);
                } catch (\Exception $e) {
                    if (PHPFOX_DEBUG) {
                        \Phpfox_Error::trigger($e->getMessage(), E_USER_ERROR);
                    }
                }


                break;
        }

        return true;
    }

    public function html($exist = false)
    {
        return file_get_contents($this->html_path($exist));
    }

    public function html_path($exist = false)
    {
        $file = $this->path . 'html/layout.html';
        if (!file_exists($file)) {
            if ($exist) {
                $o = $file;
            }

            $file = str_replace($this->id . '/html/', 'bootstrap/html/', $file);

            if ($exist) {
                if (!is_dir($this->path . 'html/')) {
                    mkdir($this->path . 'html/');
                }
                copy($file, $o);
                $file = $o;
            }
        }

        return $file;
    }

    public function css()
    {
        $file = $this->path . 'assets/autoload.less';
        if (!file_exists($file)) {
            $file = $this->path . 'assets/autoload.css';
        }

        return file_get_contents($file);
    }

    public function json()
    {
        $file = $this->path . 'theme.json';

        return file_get_contents($file);
    }

    public function has_js()
    {
        return (file_exists($this->path . 'assets/autoload.js') ? true : false);
    }

    public function js()
    {
        $file = $this->path . 'assets/autoload.js';

        return file_get_contents($file);
    }

    public function logo_url($type = 'logos')
    {
        $logo= get_from_cache('flavor/' . $type . '/' . $this->id, function() use($type){
            $logo = $this->logo($type);
            if (!$logo) {
                return '';
            }

            $logo = str_replace(PHPFOX_DIR_SITE, setting('core.path_actual') . 'PF.Site/', $logo);
            //This is url, if on window, We have to fix the link
            return str_replace(PHPFOX_DS, '/', $logo);
        });

        if (request()->get('force-flavor')) {
            $logo = $logo . '?v=' . uniqid();
        }
        return $logo;

    }

    public function logo($type = 'logos')
    {
        $id = 'flavor/' . $type . '/' . $this->id;
        $dir = $this->path . 'assets' . PHPFOX_DS . $type . PHPFOX_DS;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }


        $logo = storage()->get($id);
        if (isset($logo->value)) {
            $file = $dir . $logo->value;
            if (file_exists($file)) {
                return $file;
            }
        }
        if ($type == 'logos') {
            foreach(['logo.png','logo.jpg'] as $value){
                if (file_exists($file = $dir.$value)) {
                    storage()->set($id,$value);
                    return $file;
                }
            }
            if (file_exists($file = PHPFOX_ROOT . 'PF.Base/theme/frontend/default/style/default/image/layout/phpfox_bootstraptemplate_logo.png')) {
                if (@copy($file, $dir . ($value = 'logo_default.png'))) {
                    storage()->set($id,$value);
                    return $dir.$value;
                }
            }
        }
        return null;
    }

    public function default_photo($type = null, $bUrl = false)
    {
        if (!$type) {
            return false;
        }
        $id = 'flavor/defaults/' . $this->id;
        $dir = $this->path . 'assets' . PHPFOX_DS . 'defaults' . PHPFOX_DS;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $photos = storage()->get($id);
        $return = null;
        if (isset($photos->value) && is_array($defaults = json_decode(json_encode($photos->value), true)) && isset($defaults[$type])) {
            $file = $dir . $defaults[$type];
            if (file_exists($file)) {
                $return = $file;
            }
        }

        if ($bUrl && $return) {
            $return = str_replace(PHPFOX_DIR_SITE, \Phpfox::getLib('cdn')->getUrl(setting('core.path_actual')) . 'PF.Site/', $return);
            $return = str_replace(PHPFOX_DS, '/', $return);
            if (request()->get('force-flavor')) {
                $return = $return . '?v=' . uniqid();
            }
        }
        return $return;
    }

    public function favicon()
    {
        return $this->logo('favicons');
    }

    public function favicon_url()
    {
        return $this->logo_url('favicons');
    }

    public function banners()
    {
        $banners = [];
        $dir = $this->path . 'assets' . PHPFOX_DS . 'banners' . PHPFOX_DS;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        foreach (scandir($dir) as $file) {
            if (preg_match('/^(.*)\.([jpg|png|gif|jpeg]+)$/i', $file)) {
                $banners[] = str_replace(PHPFOX_DS, '/', str_replace(PHPFOX_DIR_SITE, home() . 'PF.Site/', $dir . $file));
            }
        }

        return $banners;
    }

    public function content()
    {
        return get_from_cache('flavor/content/' . $this->id, function(){
            $content = storage()->get('flavor/content/' . $this->id);
            if (isset($content->value)) {
                return $content->value;
            }
            return '';
        });
    }

    public function export()
    {
        $dir = $this->path;

        // build checksum
        $fileHelper = new FileHelper();
        $fileHelper->createChecksum($dir, [$dir], ['phrase.json', 'theme.json', 'flavor/bootstrap.css', 'flavor/root.less']);

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        $zip_file = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'theme-' . $this->id . '.zip';
        $Zip = new \ZipArchive();
        $Zip->open($zip_file, \ZipArchive::CREATE);

        $paths = [];
        foreach ($iter as $path => $dir) {
            if ($dir instanceof \SplFileInfo) {
                if ($dir->isFile() && strpos($path, $this->path . 'flavor') === false && strpos($path, '.git') === false && basename($dir) != '.DS_Store') {
                    if (preg_match('/^(.*)\.(json)$/i', $path)) {
                        $content = file_get_contents($path);
                        $paths[str_replace($this->path, '', $path)] = $content;
                    } else {
                        $paths['files_path'][] = str_replace($this->path, '', $path);
                        $Zip->addFile($dir->getPathName(), str_replace($this->path, '', $path));
                    }
                } elseif ($dir->isDir() && strpos($path, $this->path . 'flavor') === false && strpos($path, '.git') === false) {
                    $Zip->addEmptyDir(str_replace($this->path, '', $path));
                }
            }
        }
        $paths = json_encode($paths, JSON_PRETTY_PRINT);

        $json_file = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'theme-' . $this->id . '.json';

        file_put_contents($json_file, $paths);

        $Zip->addFile($json_file, 'theme-' . $this->id . '.json');
        $Zip->close();

        unlink($json_file);

        \Phpfox_File::instance()->forceDownload($zip_file, 'phpfox-theme-' . $this->id . '.zip');
    }

    public function revert()
    {
        $dir = $this->path;
        $bootstrap = json_decode(file_get_contents(PHPFOX_DIR_SITE . '/Apps/core-flavors/flavors/bootstrap.json'));
        foreach ($bootstrap as $file => $content) {
            if (preg_match('/^(.*)\.(gif|jpg|jpeg|png)$/i', $file)) {
                $content = base64_decode($content);
            }

            file_put_contents($dir . ltrim($file, '/'), $content);
        }

        $json = json_decode(file_get_contents($dir . 'theme.json'));
        $json->id = strtolower($this->id);
        $json->name = $this->name;
        file_put_contents($dir . 'theme.json', json_encode($json, JSON_PRETTY_PRINT));

        return true;
    }

    public function delete()
    {
        $dirs = [];
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($iter as $path => $dir) {
            if ($dir instanceof \SplFileInfo && $dir->isDir()) {
                $dirs[] = $path;
            }
        }

        $files = \Phpfox_File::instance()->getAllFiles($this->path, true);
        foreach ($files as $file) {
            unlink($file);
        }

        foreach ($dirs as $dir) {
            rmdir($dir);
        }

        rmdir($this->path);

        $id = 'flavor' . PHPFOX_DS . 'defaults' . PHPFOX_DS . $this->id;
        storage()->del($id);

        return true;
    }

    public function has_less()
    {
        return (file_exists($this->path . 'assets/variables.less') ? true : false);
    }

    public function design()
    {
        $theme_suffix = !empty($_REQUEST['theme_suffix']) ? $_REQUEST['theme_suffix'] : null;
        $less_input = $this->path . 'assets' . PHPFOX_DS . 'variables.less';
        if ($theme_suffix) {
            $temp = $this->path . 'assets' . PHPFOX_DS . 'variables' . $theme_suffix . '.less';
            if (file_exists($temp)) {
                $less_input = $temp;
            }
        }

        $get_vars = function ($less_input) {
            $variables = [];

            $lines = file($less_input);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                if (substr($line, 0, 1) == '@') {
                    $parts = array_map('trim', explode(':', $line));
                    $var = str_replace('@', '', $parts[0]);
                    if (!isset($parts[1])) {
                        continue;
                    }
                    $value = trim(explode(';', $parts[1])[0]);
                    $variables[$var] = $value;
                }
            }

            return $variables;
        };

        $variables = array_merge($get_vars(PHPFOX_DIR_SITE . 'flavors/bootstrap/assets/variables.less'), $get_vars($less_input));


        if (is_string($this->vars) && substr($this->vars, 0, 1) == '@') {
            $bootstrap = json_decode(file_get_contents(PHPFOX_DIR_SITE . 'flavors/bootstrap/theme.json'));

            $this->vars = $bootstrap->vars;
        }

        $html = '';
        if (isset($this->vars) && count((array)$this->vars)) {
            $html .= '<form class="ajax_post" method="post" action="' . url()->make('/flavors/manage', ['id' => $this->id, 'type' => 'design', 'theme_suffix' => request()->get('theme_suffix')])
                . '">';

            foreach ($this->vars as $key => $value) {
                $html .= '<div class="fm_setting">';
                $html .= '<div class="fm_title">' . $value->title . '</div>';

                if (!isset($value->type)) {
                    $value->type = 'text';
                }

                if (in_array($value->attr, ['max-width', 'width', 'min-width'])) {
                    $value->type = 'size';
                }

                $rules = json_encode(['rule' => $value->id . '{' . $value->attr . ':[VALUE];}']);
                if (isset($variables[$key])) {
                    $value->value = $variables[$key];
                }

                if (!isset($value->value)) {
                    $value->value = '';
                }

                switch ($value->type) {
                    case "theme":
                        $html .= '<div autocomplete="off" type="text" name="var[' . $key . ']" value="' . $value->value . '" data-rules=\'' . $rules . '\' >';
                        foreach ($value->options as $option) {
                            $html .= strtr('<div><a class="edit_for_theme" href="#" data-url=":url" value=":key" label=":label" :selected>:label</a></div>', [
                                ':key'      => $option->value,
                                ':label'    => $option->label,
                                ':selected' => ($value->value == $option->value) ? 'selected' : ' ',
                                ':url'      => url('/flavors/manage', ['id' => $this->id, 'type' => 'design', 'theme_suffix' => $option->value]),
                            ]);
                        }
                        $html .= '</div>';
                        break;
                    case 'size':
                        $html .= '<input autocomplete="off" type="text" name="var[' . $key . ']" value="' . $value->value . '" data-rules=\'' . $rules . '\'>';
                        break;
                    default:
                        $html .= '<input class="_colorpicker" data-old="' . $value->value . '" autocomplete="off" type="text" name="var[' . $key . ']" value="' . $value->value . '" data-rules=\''
                            . $rules . '\'>';
                        $html .= '<div class="_colorpicker_holder"></div>';
                        break;
                }

                $html .= '</div>';
            }
            $html .= '<div class="fm_submit"><span>Publish</span></div>';
            $html .= '</form>';
        }

        return $html;
    }
}

class Flavor
{
    /**
     * @var Object
     */
    public $active;

    private static $_active = null;

    public function __construct()
    {
        if (self::$_active === null) {
            if (!is_dir(PHPFOX_DIR_SITE . 'flavors/')) {
                mkdir(PHPFOX_DIR_SITE . 'flavors/');
            }
            $default_dir = PHPFOX_DIR_SITE . 'flavors/bootstrap/';
            if (!is_dir($default_dir) and is_writable(PHPFOX_DIR_SITE . 'flavors')) {
                $this->make([
                    'name' => 'bootstrap',
                ]);
            }

            $flavor = 'bootstrap';
            $default = storage()->get('flavor/default');
            if (isset($default->value)) {
                $flavor = $default->value;
            }

            $cookie = \Phpfox::getCookie('flavors_id');
            if ($cookie) {
                $flavor = $cookie;
            }

            self::$_active = $this->get($flavor);
            if (self::$_active === false) {
                self::$_active = $this->get('bootstrap');
            }
        }

        $this->active = self::$_active;
    }

    public function set_active($flavor)
    {
        self::$_active = $this->get($flavor);
        $this->active = self::$_active;
    }

    public function make($val, $file = null, $force_upgrade = false)
    {

        if ($file !== null) {
            $path = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . uniqid() . '/';
            $zip_file = $path . 'theme.zip';
            $json = null;

            mkdir($path);
            if (isset($file['is_local'])) {
                copy($file['tmp_name'], $zip_file);
            } else {
                move_uploaded_file($file['tmp_name'], $zip_file);
            }

            $zip = new \ZipArchive();
            $zip->open($zip_file);
            $zip->extractTo($path);
            $zip->close();

            foreach (scandir($path) as $file) {
                if (substr($file, -5) == '.json') {
                    $json = json_decode(file_get_contents($path . $file), true);
                    break;
                }
            }

            if ($json === null) {
                error('JSON file missing for this theme.');
            }

            $j = json_decode($json['theme.json']);

            $val['name'] = $j->id;
        }

        if (empty($val['name'])) {
            error(_p('Provide a name for your theme.'));
        }

        $name = $val['name'];
        $val['name'] = strtolower($val['name']);
        if (!preg_match('/^[^\W_]+$/', $name)) {
            error(_p('Alphanumeric characters only for the theme name.'));
        }

        $dir = PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . strtolower($name) . PHPFOX_DS;
        if ($force_upgrade === true && is_dir($dir)) {
            \Phpfox_File::instance()->delete_directory($dir);
        }

        if (is_dir($dir)) {
            \Phpfox::getLib('file')->removeDirectory($dir);
        }

        if (is_dir($dir)) {
            error(_p('Theme already exists.'));
        }

        mkdir($dir);
        mkdir($dir . 'assets/');
        mkdir($dir . 'html/');
        mkdir($dir . 'flavor/');

        if (isset($val['clone'])) {

            if ($val['clone'] == '__blank') {

                file_put_contents($dir . 'assets/autoload.css', '');

                $bootstrap = json_decode(file_get_contents(PHPFOX_DIR_SITE . '/Apps/core-flavors/flavors/bootstrap.json'), true);
                file_put_contents($dir . 'html/layout.html', $bootstrap['/html/layout.html']);
                file_put_contents($dir . 'theme.json', json_encode(['id' => '', 'name' => ''], JSON_PRETTY_PRINT));

            } else {
                $files = [];
                $object = $this->get($val['clone']);
                $dirs = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($object->path, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD
                );
                foreach ($dirs as $file_name => $o) {
                    if ($o instanceof \SplFileInfo) {
                        if ($o->isDir()) {
                            $file_name = str_replace('flavors/' . $val['clone'] . '/', 'flavors/' . $val['name'] . '/', $file_name);
                            \Phpfox_File::instance()->mkdir($file_name, true);
                        } else {
                            $new_path = str_replace('flavors/' . $val['clone'] . '/', 'flavors/' . $val['name'] . '/', $file_name);
                            $files[$file_name] = $new_path;
                        }
                    }
                }

                foreach ($files as $copy => $file) {
                    copy($copy, $file);
                }
            }

        } else {
            if (isset($json)) {
                $dirs = [];
                foreach ($json as $file => $content) {
                    $parts = explode('/', $file);
                    unset($parts[count($parts) - 1]);
                    $this_dir = implode('/', $parts);
                    if (empty($this_dir)) {
                        continue;
                    }
                    $dirs[$this_dir . '/'] = $this_dir . '/';
                }

                foreach ($dirs as $new_dir) {
                    $this_dir = $dir . $new_dir;
                    if (!is_dir($this_dir)) {
                        \Phpfox_File::instance()->mkdir($this_dir, true);
                    }
                }

                foreach ($json as $file => $content) {
                    if ($file == 'files_path') {
                        foreach ($content as $sPath) {
                            $to_filename = $dir . $sPath;

                            if (!is_dir($sDir = dirname($to_filename))) {
                                mkdir($sDir, 0777, 1);
                                chmod($sDir, 0777);
                            }
                            if (!@copy($path . $sPath, $to_filename)) {
                                throw new \RuntimeException(sprintf('Can not copy from "%s" to "%s"', $path . $sPath, $to_filename));
                            }
                        }
                    } else {
                        if (preg_match('/^(.*)\.(gif|jpg|jpeg|png)$/i', $file)) {
                            $content = base64_decode($content);
                        }

                        file_put_contents($dir . $file, $content);
                    }
                }

                // check and import phrase
                if (isset($json['phrase.json'])) {
                    $phrases = json_decode($json['phrase.json'], true);
                    (new Phrase())->addPhrase($phrases);
                }
            } else {
                $bootstrap = json_decode(file_get_contents(PHPFOX_DIR_SITE . '/Apps/core-flavors/flavors/bootstrap.json'));
                foreach ($bootstrap as $file => $content) {
                    if (preg_match('/^(.*)\.(gif|jpg|jpeg|png)$/i', $file)) {
                        $content = base64_decode($content);
                    }

                    file_put_contents($dir . ltrim($file, '/'), $content);
                }
            }
        }

        $json = json_decode(file_get_contents($dir . 'theme.json'));
        $json->id = strtolower($name);
        $json->name = $name;
        file_put_contents($dir . 'theme.json', json_encode($json, JSON_PRETTY_PRINT));

        //build default photos
        flavor()->build_default_photos(strtolower($name));

        return $this->get(strtolower($name));
    }

    public function get($flavor)
    {
        $dir = PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS;
        $path = $dir . $flavor . PHPFOX_DS;

        if (file_exists($path . 'theme.json')) {
            return new Object($path);
        }

        return false;
    }

    /**
     * @return array
     */
    public function all()
    {
        $dir = PHPFOX_DIR_SITE . 'flavors';
        if (!is_dir($dir)) {
            error('Flavor folder does not exist.');
        }

        $flavors = [];
        foreach (scandir($dir) as $flavor) {
            if (($flavor = $this->get($flavor))) {
                $flavors[] = $flavor;
            }
        }

        return $flavors;
    }

    public function rebuild_bootstrap($less = false)
    {
        // unlimited time & memory when rebuild bootstrap core.
        if (function_exists('ini_set')) {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 3000);
        }
        if (function_exists('set_time_limit')) {
            set_time_limit(0);
        }

        $theme = new \Core\Theme('bootstrap');
        $theme->get()->delete();

        $theme = new \Core\Theme();
        $new_theme = $theme->make(['name' => 'Bootstrap'], null, false, 'bootstrap');
        db()->update(':theme', ['is_default' => 1], ['theme_id' => $new_theme->theme_id]);
        if ($less === true) {
            $theme = new \Core\Theme();
            $theme->get()->rebuild();

            if (defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE) {
                @copy(PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS . 'flavor' . PHPFOX_DS . 'bootstrap.css',
                    PHPFOX_DIR . 'theme' . PHPFOX_DS . 'bootstrap' . PHPFOX_DS . 'flavor' . PHPFOX_DS . 'default.css');
            }
        }
    }

    public function rebuild_material($less = false)
    {
        // unlimited time & memory when rebuild bootstrap core.
        if (function_exists('ini_set')) {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 3000);
        }
        if (function_exists('set_time_limit')) {
            set_time_limit(0);
        }

        $theme = new \Core\Theme();
        $new_theme = $theme->make(['name' => 'Bootstrap'], null, false, 'bootstrap');
        db()->update(':theme', ['is_default' => 1], ['theme_id' => $new_theme->theme_id]);
        if ($less === true) {
            $theme = new \Core\Theme();
            $theme->get()->rebuild();

            if (defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE) {
                @copy(PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS . 'flavor' . PHPFOX_DS . 'bootstrap.css',
                    PHPFOX_DIR . 'theme' . PHPFOX_DS . 'bootstrap' . PHPFOX_DS . 'flavor' . PHPFOX_DS . 'default.css');
            }
        }
    }

    public function build_default_photos($name = null)
    {
        if ($name === null) {
            $name = $this->active->id;
        }
        $path = PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . $name . PHPFOX_DS . 'assets' . PHPFOX_DS . 'defaults' . PHPFOX_DS;
        if (!is_dir($path)) {
            return true;
        }
        $id = 'flavor' . PHPFOX_DS . 'defaults' . PHPFOX_DS . $name;
        $data = [];

        foreach (scandir($path) as $file) {
            $ext = \Phpfox_File::instance()->getFileExt($file);
            if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                continue;
            }
            $type = pathinfo($file)['filename'];
            $data[$type] = $file;
        }

        storage()->del($id);
        storage()->set($id, $data);
    }
}
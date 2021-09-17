<?php

namespace Core\Theme;

//Define flag for supporting debug css

if (!defined('PHPFOX_CSS_ALWAYS_BUILD')) {
    define('PHPFOX_CSS_ALWAYS_BUILD', false);
}


class Object extends \Core\Objectify
{
    public $theme_id;
    public $name;
    public $folder;
    public $flavor_id;
    public $flavor_folder;
    public $is_active;
    public $is_default;
    public $created;
    public $image;
    public $internal_id;
    public $version;

    protected $website;

    /**
     * @var \Core\Db
     */
    private $_db;

    public function __construct($keys)
    {
        parent::__construct($keys);
        if (isset($this->website) && substr($this->website, 0, 1) == '{') {
            foreach (json_decode($this->website) as $key => $value) {
                if ($key == 'id') {
                    $key = 'internal_id';
                }
                $this->$key = $value;
            }
            unset($this->website);
        }

        $currentImage = $this->image;
        if (empty($currentImage) && file_exists(PHPFOX_DIR . 'static' . PHPFOX_DS . 'image' . PHPFOX_DS . 'theme' . PHPFOX_DS . strtolower($this->name) . '.png')) {
            $currentImage = \Phpfox_Url::instance()->makeUrl('') . 'static/image/theme/' . strtolower($this->name) . '.png';
            $currentImage = str_replace('index.php', 'PF.Base', $currentImage);
        }
        $this->_db = new \Core\Db();
        $this->image = new \Core\Objectify(function () use ($currentImage) {
            $html = '';
            if ($currentImage) {
                $html = 'class="image_load" data-src="' . $currentImage . '"';
            } else {
                $hex = function ($color) {
                    $color = trim($color);
                    $color = preg_replace('/(lighten|darken)\(\#(.*), (.*)\)/i', '#\\2', $color);

                    return '<span style="background:' . $color . ';"></span>';
                };

                $flavor = (new \Core\Theme\Flavor($this))->getDefault();
                $path = $this->getFlavorPath() . $flavor->folder . '.less';
                if (file_exists($path)) {
                    $colors = [];
                    $lines = file($path);
                    foreach ($lines as $line) {
                        if (preg_match('/@brandPrimary\:(.*?);/s', $line, $matches)) {
                            $colors[] = $hex($matches[1]);
                        } else {
                            if (preg_match('/@bodyBg\:(.*?);/s', $line, $matches)) {
                                $colors[] = $hex($matches[1]);
                            } else {
                                if (preg_match('/@blockBg\:(.*?);/s', $line, $matches)) {
                                    $colors[] = $hex($matches[1]);
                                } else {
                                    if (preg_match('/@headerBg\:(.*?);/s', $line, $matches)) {
                                        $colors[] = $hex($matches[1]);
                                    }
                                }
                            }
                        }
                    }

                    if ($colors) {
                        $colors = implode('', $colors);
                        $html = '><div class="theme_colors">' . $colors . '<' . "/div";
                    }
                }
            }

            return $html;
        });
    }

    public function basePath()
    {
        return PHPFOX_DIR . 'theme/default/';
    }

    public function getPath()
    {
        return PHPFOX_DIR_SITE . 'flavors/' . $this->folder . '/';
    }

    public function getFlavorPath()
    {
        $sNewPath = PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS . 'flavor' . PHPFOX_DS;
        if (is_dir($sNewPath)) {
            return $sNewPath;
        }
        return PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . $this->folder . PHPFOX_DS . 'flavor' . PHPFOX_DS;
    }

    public function delete()
    {
        if (is_dir($this->getFlavorPath())) {
            \Phpfox_File::instance()->delete_directory($this->getFlavorPath());
        }

        foreach ($this->flavors() as $Flavor) {
            $Flavor->delete();
        }
        $this->_db->delete(':theme', ['theme_id' => $this->theme_id]);
        return null;
    }

    /**
     * @return bool
     * @deprecated
     */
    public function export()
    {
        return false;
    }

    public function setDefault()
    {
        $this->_db->update(':theme', ['is_default' => 0], ['is_default' => 1]);
        $this->_db->update(':theme_style', ['is_default' => 0], ['is_default' => 1]);

        $this->_db->update(':theme', ['is_default' => 1], ['theme_id' => $this->theme_id]);
        $this->_db->update(':theme_style', ['is_default' => 1], ['style_id' => $this->flavor_id]);

        return true;
    }

    public function deleteFlavor($id)
    {
        \Phpfox_Database::instance()->delete(':theme_style', ['style_id' => (int)$id]);

        return true;
    }

    public function setFlavor($id)
    {
        $flavor = \Phpfox_Database::instance()
            ->select('*')
            ->from(':theme_style')
            ->where(['style_id' => $id])
            ->get();

        if (!isset($flavor['style_id'])) {
            return false;
        }

        $this->flavor_id = $flavor['style_id'];
        $this->flavor_folder = $flavor['folder'];
        return null;
    }

    /**
     * @return Flavor\Object[]
     */
    public function flavors()
    {
        $rows = \Phpfox_Database::instance()
            ->select('*')
            ->from(':theme_style')
            ->where(['theme_id' => $this->theme_id])
            ->order('name ASC')
            ->all();

        $flavors = [];
        foreach ($rows as $row) {
            $row['is_selected'] = ($this->flavor_folder == $row['folder'] ? true : false);

            $flavors[] = new Flavor\Object($this, $row);
        }

        return $flavors;
    }

    public function rebuild_bootstrap()
    {
        if (is_dir(PHPFOX_DIR_SITE . 'flavors/bootstrap/')) {
            $theme = new \Core\Theme('bootstrap');
            $theme->get()->delete();
        }

        $theme = new \Core\Theme();
        $new_theme = $theme->make(['name' => 'Bootstrap'], null, false, 'bootstrap');
        db()->update(':theme', ['is_default' => 1], ['theme_id' => $new_theme->theme_id]);
    }

    public function rebuild()
    {
        $flavorId = $this->flavor_folder;
        if (!$flavorId) {
            throw new \Exception(_p('Cannot merge a theme without a flavor.'));
        }

        $db = new \Core\Db();
        $moduleList = $db->select('module_id')
            ->singleData('module_id')
            ->from(':module')
            ->where('is_active=1')
            ->all();

        $css = new CSS($this);

        //Cache content from module and app
        $sCachedId = \Phpfox_Cache::instance()->set('css_module_app_less_content');
        if (!($sLessData = \Phpfox_Cache::instance()->get($sCachedId))) {
            $moduleData = $css->getModule($moduleList);
            $appData = $css->getApp();
            $sLessData = $moduleData . $appData;
            \Phpfox_Cache::instance()->save($sCachedId, $sLessData);
        }
        $css->set('', null, $sLessData, $this->name);

        if (is_array($moduleList)) {
            $css->reBuildModule($moduleList);
        }
        return null;
    }

    /**
     * @return bool
     * @deprecated
     */

    public function merge()
    {
        return false;
    }


    public function getCssFileName($path, $type = 'module')
    {

        //check is less file exist
        if (substr($path, -4) == '.css') {
            $lessPath = substr($path, 0, -4) . '.less';
            if ($type == 'module' && file_exists(PHPFOX_DIR . $lessPath)) {
                $path = $lessPath;
            } else {
                if ($type == 'app' && file_exists(PHPFOX_ROOT) . $path) {
                    $path = $lessPath;
                }
            }
        }

        if ('module' == $type) {
            $path = trim(str_replace('module', '', $path), PHPFOX_DS);
            $themPath = 'flavors' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS;
            $filePath = $themPath . 'flavor' . PHPFOX_DS . trim(substr(str_replace([PHPFOX_DS, '/', '\\'], ['_', '_', '_'], $path), 0, -4), '_');
            $filePath = trim($filePath, '.');
            $filePath = $filePath . '.css';
        } else {
            if ('app' == $type) {
                $path = trim(str_replace('module', '', $path), PHPFOX_DS);
                $themPath = 'flavors' . PHPFOX_DS . flavor()->active->id . PHPFOX_DS;
                $filePath = $themPath . 'flavor' . PHPFOX_DS . trim(substr(str_replace([PHPFOX_DS, '/', '\\'], ['_', '_', '_'], $path), 0, -4), '_');
                $filePath = trim($filePath, '.');
                $filePath = $filePath . '.css';
            }
        }

        $checkFilePath = PHPFOX_DIR_SITE . $filePath;

        if (PHPFOX_CSS_ALWAYS_BUILD || !file_exists($checkFilePath)) {
            try {
                (new CSS($this))->buildFile($path, $type);
            } catch (\Exception $e) {
                if (PHPFOX_DEBUG) {
                    throw $e;
                }
            }
        }

        $sReturn = 'PF.Site' . PHPFOX_DS . $filePath;
        return $sReturn;
    }

    public function __toArray()
    {

    }
}
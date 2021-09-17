<?php

namespace Core\View;

class Loader extends \Twig_Loader_Filesystem
{
    public $layout = null;

    public function getSource($name)
    {
        if ($name == '@Theme/macro/form.html' && request()->segment(1) == 'admincp') {
            $file = PHPFOX_DIR . 'theme/default/html/macro/form.html';

            return file_get_contents($file);
        }

        if ($name == '@Theme/layout.html') {
            \Core\Event::trigger('Core\View\Loader::getSource', $this);
            if ($this->layout !== null) {
                return $this->layout;
            }

            $Theme = \Phpfox_Template::instance()->theme()->get();
            $Service = new \Core\Theme\Service($Theme);

            return $Service->html()->get();
        } else {
            if (substr($name, 0, 7) == '@Theme/') {
                $Theme = \Phpfox_Template::instance()->theme()->get();
                $name = str_replace('@Theme/', '', $name);
                $file = $Theme->getPath() . 'html/' . $name;

                if (!file_exists($file)) {
                    $file = PHPFOX_DIR . 'theme/default/html/' . $name;
                }

                $html = file_get_contents($file);

                return $html;
            }
        }

        return parent::getSource($name);
    }
}
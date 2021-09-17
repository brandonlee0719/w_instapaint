<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class TagController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('photo.can_view_photos', true);
        if (!defined('PHPFOX_GET_FORCE_REQ')) {
            define('PHPFOX_GET_FORCE_REQ', true);
        }

        if ($sTag = $this->request()->get('req3')) {
            return \Phpfox_Module::instance()->setController('photo.index');
        }

        $this->template()->setTitle(_p('photo_tags'))
            ->setBreadCrumb(_p('photo'), $this->url()->makeUrl('photo'))
            ->setBreadCrumb(_p('tags'), $this->url()->makeUrl('photo.tag'), true);

        $this->setParam('iTagDisplayLimit', 75);
        $this->setParam('bNoTagBlock', true);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_tag_clean')) ? eval($sPlugin) : false);
    }
}
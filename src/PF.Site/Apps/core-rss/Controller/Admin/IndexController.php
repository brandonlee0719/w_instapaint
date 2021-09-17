<?php

namespace Apps\Core_RSS\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Component;

class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($iDeleteId = $this->request()->get('delete'))) {
            if (Phpfox::getService('rss.process')->delete($iDeleteId)) {
                $this->url()->send('admincp.rss', null, _p('feed_successfully_deleted'));
            }
        }

        $this->template()->setTitle(_p('manage_feeds'))
            ->setBreadCrumb(_p('manage_feeds'), $this->url()->makeUrl('admincp.rss'))
            ->assign(array(
                    'aFeeds' => Phpfox::getService('rss')->get()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}

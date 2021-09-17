<?php

namespace Apps\Core_RSS\Controller;

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
        if (($iId = $this->request()->getInt('id'))) {
            if (($sContent = Phpfox::getService('rss')->getFeed($iId))) {
                ob_clean();
                header('Content-type: text/xml; charset=utf-8');
                echo $sContent;
                exit;
            }
        }

        $aFeeds = Phpfox::getService('rss')->getFeeds();

        $this->template()->setTitle(_p('rss_feeds'))
            ->setBreadCrumb(_p('rss_feeds'), $this->url()->makeUrl('rss'))
            ->assign(array(
                    'aGroupFeeds' => $aFeeds
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}

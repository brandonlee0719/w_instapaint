<?php

namespace Apps\Core_RSS\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Component;

class LogController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!($aFeed = Phpfox::getService('rss.log')->getFeed($this->request()->getInt('id')))) {
            $this->url()->send('admincp.rss', null, _p('unable_to_find_rss_log'));
        }

        $this->setParam(array(
                'rss' => array(
                    'table' => 'rss_log',
                    'field' => 'feed_id',
                    'key' => $aFeed['feed_id'],
                    'users' => true
                )
            )
        );

        $this->template()->setTitle(_p('viewing_rss_feed_log'))
            ->setBreadCrumb(_p('manage_feeds'), $this->url()->makeUrl('admincp.rss'))
            ->setBreadCrumb(_p('rss_feed_log'), null, true)
            ->assign(array(
                    'bRssIsAdminCp' => true
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_log_clean')) ? eval($sPlugin) : false);
    }
}

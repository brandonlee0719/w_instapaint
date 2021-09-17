<?php

namespace Apps\Core_RSS\Controller;

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
        Phpfox::isUser(true);

        $this->setParam(array(
                'rss' => array(
                    'table' => 'rss_log_user',
                    'field' => 'user_id',
                    'key' => Phpfox::getUserId()
                )
            )
        );

        $this->template()->setTitle(_p('rss_logs'))
            ->setBreadCrumb(_p('rss_feeds'), $this->url()->makeUrl('rss'))
            ->setBreadCrumb(_p('log'), null);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_log_clean')) ? eval($sPlugin) : false);
    }
}

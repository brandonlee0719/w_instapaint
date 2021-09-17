<?php

namespace Apps\Core_RSS\Controller;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox_Component;

class ProfileController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');

        if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'rss.can_subscribe_profile')) {
            return Phpfox_Error::display(_p('user_has_disabled_rss_feeds'));
        }

        if (($sContent = Phpfox::getService('rss')->getUserFeed($aUser))) {
            header('Content-type: text/xml; charset=utf-8');
            echo $sContent;
            exit;
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}

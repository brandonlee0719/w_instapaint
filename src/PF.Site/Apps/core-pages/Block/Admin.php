<?php

namespace Apps\Core_Pages\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Admin extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aPage = $this->getParam('aPage');
        if (empty($aPage) || !Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'pages.view_admins')) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('page_admins'),
                'aPageAdmins' => \Phpfox::getService('pages')->getPageAdmins()
            )
        );

        return 'block';
    }
}

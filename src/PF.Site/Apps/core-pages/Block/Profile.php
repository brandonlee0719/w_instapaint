<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Profile extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');

        list($iTotal, $aPages) = Phpfox::getService('pages')->getForProfile($aUser['user_id'], 10);

        if (!$iTotal) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => '<a href="' . $this->url()->makeUrl($aUser['user_name'],
                        'pages/?view=all') . '" title="' . _p('pages_you_created_and_joined') . '">' . _p('liked_pages') . '<span class="title_count">' . $iTotal . '</span></a>',
                'aPagesList' => $aPages,
                'sDefaultCoverPath' => Phpfox::getParam('pages.default_cover_photo')
            )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_profile_clean')) ? eval($sPlugin) : false);
    }
}

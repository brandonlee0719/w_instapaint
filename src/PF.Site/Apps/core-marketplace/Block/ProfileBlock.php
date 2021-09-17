<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ProfileBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');

        if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'marketplace.display_on_profile')) {
            return false;
        }

        $iProfileLimit = 5;

        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_profile_process')) ? eval($sPlugin) : false);

        $aListings = Phpfox::getService('marketplace')->getForProfileBlock($aUser['user_id'], $iProfileLimit);

        if (!count($aListings) && !defined('PHPFOX_IN_DESIGN_MODE')) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('marketplace_listings'),
                'sBlockJsId' => 'profile_marketplace',
                'aListings' => $aListings
            )
        );

        if (Phpfox::getUserId() == $aUser['user_id']) {
            $this->template()->assign('sDeleteBlock', 'profile');
        }

        if (count($aListings) >= $iProfileLimit) {
            $this->template()->assign(array(
                    'aFooter' => array(
                        _p('view_more') => $this->url()->makeUrl($aUser['user_name'], array('marketplace'))
                    )
                )
            );
        }

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_profile_clean')) ? eval($sPlugin) : false);
    }
}
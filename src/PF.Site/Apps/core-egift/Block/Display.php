<?php

namespace Apps\Core_eGifts\Block;

use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox;

class Display extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        // Check if current user level have permission to send an egift
        if (!user('pf_can_send_gift_other')) {
            return false;
        }

        $aUser = $this->getParam('aUser');
        $aCategories = Phpfox::getService('egift.category')->getCategories(false, true);

        // Check if there are categories to display
        foreach ($aCategories as $iKey => $aCategory) {
            if (empty($aCategory['time_start']) && empty($aCategory['time_end'])) {
                if (!isset($aUser['is_user_birthday']) || $aUser['is_user_birthday'] != true) {
                    unset($aCategories[$iKey]);
                }
                continue;
            }

            if (PHPFOX_TIME < $aCategory['time_start'] || PHPFOX_TIME > $aCategory['time_end']) {
                unset($aCategories[$iKey]);
            }
        }

        if (empty($aCategories)) {
            return false;
        }


        if (!defined('PHPFOX_IS_USER_PROFILE') || PHPFOX_IS_USER_PROFILE == false
            || (empty($aCategories) || (empty($aCategories) && (!isset($aUser['is_user_birthday']) || $aUser['is_user_birthday'] != true)))
            || ($aUser['user_id'] == Phpfox::getUserId())
        ) {
            // we should also check if its this user's birthday
            return false;
        }

        $this->template()->assign(array(
                'aCategories' => $aCategories,
                'aUser' => $aUser
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
        (($sPlugin = Phpfox_Plugin::get('core.component_block_activity_clean')) ? eval($sPlugin) : false);
    }
}

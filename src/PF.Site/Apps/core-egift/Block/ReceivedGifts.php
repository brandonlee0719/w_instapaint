<?php

namespace Apps\Core_eGifts\Block;

use Phpfox_Component;
use Phpfox;

class ReceivedGifts extends Phpfox_Component
{
    public function process()
    {
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        // Get current user
        $aUser = $this->getParam('aUser');
        $iLimit = $this->getParam('limit', 9);
        if ($iLimit <= 0) return false;
        $aGifts = Phpfox::getService('egift')->getReceivedGifts($aUser['user_id'], $iLimit);

        if (empty($aGifts)) {
            return false;
        }
        
        $this->template()->assign(array(
            'sHeader' => _p('Gifts'),
            'aGifts' => $aGifts
        ));

        return 'block';
    }

    /**
     * @return array
     */
    function getSettings()
    {
        return [
            [
                'info' => _p('Received Gifts Limit'),
                'description' => _p('Define the limit of how many received gifts can be displayed when viewing the user profile section. Set 0 will hide this block.'),
                'value' => 9,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }

    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'sHeader',
                'aGifts'
            )
        );
    }
}
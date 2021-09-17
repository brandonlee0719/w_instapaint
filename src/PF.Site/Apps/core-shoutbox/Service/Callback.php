<?php
namespace Apps\phpFox_Shoutbox\Service;

use Phpfox_Service;

/**
 * Class Callback
 *
 * @package Apps\phpFox_Shoutbox\Service
 */
class Callback extends Phpfox_Service
{
    /**
     * @return array
     */
    public function getPagePerms()
    {
        $aPerms = [
            'shoutbox.share_shoutbox' => _p('who_can_share_messages'),
            'shoutbox.view_shoutbox'  => _p('who_can_view_shoutbox'),
        ];
        
        return $aPerms;
    }
    
    /**
     * @return array
     */
    public function getGroupPerms()
    {
        $aPerms = [
            'shoutbox.share_shoutbox' => _p('who_can_share_messages'),
            'shoutbox.view_shoutbox'  => _p('who_can_view_shoutbox'),
        ];
    
        return $aPerms;
    }
}
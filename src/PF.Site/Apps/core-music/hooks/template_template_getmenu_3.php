<?php
/**
 * Created by PhpStorm.
 * User: phpFox
 * Date: 5/31/17
 * Time: 16:04
 */
defined('PHPFOX') or exit('NO DICE!');

if(Phpfox::isModule('music') && !Phpfox::getUserParam('music.can_access_music'))
{
    foreach ($aMenus as $key => $value) {
        if($value['module'] == 'music' && ($value['url'] = 'music' || $value['url'] = 'profile.music'))
        {
            unset($aMenus[$key]);
            break;
        }
    }
}
?>
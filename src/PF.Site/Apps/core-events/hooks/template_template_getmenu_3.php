<?php
/**
 * Created by PhpStorm.
 * User: phpFox
 * Date: 5/31/17
 * Time: 16:04
 */
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isModule('event') && !Phpfox::getUserParam('event.can_access_event')) {
    foreach ($aMenus as $key => $value) {
        if ($value['module'] == 'event' && ($value['url'] = 'event' || $value['url'] = 'profile.event')) {
            unset($aMenus[$key]);
            break;
        }
    }
}

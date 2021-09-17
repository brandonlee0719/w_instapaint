<?php
/**
 * Created by PhpStorm.
 * User: phpFox
 * Date: 5/31/17
 * Time: 16:04
 */
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isModule('marketplace') && !Phpfox::getUserParam('marketplace.can_access_marketplace')) {
    foreach ($aMenus as $key => $value) {
        if ($value['module'] == 'marketplace' && ($value['url'] = 'marketplace' || $value['url'] = 'profile.marketplace')) {
            unset($aMenus[$key]);
            break;
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: phpFox
 * Date: 5/31/17
 * Time: 16:04
 */
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isModule('forum') && !Phpfox::getUserParam('forum.can_view_forum')) {
    foreach ($aMenus as $key => $value) {
        if ($value['module'] == 'forum' && ($value['url'] = 'forum')) {
            unset($aMenus[$key]);
            break;
        }
    }
}

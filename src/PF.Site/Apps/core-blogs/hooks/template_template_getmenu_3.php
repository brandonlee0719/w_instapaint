<?php
defined('PHPFOX') or exit('NO DICE!');
if (Phpfox::isModule('blog') && !Phpfox::getUserParam('blog.view_blogs')) {
    foreach ($aMenus as $key => $value) {
        if ($value['module'] == 'blog' && ($value['url'] = 'blog' || $value['url'] = 'profile.blog')) {
            unset($aMenus[$key]);
            break;
        }
    }
}

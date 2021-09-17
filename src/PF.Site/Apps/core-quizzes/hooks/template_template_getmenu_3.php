<?php
/**
 * Created by PhpStorm.
 * User: phpFox
 * Date: 5/31/17
 * Time: 16:04
 */
defined('PHPFOX') or exit('NO DICE!');

if (Phpfox::isModule('quiz') && !Phpfox::getUserParam('quiz.can_access_quiz')) {
    foreach ($aMenus as $key => $value) {
        if ($value['module'] == 'quiz' && ($value['url'] = 'quiz' || $value['url'] = 'profile.quiz')) {
            unset($aMenus[$key]);
            break;
        }
    }
}
?>
<?php
// change location of sub_menu block

Phpfox::getLib('setting')->setParam('core.sub_menu_location', '6');

function materialParseMobileIcon($sFaIcon) {
    static $aIconParseList = [
        'pencil-square' => 'compose-alt',
        'users' => 'user-man-three-o',
        'photo' => 'photos-alt-o',
        'comments' => 'comments-o',
        'bar-chart' => 'bar-chart2',
        'puzzle-piece' => 'question-circle-o',
        'calendar' => 'calendar-check-o',
        'music' => 'music-note-o',
        'usd' => 'store-o',
        'video-camera' => 'video',
        'default' => 'box-o'
    ];

    if (is_null($sFaIcon)) {
        return 'ico ico-' . $aIconParseList['default'];
    } elseif (empty($aIconParseList[$sFaIcon])) {
        return 'fa fa-' . $sFaIcon;
    }

    return 'ico ico-' . $aIconParseList[$sFaIcon];
}

if (\Phpfox::getMessage()) {
    new \Core\Event('lib_module_page_class', function ($object) {
        $object->cssClass .= ' has-public-message';
    });
}

new \Core\Event('lib_module_page_class', function ($object) {
    $function = new Core\View\Functions('');
    if (!$function->checkContent(3) && !$function->checkContent(10) && \Phpfox_Module::instance()->getFullControllerName() == 'core.index-visitor') {
        $object->cssClass .= ' welcome-only';
    }
});

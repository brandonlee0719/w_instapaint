<?php

namespace Apps\Core_Announcement;

use Phpfox;

Phpfox::getLib('module')
    ->addAliasNames('announcement', 'Core_Announcement')
    ->addServiceNames([
        'announcement' => Service\Announcement::class,
        'announcement.callback' => Service\Callback::class,
        'announcement.process' => Service\Process::class,
    ])
    ->addTemplateDirs([
        'announcement' => PHPFOX_DIR_SITE_APPS . 'core-announcement' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'announcement.admincp.manage' => Controller\Admin\ManageController::class,
        'announcement.admincp.add' => Controller\Admin\AddController::class,
        'announcement.index' => Controller\IndexController::class,
        'announcement.view' => Controller\ViewController::class,
    ])
    ->addComponentNames('ajax', [
        'announcement.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'announcement.index' => Block\Index::class,
        'announcement.manage' => Block\Manage::class,
        'announcement.more' => Block\More::class
    ]);

group('/announcement', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('announcement.admincp.manage');
        return 'controller';
    });

    // FrontEnd routes
    route('/callback/', 'announcement.callback');
    route('/view/*', 'announcement.view');
    route('/', 'announcement.index');
});

Phpfox::getLib('setting')->setParam('announcement.icon_info', Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/core-announcement/assets/icons/announcement-ico_info.png');
Phpfox::getLib('setting')->setParam('announcement.icon_success', Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/core-announcement/assets/icons/announcement-ico_success.png');
Phpfox::getLib('setting')->setParam('announcement.icon_warning', Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/core-announcement/assets/icons/announcement-ico_warning.png');
Phpfox::getLib('setting')->setParam('announcement.icon_danger', Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/core-announcement/assets/icons/announcement-ico_danger.png');

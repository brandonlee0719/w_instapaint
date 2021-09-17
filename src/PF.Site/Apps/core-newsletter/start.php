<?php

namespace Apps\Core_Newsletter;

use Phpfox;

Phpfox::getLib('module')
    ->addAliasNames('newsletter', 'Core_Newsletter')
    ->addServiceNames([
        'newsletter' => Service\Newsletter::class,
        'newsletter.callback' => Service\Callback::class,
        'newsletter.process' => Service\Process::class,
    ])
    ->addTemplateDirs([
        'newsletter' => PHPFOX_DIR_SITE_APPS . 'core-newsletter' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'newsletter.admincp.add' => Controller\Admin\AddController::class,
        'newsletter.admincp.view' => Controller\Admin\ViewController::class,
        'newsletter.admincp.index' => Controller\Admin\IndexController::class,
    ])
    ->addComponentNames('ajax', [
        'newsletter.ajax' => Ajax\Ajax::class
    ]);

group('/newsletter', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('newsletter.admincp.index');
        return 'controller';
    });
});


define('CORE_NEWSLETTER_STATUS_DRAFT', 0);
define('CORE_NEWSLETTER_STATUS_IN_PROGRESS', 1);
define('CORE_NEWSLETTER_STATUS_COMPLETED', 2);
define('YEAR_IN_SECOND', 31557600);
define('CORE_NEWSLETTER_EXTERNAL_TYPE', 2);

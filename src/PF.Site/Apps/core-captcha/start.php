<?php

namespace Apps\Core_Captcha;

use Phpfox;

Phpfox::getLib('module')
    ->addAliasNames('captcha', 'Core_Captcha')
    ->addServiceNames([
        'captcha' => Service\Captcha::class,
        'captcha.callback' => Service\Callback::class,
        'captcha.process' => Service\Process::class,
    ])
    ->addTemplateDirs([
        'captcha' => PHPFOX_DIR_SITE_APPS . 'core-captcha' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'captcha.image' => Controller\ImageController::class
    ])
    ->addComponentNames('ajax', [
        'captcha.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'captcha.form' => Block\Form::class
    ]);

group('/captcha', function () {
    // FrontEnd routes
    route('/image/*', 'captcha.image');
});

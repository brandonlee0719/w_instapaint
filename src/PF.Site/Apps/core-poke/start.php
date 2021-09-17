<?php

namespace Apps\Core_Poke;

use Phpfox;

Phpfox::getLib('module')
    ->addAliasNames('poke', 'Core_Poke')
    ->addServiceNames([
        'poke' => Service\Poke::class,
        'poke.callback' => Service\Callback::class,
        'poke.process' => Service\Process::class,
    ])
    ->addTemplateDirs([
        'poke' => PHPFOX_DIR_SITE_APPS . 'core-poke' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('ajax', [
        'poke.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'poke.display' => Block\Display::class,
        'poke.poke' => Block\Poke::class
    ]);

define('CORE_POKE_STATUS_POKING', 1);
define('CORE_POKE_STATUS_POKED', 2);

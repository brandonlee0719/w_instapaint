<?php
namespace Apps\phpFox_Shoutbox;

use Phpfox_Module;

Phpfox_Module::instance()
    ->addServiceNames([
        'shoutbox.callback' => '\Apps\phpFox_Shoutbox\Service\Callback',
    ])
    ->addTemplateDirs([
        'shoutbox' => (new Install())->path . PHPFOX_DS . 'views',
    ])
    ->addAliasNames('shoutbox', 'phpFox_Shoutbox')
    ->addComponentNames('block', [
        'shoutbox.chat' => '\Apps\phpFox_Shoutbox\Block\Chat',
    ])
    ->addComponentNames('controller', [
        'shoutbox.polling' => Controller\PollingController::class
    ])
    ->addComponentNames('ajax', [
        'phpFox_Shoutbox.ajax' => '\Apps\phpFox_Shoutbox\Ajax\Ajax',
        'shoutbox.ajax' => '\Apps\phpFox_Shoutbox\Ajax\Ajax',
    ]);

route('/shoutbox/polling/', 'shoutbox.polling');
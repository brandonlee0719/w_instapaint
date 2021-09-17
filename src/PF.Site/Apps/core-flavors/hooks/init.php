<?php

if (request()->get('force-flavor')) {
    \Phpfox_Cache::instance()->remove();
    \Phpfox_Plugin::set();
    register_shutdown_function(function () {
        \Phpfox_Cache::instance()->remove();
    });
}
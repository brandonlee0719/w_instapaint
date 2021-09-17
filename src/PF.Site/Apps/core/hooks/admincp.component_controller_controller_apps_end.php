<?php

foreach ($allApps as $key => $app) {
    if ($app instanceof Core\App\Object) {
        if ($app->id == 'PHPfox_Core') {
            unset($allApps[$key]);
        }
    }
}
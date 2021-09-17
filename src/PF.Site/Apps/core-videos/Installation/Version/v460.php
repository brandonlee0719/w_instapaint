<?php

namespace Apps\PHPfox_Videos\Installation\Version;

class v460
{
    public function process()
    {
        // remove old video menu YPFOXAPPS-1902
        db()->delete(':menu', ['module_id' => 'core', 'url_value' => 'video']); // video 4.5.2 with module_id = core
        db()->delete(':menu', ['module_id' => 'v', 'url_value' => 'video']); // video 4.5.3 with module_id = v
    }
}

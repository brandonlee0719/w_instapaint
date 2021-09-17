<?php

namespace Apps\PHPfox_Facebook\Installation\Version;

class v453
{
    public function process()
    {
        db()->delete(':setting', 'product_id= "PHPfox_Facebook" AND var_name = "m9_facebook_require_email"');

    }
}

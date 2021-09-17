<?php

namespace Apps\Core_Announcement\Installation\Version;

class v453
{
    public function process()
    {
        db()->update(':module', ['phrase_var_name' => 'module_apps', 'is_active' => 1],
            ['module_id' => 'announcement']);
    }
}

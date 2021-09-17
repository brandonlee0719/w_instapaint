<?php

namespace Apps\Core_Poke\Installation\Version;

class v453
{
    public function process()
    {
        db()->update(':module', ['phrase_var_name' => 'module_apps'], ['module_id' => 'poke']);
    }
}

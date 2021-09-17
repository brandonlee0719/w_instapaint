<?php

namespace Apps\Core_eGifts\Installation\Version;

use Phpfox;

class v460
{
    public function process()
    {
        db()->update(':module', ['phrase_var_name' => 'module_apps'], ['module_id' => 'egift']);

        // add ordering egifts
        if (!db()->isField(':egift', 'ordering')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('egift') . "` ADD `ordering` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        db()->update(':egift_invoice', ['feed_id' => 'birthday_id'], 'feed_id IS NULL', false);
    }
}

<?php

namespace Apps\Core_Pages\Installation\Version;


class v460
{
    public function process()
    {
        // add job to re-generate missing thumbnails
        \Phpfox_Queue::instance()->addJob('pages_generate_missing_thumbnails', []);
    }
}

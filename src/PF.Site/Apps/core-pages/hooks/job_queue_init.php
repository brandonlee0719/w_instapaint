<?php
\Core\Queue\Manager::instance()->addHandler('pages_generate_missing_thumbnails',
    '\Apps\Core_Pages\Job\GenerateMissingThumbnails');

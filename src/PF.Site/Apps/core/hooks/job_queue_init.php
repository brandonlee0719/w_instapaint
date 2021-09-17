<?php
\Core\Queue\Manager::instance()->addHandler('core_get_facebook_images', '\Apps\PHPfox_Core\Job\GetFacebookImages');
\Core\Queue\Manager::instance()->addHandler('core_clone_phpfox_tag', '\Apps\PHPfox_Core\Job\ClonePhpfoxTag');
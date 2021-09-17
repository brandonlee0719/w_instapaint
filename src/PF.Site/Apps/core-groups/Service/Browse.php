<?php

namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Browse;

/**
 * Class Browse
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Browse extends Phpfox_Pages_Browse
{
    public function getFacade()
    {
        return Phpfox::getService('groups.facade');
    }
}

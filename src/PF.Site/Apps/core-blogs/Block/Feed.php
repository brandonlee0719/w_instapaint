<?php

namespace Apps\Core_Blogs\Block;

use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Feed
 * @package Apps\Core_Blogs\Block
 */
class Feed extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($iFeedId = $this->getParam('this_feed_id')) {
            $aAssign = $this->getParam('custom_param_blog_' . $iFeedId);

            if (!empty($aAssign)) {
                $this->template()->assign(
                    $this->getParam('custom_param_blog_' . $iFeedId)
                );
            }

            $this->clearParam('custom_param_blog_' . $iFeedId);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

        (($sPlugin = Phpfox_Plugin::get('blog.component_block_feed_clean')) ? eval($sPlugin) : false);
    }
}

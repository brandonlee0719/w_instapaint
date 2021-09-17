<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Forums\Block;

use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class FeedRowsBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $custom = $this->getParam('custom_param_' . $this_feed_id);
            $this->template()->assign([
                'aThread' => $custom,
            ]);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_block_feed_rows')) ? eval($sPlugin) : false);
    }
}
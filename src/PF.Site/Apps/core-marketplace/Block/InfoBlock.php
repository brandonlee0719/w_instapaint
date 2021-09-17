<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Block;

use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class InfoBlock
 * @package Apps\Core_Marketplace\Block
 */
class InfoBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_info_clean')) ? eval($sPlugin) : false);
    }
}
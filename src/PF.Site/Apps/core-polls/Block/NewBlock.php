<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Block;

use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Poll
 *
 */
class NewBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        $this->template()->assign(array(
                'aPolls' => \Phpfox::getService('poll')->getNew($iLimit)
            )
        );
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('New Polls Limit'),
                'description' => _p('Define the limit of how many polls can be displayed when viewing new poll section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"New Polls Limit" must be greater than or equal to 0')
            ]
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_new_clean')) ? eval($sPlugin) : false);
    }
}
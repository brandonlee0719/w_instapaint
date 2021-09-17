<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class InviteBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isUser() || $this->request()->get('view') == 'invites') {
            return false;
        }
        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        $aEventInvites = Phpfox::getService('event')->getInviteForUser($iLimit);

        if (!count($aEventInvites)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('invites'),
                'aEventInvites' => $aEventInvites,
                'aFooter' => [
                    _p('view_more') => $this->url()->makeUrl('event', ['view' => 'invites'])
                ],
                'bIsInviteBlock' => true,
            )
        );

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Invites Events Limit'),
                'description' => _p('Define the limit of how many invites events can be displayed when viewing the events section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
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
                'title' => _p('"Invites Events Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_invite_clean')) ? eval($sPlugin) : false);
    }
}
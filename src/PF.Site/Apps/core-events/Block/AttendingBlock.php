<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class AttendingBlock extends \Phpfox_Component
{
    private $_aRsvp = [
        'attending' => 1,
        'maybe' => 2,
        'awaiting' => 0,
    ];

    /**
     * Controller
     */
    public function process()
    {

        $aEvent = $this->getParam('aEvent');
        $iPageSize = $this->getParam('limit', 10);
        $iPage = $this->getParam('page', 1);
        $sTab = $this->getParam('tab', 'attending');
        $sContainer = $this->getParam('container');
        if (!(int)$iPageSize) {
            return false;
        }
        if (!$aEvent) {
            $aEvent = Phpfox::getService('event')->getEvent($aEvent = $this->getParam('iEventId'));
        }

        list($iCnt, $aInvites) = Phpfox::getService('event')->getInvites($aEvent['event_id'], $this->_aRsvp[$sTab],
            $iPage, $iPageSize);

        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'paging_mode' => 'pagination',
            'ajax_paging' => [
                'block' => 'event.attending',
                'params' => [
                    'tab' => $sTab,
                    'iEventId' => $aEvent['event_id']
                ],
                'container' => $sContainer
            ]
        );
        $this->template()->assign(array(
                'iCnt' => $iCnt,
                'aInvites' => $aInvites,
                'bIsPaging' => $this->getParam('ajax_paging', 0)
            )
        );
        Phpfox::getLib('pager')->set($aParamsPager);
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Attending Limit'),
                'description' => _p('Define the limit of how many attending users can be displayed when viewing the event detail. Set 0 will hide this block.'),
                'value' => 10,
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
                'title' => _p('"Attending Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_attending_clean')) ? eval($sPlugin) : false);
    }
}
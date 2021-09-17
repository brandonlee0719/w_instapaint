<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class BrowseBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iRsvp = $this->request()->get('rsvp', 1);
        $iPage = $this->request()->getInt('page');

        $iPageSize = 20;

        $aEvent = Phpfox::getService('event')->getEvent($this->request()->get('id'), true);

        list($iCnt, $aInvites) = Phpfox::getService('event')->getInvites($aEvent['event_id'], $iRsvp, $iPage,
            $iPageSize);

        Phpfox_Pager::instance()->set(array(
                'ajax' => 'event.browseList',
                'page' => $iPage,
                'size' => $iPageSize,
                'count' => $iCnt,
                'aParams' =>
                    array(
                        'id' => $aEvent['event_id'],
                        'rsvp' => $iRsvp
                    )
            )
        );

        $aLists = array(
            _p('attending') => '1',
            _p('maybe_attending') => '2',
            _p('awaiting_reply') => '0',
        );

        $this->template()->assign(array(
                'aEvent' => $aEvent,
                'aInvites' => $aInvites,
                'bIsInBrowse' => ($iPage > 0 ? true : false),
                'aLists' => $aLists
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_browse_clean')) ? eval($sPlugin) : false);
    }
}
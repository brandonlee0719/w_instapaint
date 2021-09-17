<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class InfoBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $sGoogleApiKey = Phpfox::getParam('core.google_api_key');
        if (empty($sGoogleApiKey)) {
            $sExtraParam = '';
        } else {
            $sExtraParam = "&amp;key=" . $sGoogleApiKey;
        }

        $aEvent = $this->getParam('aEventDetail');
        $this->template()->assign([
            'sExtraParam' => $sExtraParam,
            'iAttendingCnt' => Phpfox::getService('event')->getTotalRsvp($aEvent['event_id'], 1),
            'iMaybeCnt' => Phpfox::getService('event')->getTotalRsvp($aEvent['event_id'], 2),
            'iAwaitingCnt' => Phpfox::getService('event')->getTotalRsvp($aEvent['event_id'], 0),
            'iEventId' => $aEvent['event_id'],
            'aEvent' => $aEvent
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_info_clean')) ? eval($sPlugin) : false);
    }
}
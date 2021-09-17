<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ListBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iRsvp = $this->request()->get('rsvp', 1);
        $iPage = $this->request()->getInt('page', 1);
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);
        $aCallback = $this->getParam('aCallback', false);
        $iPageSize = 6;

        if (PHPFOX_IS_AJAX) {
            $aCallback = false;
            if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'getEventInvites')) {
                $aCallback = Phpfox::callback($sModule . '.getEventInvites', $iItem);
            }

            $aEvent = Phpfox::getService('event')->callback($aCallback)->getEvent($this->request()->get('id'), true);
            $this->template()->assign('aEvent', $aEvent);
        } else {
            $aEvent = $this->getParam('aEvent');
            $this->template()->assign('aEvent', $aEvent);
        }

        list($iCnt, $aInvites) = Phpfox::getService('event')->getInvites($aEvent['event_id'], $iRsvp, $iPage,
            $iPageSize);

        Phpfox_Pager::instance()->set(array(
                'page' => $iPage,
                'size' => $iPageSize,
                'count' => $iCnt,
                'paging_mode' => 'pagination',
                'ajax_paging' => [
                    'block' => 'event.list',
                    'params' => [
                        'id' => $aEvent['event_id']
                    ],
                    'container' => '.events_item_holder'
                ]
            )
        );

        $this->template()->assign(array(
                'aInvites' => $aInvites,
                'iRsvp' => $iRsvp
            )
        );

        if (!PHPFOX_IS_AJAX) {
            $sExtra = '';
            if ($aCallback !== false) {
                $sExtra .= '&amp;module=' . $aCallback['module'] . '&amp;item=' . $aCallback['item'];
            }

            $this->template()->assign(array(
                    'sHeader' => '',
                    'aMenu' => array(
                        _p('attending') => '#event.listGuests?rsvp=1&amp;id=' . $aEvent['event_id'] . $sExtra,
                        _p('maybe') => '#event.listGuests?rsvp=2&amp;id=' . $aEvent['event_id'] . $sExtra,
                        _p('can_t_make_it') => '#event.listGuests?rsvp=3&amp;id=' . $aEvent['event_id'] . $sExtra,
                        _p('not_responded') => '#event.listGuests?rsvp=0&amp;id=' . $aEvent['event_id'] . $sExtra
                    ),
                    'sBoxJsId' => 'event_guests'
                )
            );

            return 'block';
        }

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_list_clean')) ? eval($sPlugin) : false);
    }
}

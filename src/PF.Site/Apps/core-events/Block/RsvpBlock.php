<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class RsvpBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        $aCallback = false;
        if (PHPFOX_IS_AJAX) {
            $sModule = $this->request()->get('module', false);
            $iItem = $this->request()->getInt('item', false);
            if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'getEventInvites')) {
                $aCallback = Phpfox::callback($sModule . '.getEventInvites', $iItem);
            }
        }

        $aEvent = (PHPFOX_IS_AJAX ? Phpfox::getService('event')->callback($aCallback)->getEvent($this->request()->get('id'),
            true) : $this->getParam('aEvent'));

        if (PHPFOX_IS_AJAX) {
            $this->template()->assign(array(
                    'aEvent' => $aEvent,
                    'aCallback' => $aCallback
                )
            );
        } else {
            $aCallback = $this->getParam('aCallback', false);

            $this->template()->assign(array(
                    'aCallback' => $aCallback,
                    'aEvent' => $aEvent,
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
        (($sPlugin = Phpfox_Plugin::get('event.component_block_rsvp_clean')) ? eval($sPlugin) : false);
    }
}
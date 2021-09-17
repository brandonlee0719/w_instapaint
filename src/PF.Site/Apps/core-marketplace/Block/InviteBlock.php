<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class InviteBlock
 * @package Apps\Core_Marketplace\Block
 */
class InviteBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        list($iCnt, $aEventInvites) = Phpfox::getService('marketplace')->getUserInvites();

        if (!$iCnt) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('invites'),
                'aEventInvites' => $aEventInvites
            )
        );

        if ($iCnt) {
            $this->template()->assign(array(
                    'aFooter' => array(
                        _p('view_all') . ' (' . $iCnt . ')' => $this->url()->makeUrl('marketplace',
                            array('view' => 'invites'))
                    )
                )
            );
        }

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_invite_clean')) ? eval($sPlugin) : false);
    }
}
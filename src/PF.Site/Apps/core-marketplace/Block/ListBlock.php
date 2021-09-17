<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ListBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iPage = $this->request()->getInt('page');
        $iType = $this->request()->getInt('type', 1);
        $iPageSize = 6;

        if (PHPFOX_IS_AJAX) {
            $aListing = Phpfox::getService('marketplace')->getListing($this->request()->get('id'));
            $this->template()->assign('aListing', $aListing);
        } else {
            $aListing = $this->getParam('aListing');
        }

        list($iCnt, $aInvites) = Phpfox::getService('marketplace')->getInvites($aListing['listing_id'], $iType, $iPage,
            $iPageSize);

        Phpfox_Pager::instance()->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'paging_mode' => 'pagination',
            'ajax_paging' => [
                'block' => 'marketplace.list',
                'params' => [
                    'id' => $aListing['listing_id']
                ],
                'container' => '.mp_item_holder'
            ]
        ));

        $this->template()->assign(array(
                'aInvites' => $aInvites,
                'iType' => $iType
            )
        );

        if (!PHPFOX_IS_AJAX) {
            $this->template()->assign(array(
                    'sHeader' => _p('invites'),
                    'sBoxJsId' => 'marketplace_members'
                )
            );

            $this->template()->assign(array(
                    'aMenu' => array(
                        _p('visited') => '#marketplace.listInvites?type=1&amp;id=' . $aListing['listing_id'],
                        _p('not_responded') => '#marketplace.listInvites?type=0&amp;id=' . $aListing['listing_id']
                    )
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
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_list_clean')) ? eval($sPlugin) : false);
    }
}
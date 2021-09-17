<?php

defined('PHPFOX') or exit('NO DICE!');

class Friend_Component_Block_Browse_Online extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        $iLimit = Phpfox::getParam('core.items_per_page', 10);
        $iPage = $this->request()->get('page', 1);
        list($iCnt, $aRows) = Phpfox::getService('friend')->get('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId(),
            '', 0, $iLimit, true, false, true);

        $pager = Phpfox_Pager::instance();
        $pager->set(array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCnt,
            'paging_mode' => 'loadmore',
            'ajax_paging' => [
                'block' => 'friend.browse-online',
                'container' => '.browse-online-container'
            ]
        ));

        $this->template()->assign([
            'aFriends' => $aRows,
            'bIsPaging' => $this->getParam('ajax_paging', 0),
            'hasPagingNext' => $iPage < $pager->getTotalPages()
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('friend.component_block_search_small_clean')) ? eval($sPlugin) : false);
    }
}

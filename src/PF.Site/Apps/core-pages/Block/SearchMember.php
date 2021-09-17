<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class SearchMember extends \Phpfox_Component
{
    public function process()
    {
        $sTab = $this->getParam('tab', 'all');
        $iPageId = $this->getParam('page_id');
        $iPage = $this->getParam('page', 1);
        $iLimit = Phpfox::getParam('core.items_per_page', 20);
        $sSearch = $this->getParam('search');

        switch ($sTab) {
            case 'admin':
                $aMembers = Phpfox::getService('pages')->getPageAdmins($iPageId, $iPage, $iLimit, $sSearch);
                $iSize = Phpfox::getService('pages')->getPageAdminsCount($iPageId);
                break;
            case 'all':
            default:
                list($iSize, $aMembers) = Phpfox::getService('pages')->getMembers($iPageId, $iLimit, $iPage,
                    $sSearch);
                break;
        }

        // Pagination configuration
        if (!$sSearch) {
            Phpfox_Pager::instance()->set(array(
                'page' => $iPage,
                'size' => $iLimit,
                'count' => $iSize,
                'paging_mode' => 'pagination',
                'ajax_paging' => [
                    'block' => 'pages.search-member',
                    'params' => [
                        'tab' => $sTab,
                        'page_id' => $iPageId
                    ],
                    'container' => $this->getParam('container')
                ]
            ));
        }

        $this->template()->assign([
            'sTab' => $sTab,
            'aMembers' => $aMembers,
            'bIsAdmin' => Phpfox::getService('pages')->isAdmin($iPageId),
            'bIsOwner' => Phpfox::getService('pages')->getPageOwnerId($iPageId) == Phpfox::getUserId(),
            'iPageId' => $iPageId,
            'sSearch' => $sSearch
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_search_member_clean')) ? eval($sPlugin) : false);
    }
}

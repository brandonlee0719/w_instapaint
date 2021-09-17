<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Pager;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class SearchMember extends \Phpfox_Component
{
    public function process()
    {
        $sTab = $this->getParam('tab', 'all');
        $iGroupId = $this->getParam('group_id');
        $iPage = $this->getParam('page', 1);
        $iLimit = Phpfox::getParam('core.items_per_page', 20);
        $sSearch = $this->getParam('search');

        switch ($sTab) {
            case 'pending':
                $aMembers = Phpfox::getService('groups')->getPendingUsers($iGroupId, false, $iPage, $iLimit, $sSearch);
                $iSize = Phpfox::getService('groups')->getPendingUsers($iGroupId, true);
                break;
            case 'admin':
                $aMembers = Phpfox::getService('groups')->getPageAdmins($iGroupId, $iPage, $iLimit, $sSearch);
                $iSize = Phpfox::getService('groups')->getGroupAdminsCount($iGroupId);
                break;
            case 'all':
            default:
                list($iSize, $aMembers) = Phpfox::getService('groups')->getMembers($iGroupId, $iLimit, $iPage,
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
                    'block' => 'groups.search-member',
                    'params' => [
                        'tab' => $sTab,
                        'group_id' => $iGroupId
                    ],
                    'container' => $this->getParam('container')
                ]
            ));
        }

        $this->template()->assign([
            'sTab' => $sTab,
            'aMembers' => $aMembers,
            'bIsAdmin' => Phpfox::getService('groups')->isAdmin($iGroupId),
            'bIsOwner' => Phpfox::getService('groups')->getPageOwnerId($iGroupId) == Phpfox::getUserId(),
            'iGroupId' => $iGroupId,
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
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_search_member_clean')) ? eval($sPlugin) : false);
    }
}

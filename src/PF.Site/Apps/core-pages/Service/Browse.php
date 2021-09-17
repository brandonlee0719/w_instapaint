<?php

namespace Apps\Core_Pages\Service;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Browse extends \Phpfox_Pages_Browse
{
    /**
     * @return Facade
     */
    public function getFacade()
    {
        return \Phpfox::getService('pages.facade');
    }

    public function processRows(&$aRows)
    {
        foreach ($aRows as $iKey => $aPage) {
            if (!isset($aPage['vanity_url']) || empty($aPage['vanity_url'])) {
                $aRows[$iKey]['url'] = Phpfox::permalink('pages', $aPage['page_id']);
            } else {
                $aRows[$iKey]['url'] = url($aPage['vanity_url']);
            }

            // check manage/delete for each page
            $aRows[$iKey]['canApprove'] = in_array($this->request()->get('view'), ['my', 'pending']) && $aPage['view_id'] && Phpfox::getUserParam('pages.can_approve_pages');
            if (Phpfox::getUserId() == $aPage['user_id']) {
                $aRows[$iKey]['canEdit'] = true;
                $aRows[$iKey]['canDelete'] = true;
            } else {
                $aRows[$iKey]['canEdit'] = Phpfox::getUserParam('pages.can_edit_all_pages') ||
                    Phpfox::getService('pages')->isAdmin($aPage, Phpfox::getUserId());
                $aRows[$iKey]['canDelete'] = Phpfox::getUserParam('pages.can_delete_all_pages');
            }

            $aRows[$iKey]['type_name'] = Phpfox::getService('pages.type')->getTypeName($aPage['type_id']);
            $aRows[$iKey]['type_link'] = Phpfox::permalink('pages.category', $aPage['type_id'], $aPage['type_name']);
            if (!empty($aPage['category_id'])) {
                $aRows[$iKey]['category_link'] = Phpfox::permalink('pages.sub-category', $aPage['category_id'],
                    $aPage['category_name']);
            }
            list($iCnt, $aMembers) = Phpfox::getService('pages')->getMembers($aPage['page_id']);
            $aRows[$iKey]['members'] = $aMembers;
            $aRows[$iKey]['total_members'] = $iCnt;
            $aRows[$iKey]['remain_members'] = $iCnt - 3;
            $aRows[$iKey]['text_parsed'] = Phpfox::getService('pages')->getInfo($aPage['page_id'], true);
        }
    }
}

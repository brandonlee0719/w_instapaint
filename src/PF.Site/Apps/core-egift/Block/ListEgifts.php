<?php

namespace Apps\Core_eGifts\Block;

use Phpfox;
use Phpfox_Component;

class ListEgifts extends Phpfox_Component
{
    public function process()
    {
        $aCategories = Phpfox::getService('egift.category')->getCategories(false, true);
        $bIsBirthday = $this->getParam('is_user_birthday');

        if (!count($aCategories)) {
            return false;
        }

        // Check if there are categories to display
        foreach ($aCategories as $iKey => $aCategory) {
            if (empty($aCategory['time_start']) && empty($aCategory['time_end'])) {
                if (!isset($bIsBirthday) || $bIsBirthday != true) {
                    unset($aCategories[$iKey]);
                }
                continue;
            }
            if (PHPFOX_TIME < $aCategory['time_start'] || PHPFOX_TIME > $aCategory['time_end']) {
                unset($aCategories[$iKey]);
            }
        }
        // Get params
        $iPage = $this->getParam('page', 1);
        $iLimit = 6;
        $aFirstCategory = reset($aCategories);
        $iCategoryId = $this->getParam('category', $aFirstCategory['category_id']);

        // If there are nothing in this category. We'll choose another one
        if (!Phpfox::getService('egift.category')->getTotalItemBelongToCategory($iCategoryId)) {
            foreach ($aCategories as $aCategory) {
                if ($aCategory['item_count']) {
                    $iCategoryId = $aCategory['category_id'];
                }
            }
        }

        $aEgifts = Phpfox::getService('egift')->getEgifts($iCategoryId, $iPage, $iLimit, $iCount);

        // Set params for pagination
        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCount,
            'paging_mode' => 'pagination',
            //configure for ajax paging
            'ajax_paging' => [
                // block for paging content
                'block' => 'egift.list-egifts',
                // extra params
                'params' => [
                    'category' => $iCategoryId,
                    'is_user_birthday' => $bIsBirthday
                ],
                // container to replace content
                'container' => '.js_core_egift_list_items'
            ]
        );

        Phpfox::getLib('pager')->set($aParamsPager);

        $this->template()->assign(array(
                'aCategories' => $aCategories,
                'aEgifts' => $aEgifts,
                'iPage' => $iPage,
                'iCategoryId' => $iCategoryId,
                'bIsPaging' => $this->getParam('ajax_paging', 0),
                'bIsBirthday' => $bIsBirthday
            )
        );

        return 'block';
    }
}

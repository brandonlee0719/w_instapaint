<?php

namespace Apps\PHPfox_Groups\Block;

/**
 * [PHPFOX_HEADER]
 */
use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class GroupDeleteCategory extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);
        $iCategoryId = $this->request()->getInt('category_id');
        $bIsSub = $this->request()->get('is_sub', false);
        $iNumberOfChildren = Phpfox::getService('groups')->getItemsByCategory($iCategoryId, (boolean)$bIsSub,
            Phpfox::getService('groups.facade')->getItemTypeId(), 0, true);
        $iNumberOfSubCategories = $bIsSub ? 0 : Phpfox::getService('groups.type')->countSubCategories($iCategoryId);

        $this->template()->assign(array(
                'iCategoryId' => $iCategoryId,
                'aAllCategories' => Phpfox::getService('groups.category')->getCategories(),
                'iNumberOfChildren' => $iNumberOfChildren,
                'iNumberOfSubCategories' => $iNumberOfSubCategories,
                'bIsSub' => $bIsSub
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_delete_category_clean')) ? eval($sPlugin) : false);
    }
}

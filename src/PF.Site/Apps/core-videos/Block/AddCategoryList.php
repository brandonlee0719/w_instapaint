<?php

namespace Apps\PHPfox_Videos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class AddCategoryList extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aItems = Phpfox::getService('v.category')->getForUsers(0, 1, 1, 0);
        $selected = $this->getParam('aSelectedCategories');
        if ($selected) {
            $aChecked = [];
            foreach ($selected as $select) {
                $aChecked[] = $select['category_id'];
            }
            foreach ($aItems as $iKey => $aItem) {
                if (in_array($aItem['category_id'], $aChecked)) {
                    $aItems[$iKey]['active'] = true;
                }
                foreach ($aItem['sub'] as $iSubKey => $aSub) {
                    if (in_array($aSub['category_id'], $aChecked)) {
                        $aItems[$iKey]['sub'][$iSubKey]['active'] = true;
                    }
                }
            }
        }
        $this->template()->assign(array(
            'aItems' => $aItems
        ));

        (($sPlugin = Phpfox_Plugin::get('video.component_block_add_category_list_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_block_add_category_list_clean')) ? eval($sPlugin) : false);
    }
}

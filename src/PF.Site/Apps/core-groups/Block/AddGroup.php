<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddGroup extends \Phpfox_Component
{
    public function process()
    {
        // get main category
        $iTypeId = $this->request()->get('type_id');
        $aMainCategory = Phpfox::getService('groups.type')->getById($iTypeId);

        if (!$aMainCategory) {
            return false;
        }

        $this->template()->assign([
            'aMainCategory' => $aMainCategory,
            'iTypeId' => $iTypeId,
            'aCategories' => Phpfox::getService('groups.type')->get()
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_add_group_clean')) ? eval($sPlugin) : false);
    }
}

<?php

namespace Apps\Core_Pages\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class AddPage extends \Phpfox_Component
{
    public function process()
    {
        // get main category
        $iTypeId = $this->request()->get('type_id');
        $aMainCategory = Phpfox::getService('pages.type')->getById($iTypeId);

        if (!$aMainCategory) {
            return false;
        }

        $this->template()->assign([
            'aMainCategory' => $aMainCategory,
            'iTypeId' => $iTypeId,
            'aCategories' => Phpfox::getService('pages.type')->get()
        ]);

        return 'block';
    }
}

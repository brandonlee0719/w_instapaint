<?php

namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class CategoryBlock
 * @package Apps\Core_Marketplace\Block
 */
class CategoryBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iCurrentCategory = $this->getParam('sCurrentCategory', null);
        $iParentCategoryId = $this->getParam('iParentCategoryId', 0);

        $aCategories = Phpfox::getService('marketplace.category')->getForBrowse();

        if (!is_array($aCategories)) {
            return false;
        }

        if (!count($aCategories)) {
            return false;
        }

        $this->template()->assign([
            'sHeader' => _p('categories'),
            'aCategories' => $aCategories,
            'iCurrentCategory' => $iCurrentCategory,
            'iParentCategoryId' => $iParentCategoryId,
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_category_clean')) ? eval($sPlugin) : false);
    }
}
<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class GroupCategory
 * @package Apps\PHPfox_Groups\Block
 */
class GroupCategory extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iCacheTime = $this->getParam('cache_time', Phpfox::getParam('core.cache_time_default', 5));
        $iCurrentCategory = $this->getParam('sCurrentCategory', null);
        $iParentCategoryId = $this->getParam('iParentCategoryId', 0);

        $aCategories = Phpfox::getService('groups.type')->get($iCacheTime);


        if (!is_array($aCategories) || !count($aCategories)) {
            return false;
        }

        $this->template()->assign([
            'sHeader' => _p('categories'),
            'iCurrentCategory' => $iCurrentCategory,
            'iParentCategoryId' => $iParentCategoryId,
            'aCategories' => $aCategories
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_category_clean')) ? eval($sPlugin) : false);
    }
}

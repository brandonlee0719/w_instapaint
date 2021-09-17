<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Category
 * @package Apps\Core_Pages\Block
 */
class Category extends \Phpfox_Component
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

        $aCategories = Phpfox::getService('pages.type')->get($iCacheTime);

        if (!is_array($aCategories) || !count($aCategories)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('categories'),
                'iCurrentCategory' => $iCurrentCategory,
                'iParentCategoryId' => $iParentCategoryId,
                'aCategories' => $aCategories
            )
        );

        return 'block';
    }

    /**
     * Get settings of this block
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('block_category_cache_time_info'),
                'description' => _p('block_category_cache_time_description'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time'
            ]
        ];
    }

    /**
     * Validation
     *
     * @return array
     */
    public function getValidation()
    {
        return [
            'cache_time' => [
                'def' => 'int:required',
                'title' => _p('cache_time_must_be_an_integer')
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_category_clean')) ? eval($sPlugin) : false);
    }
}

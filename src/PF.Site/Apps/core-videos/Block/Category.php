<?php

namespace Apps\PHPfox_Videos\Block;

use Phpfox;
use Phpfox_Component;

/**
 * Class Category
 * @package Apps\PHPfox_Videos\Block
 */
class Category extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $aParentId = ($this->request()->get('req2') == 'category' && $this->request()->get('req3') > 0) ? $this->request()->getInt('req3') : 0;
        $iCacheTime = $this->getParam('cache_time', Phpfox::getParam('core.cache_time_default', 0));

        $iCurrentCategory = $this->getParam('sCurrentCategory', null);
        $iParentCategoryId = $this->getParam('iParentCategoryId', 0);


        $aCategories = Phpfox::getService('v.category')->getForUsers(0, 1, 1, $iCacheTime);


        if (!is_array($aCategories)) {
            return false;
        }

        if (empty($aCategories)) {
            return false;
        }

        $this->template()
            ->assign([
                'sHeader' => _p('categories'),
                'aCategories' => $aCategories,
                'iCurrentCategory' => $iCurrentCategory,
                'iParentCategoryId' => $iParentCategoryId,
            ]);

        return 'block';
    }

    /**
     * Get block settings
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('videos_category_cache_time_info'),
                'description' => _p('videos_category_cache_time_description'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
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
                'title' => '"Cache Time" must be an integer.'
            ]
        ];
    }
}

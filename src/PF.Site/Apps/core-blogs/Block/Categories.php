<?php

namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Categories
 * @package Apps\Core_Blogs\Block
 */
class Categories extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        //Hide this blog when login as page
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            return false;
        }

        $iCurrentCategory = $this->getParam('sCurrentCategory', null);
        $iParentCategoryId = $this->getParam('iParentCategoryId', 0);
        $aCategories = Phpfox::getService('blog.category')->getForBrowse(null,
            $this->getParam('sPhotoCategorySubSystem', null));
        if (empty($aCategories)) {
            return false;
        }
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        if (!is_array($aCategories)) {
            return false;
        }
        $this->template()->assign([
            'aCategories' => $aCategories,
            'iCurrentCategory' => $iCurrentCategory,
            'iParentCategoryId' => $iParentCategoryId,
            'sHeader' => _p('categories'),
        ]);

        (($sPlugin = Phpfox_Plugin::get('blog.component_block_categories_process')) ? eval($sPlugin) : false);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        $this->template()->clean([
                'aCategories',
            ]
        );

        (($sPlugin = Phpfox_Plugin::get('blog.component_block_categories_clean')) ? eval($sPlugin) : false);
    }
}

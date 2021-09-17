<?php
namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class CategoryBlock
 * @package Apps\Core_Events\Block
 */
class CategoryBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $sCategory = $this->getParam('sCategory');

        $iCurrentCategory = $this->getParam('sCurrentCategory', null);
        $iParentCategoryId = $this->getParam('iParentCategoryId', 0);

        $aCategories = Phpfox::getService('event.category')->getForBrowse();

        if (!is_array($aCategories)) {
            return false;
        }

        if (!count($aCategories)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' =>  _p('categories'),
                'iCurrentCategory' => $iCurrentCategory,
                'iParentCategoryId' => $iParentCategoryId,
                'aCategories' => $aCategories,
                'sCategory' => $sCategory
            )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_category_clean')) ? eval($sPlugin) : false);
    }
}

<?php

namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class AddCategoryList
 * @package Apps\Core_Blogs\Block
 */
class AddCategoryList extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aSelected = $this->getParam('aSelectedCategories');

        $this->template()->assign(array(
            'sCategories' => Phpfox::getService('blog.category')->getSelect(false, true),
            'bMultiple' => $this->getParam('bMultiple', true),
        ));

        if (!empty($aSelected)) {
            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    $Behavior.core_blog_init_category = function(){
                        var aCategories = explode(",", "' . implode(',', $aSelected) . '");
                        var i;
                        for (i in aCategories) {
                             $(".js_blog_category_" + aCategories[i]).attr("selected", true);
                        }
                    }
                 </script>'
            ));
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_block_add_category_list_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_block_add_category_list_clean')) ? eval($sPlugin) : false);
    }
}


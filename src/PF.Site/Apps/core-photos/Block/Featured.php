<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class Featured extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if(!(int)$iLimit)
        {
            return false;
        }
        $iCacheTime = $this->getParam('cache_time', 5);
        // Get the featured random image
        list(, $aFeaturedImages) = Phpfox::getService('photo')->getFeatured($iLimit, $iCacheTime);

        // If not images were featured lets get out of here
        if (!count($aFeaturedImages)) {
            return false;
        }
        // If this is not AJAX lets display the block header, footer etc...
        if (!PHPFOX_IS_AJAX) {
            $this->template()->assign(array(
                    'sHeader' => _p('featured_photos'),
                    'sBlockJsId' => 'featured_photo'
                )
            );
        }
        // Assign template vars
        $this->template()->assign(array(
                'aFeaturedImages' => $aFeaturedImages,
                'iRefreshTime' => Phpfox::getService('photo')->getFeaturedRefreshTime()
            )
        );
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Featured Photos Limit'),
                'description' => _p('Define the limit of how many featured photos can be displayed when viewing the photo section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Featured Photos Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Featured Photos</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Featured Photos Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_featured_clean')) ? eval($sPlugin) : false);
    }
}
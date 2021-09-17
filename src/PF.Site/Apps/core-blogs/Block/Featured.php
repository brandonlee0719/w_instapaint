<?php

namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

/**
 * Class Featured
 * @package Apps\Core_Blogs\Block
 */
class Featured extends Phpfox_Component
{
    const IMG_SUFFIX = '_240';

    public function process()
    {
        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }
        $iCacheTime = $this->getParam('cache_time', 5);
        // Get the featured random blog
        $aFeaturedBlogs = Phpfox::getService('blog')->getFeatured(abs($iLimit), $iCacheTime);

        // If not blogs were featured lets get out of here
        if (!count($aFeaturedBlogs)) {
            return false;
        }

        // Get image for the blog
        foreach ($aFeaturedBlogs as &$aRow) {
            if (!empty($aRow['image_path'])) {
                $aRow['image'] = Phpfox::getService('blog')->getImageUrl($aRow['image_path'], $aRow['server_id'],
                    self::IMG_SUFFIX);
            } else {
                list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aRow['text'],
                    'img');
                $aRow['text'] = $sDescription;
                $aRow['image'] = empty($aImages) ? '' : str_replace('_view', '', $aImages[0]);
            }

            $aRow['total_view'] = $aRow['ttv_blog'];
        }

        // If this is not AJAX lets display the block header, footer etc...
        if (!PHPFOX_IS_AJAX) {
            $this->template()->assign(array(
                    'sHeader' => _p('featured_blog'),
                    'sBlockJsId' => 'featured_blog'
                )
            );
        }
        // Assign template vars
        $this->template()->assign(array(
                'aFeaturedBlogs' => $aFeaturedBlogs,
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
                'info' => _p('Featured Blogs Limit'),
                'description' => _p('Define the limit of how many featured blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Featured Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Featured Blogs</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Featured Blogs Limit" must be greater than or equal to 0'
            ]
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'aFeaturedBlogs',
                'sHeader',
                'sBlockJsId',
                'limit',
                'cache_time'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('blog.component_block_featured_clean')) ? eval($sPlugin) : false);
    }
}

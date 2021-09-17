<?php
namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

/**
 * Class PopularTopic
 * @package Apps\Core_Blogs\Block
 * @deprecated in v4.6.0, remove in next version
 */
class PopularTopic extends Phpfox_Component
{
    public function process()
    {
        $bNotShowThiBlock = defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE') || !Phpfox::isModule('tag') || Phpfox::getParam('tag.enable_hashtag_support');

        // Check if do not show this block
        if ($bNotShowThiBlock) {
            return false;
        }

        $iLimit = $this->getParam('limit', 20);
        if (!$iLimit) {
            return false;
        }
        $aHotTags = Phpfox::getService('blog')->getHotTopics($iLimit);

        if (empty($aHotTags)) {
            return false;
        }

        // If this is not AJAX lets display the block header, footer etc...
        if (!PHPFOX_IS_AJAX) {
            $this->template()->assign(array(
                    'sHeader' => _p('popular_topics'),
                    'sBlockJsId' => 'popular_topics'
                )
            );
        }
        // Assign template vars
        $this->template()->assign(array(
                'aHotTags' => $aHotTags
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
                'info' => _p('Popular Topics Limit'),
                'description' => _p('Define the limit of how many popular topics can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 20,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
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
                'title' => '"Popular Topics Limit" must be greater than or equal to 0'
            ]
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        $this->template()->clean([
                'aHotTags',
            ]
        );
        (($sPlugin = Phpfox_Plugin::get('blog.component_block_featured_clean')) ? eval($sPlugin) : false);
    }
}

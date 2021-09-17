<?php

namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class TopBloggers
 * @package Apps\Core_Blogs\Block
 */
class TopBloggers extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        $iMinPost = $this->getParam('min_post', 10);
        if (!(int)$iLimit) {
            return false;
        }
        // Get Top Blogger
        $aRows = Phpfox::getService('blog')->getTopUsers($iLimit, $iMinPost, (bool)$this->getParam('cache'),
            (int)$this->getParam('cache_time'));

        if (!is_array($aRows) || !count($aRows)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('top_bloggers'),
                'aTopBloggers' => $aRows,
                'bDisplayBlogCount' => $this->getParam('display_blog_count', 1)
            )
        );

        (($sPlugin = Phpfox_Plugin::get('blog.component_block_top_process')) ? eval($sPlugin) : false);

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Top Bloggers Limit'),
                'description' => _p('Define the limit of how many top bloggers can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Display Post Count for Top Bloggers'),
                'description' => _p('Set this to <b>True</b> if you would like to display the post count besides the names of each of the <b>Top Bloggers</b>.<br><br>Note that this feature relies on the theme you are using and if the theme is not the default theme provided this might not be displayed.'),
                'value' => 1,
                'type' => 'boolean',
                'var_name' => 'display_blog_count',
            ],
            [
                'info' => _p('Blog Count for Top Bloggers'),
                'description' => _p('Before a user can be considered to be a Top Blogger they must enter X amount of blog(s) where X is the value of this setting.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'min_post',
            ],
            [
                'info' => _p('Cache Top Bloggers'),
                'description' => _p('Set this to <b>True</b> if we should cache the <b>Top Bloggers</b>. It always a good idea to cache such things as there is no need to run an extra query to the database to find out which users are the <b>Top Bloggers</b> as this requires counting all of the blogs added.<br><br>Note that this setting controls how long to keep the cache.'),
                'value' => true,
                'type' => 'boolean',
                'var_name' => 'cache',
            ],
            [
                'info' => _p('Top Bloggers Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Top Bloggers</b> by minutes.<br><br>Note this setting will have not affect if the setting Cache Top Bloggers is disabled.'),
                'value' => 180,
                'type' => 'integer',
                'var_name' => 'cache_time',
            ],
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Top Bloggers Limit" must be greater than or equal to 0'
            ],
            'min_post' => [
                'def' => 'int:required',
                'min' => 1,
                'title' => '"Blog Count for Top Bloggers" must be greater than 0'
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
                'aTopBlogger',
                'sHeader',
                'limit',
                'cache',
                'cache_time',
                'display_blog_count',
                'min_post'
            )
        );
        (($sPlugin = Phpfox_Plugin::get('blog.component_block_top_clean')) ? eval($sPlugin) : false);
    }
}

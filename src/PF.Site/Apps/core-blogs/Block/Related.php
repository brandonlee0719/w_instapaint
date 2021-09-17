<?php

namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Related
 * @package Apps\Core_Blogs\Block
 */
class Related extends \Phpfox_Component
{
    const IMG_SUFFIX = '_240';

    public function process()
    {
        $aBlog = $this->getParam('aBlog');
        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

        // Get current categories of this blog
        $aCategories = Phpfox::getService('blog.category')->getCategoriesByBlogId($aBlog['blog_id']);

        if (empty($aCategories)) {
            return false;
        }

        $aSelectedCategories = array();
        foreach ($aCategories as $aCategory) {
            $aSelectedCategories[] = $aCategory['category_id'];
        }
        if (empty($aSelectedCategories)) {
            return false;
        }

        $aBlogs = Phpfox::getService('blog')->inThisCategory($aBlog, $aSelectedCategories, $iLimit);

        // If not blogs were featured lets get out of here
        if (empty($aBlogs)) {
            return false;
        }

        // Get image for the blog
        foreach ($aBlogs as &$aRow) {
            if (!empty($aRow['image_path'])) {
                $aRow['image'] = Phpfox::getService('blog')->getImageUrl($aRow['image_path'], $aRow['server_id'],
                    self::IMG_SUFFIX);
            } else {
                list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aRow['text'],
                    'img');
                $aRow['text'] = $sDescription;
                $aRow['image'] = empty($aImages) ? '' : str_replace('_view', '', $aImages[0]);
            }
        }

        $this->template()->assign(array(
                'sHeader' => _p('suggestion'),
                'aBlogs' => $aBlogs
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
                'info' => _p('Suggestion Blogs Limit'),
                'description' => _p('Define the limit of how many blogs can be displayed when viewing the blog detail. Set 0 will hide this block.'),
                'value' => 4,
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
                'title' => '"Suggestion Blogs Limit" must be greater than or equal to 0'
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
                'aBlogs',
                'sHeader',
                'limit',
            )
        );
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_album_tag_clean')) ? eval($sPlugin) : false);
    }
}

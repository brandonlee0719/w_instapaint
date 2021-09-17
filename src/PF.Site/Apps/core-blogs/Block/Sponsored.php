<?php

namespace Apps\Core_Blogs\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Sponsored
 * @package Apps\Core_Blogs\Block
 */
class Sponsored extends Phpfox_Component
{
    const IMG_SUFFIX = '_240';

    public function process()
    {
        if (!Phpfox::isModule('ad')) {
            return false;
        }
        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }
        $iCacheTime = $this->getParam('cache_time', 5);
        $aSponsorBlogs = Phpfox::getService('blog')->getRandomSponsored($iLimit, $iCacheTime);

        if (empty($aSponsorBlogs)) {
            return false;
        }

        // Get image for the blog
        foreach ($aSponsorBlogs as &$aRow) {
            if (!empty($aRow['image_path'])) {
                $aRow['image'] = Phpfox::getService('blog')->getImageUrl($aRow['image_path'], $aRow['server_id'],
                    self::IMG_SUFFIX);
            } else {
                list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aRow['text'],
                    'img');
                $aRow['text'] = $sDescription;
                $aRow['image'] = empty($aImages) ? '' : str_replace('_view', '', $aImages[0]);
            }

            if (Phpfox::isModule('ad')) {
                Phpfox::getService('ad.process')->addSponsorViewsCount($aRow['sponsor_id'], 'blog');
            }

            $aRow['total_view'] = $aRow['ttv_blog'];
        }

        $this->template()->assign(array(
                'aSponsorBlogs' => $aSponsorBlogs,
                'sHeader' => _p('sponsored_blog'),
            )
        );
        if (Phpfox::getUserParam('blog.can_sponsor_blog') || Phpfox::getUserParam('blog.can_purchase_sponsor')) {
            $this->template()->assign([
                'aFooter' => array(_p('sponsor_your_blog') => $this->url()->makeUrl('blog', array('view' => 'my')))
            ]);
        }
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Sponsored Blogs Limit'),
                'description' => _p('Define the limit of how many sponsored blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Blogs</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Sponsored Blogs Limit" must be greater than or equal to 0'
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
                'aSponsorBlogs',
                'sHeader',
                'aFooter',
                'limit',
                'cache_time'
            )
        );
        (($sPlugin = Phpfox_Plugin::get('blog.component_block_featured_clean')) ? eval($sPlugin) : false);
    }
}

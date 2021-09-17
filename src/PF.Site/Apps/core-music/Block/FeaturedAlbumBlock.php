<?php
/**
 * [PHPFOX_HEADER]
 */


namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class FeaturedAlbumBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
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
        $aAlbums = (array)Phpfox::getService('music.album')->getFeaturedAlbums($iLimit, $iCacheTime);

        if (!count($aAlbums)) {
            return false;
        }
        $this->template()->assign(array(
                'sHeader' => _p('featured_albums'),
                'aFeaturedAlbums' => $aAlbums,
                'sDefaultThumbnail' => Phpfox::getParam('music.default_album_photo')
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
                'info' => _p('Featured Albums Limit'),
                'description' => _p('Define the limit of how many featured albums can be displayed when viewing the album section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Featured Albums Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Featured Albums</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => _p('"Featured Albums Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_featured_album_clean')) ? eval($sPlugin) : false);
    }
}
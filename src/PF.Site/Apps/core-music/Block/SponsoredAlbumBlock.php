<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class SponsoredAlbumBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!Phpfox::isModule('ad')) {
            return false;
        }
        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $iLimit = $this->getParam('limit', 4);
        if(!(int)$iLimit)
        {
            return false;
        }
        $iCacheTime = $this->getParam('cache_time', 5);
        $aSponsorAlbum = \Phpfox::getService('music')->getRandomSponsoredAlbum($iLimit, $iCacheTime);
        if (empty($aSponsorAlbum)) {
            return false;
        }

        foreach ($aSponsorAlbum as $aSponsor)
        {
            Phpfox::getService('ad.process')->addSponsorViewsCount($aSponsor['sponsor_id'], 'music', 'sponsorAlbum');
        }

        $this->template()->assign(array(
                'sHeader' => _p('sponsored_music_album'),
                'aSponsorAlbum' => $aSponsorAlbum,
                'sDefaultThumbnail' => Phpfox::getParam('music.default_album_photo')
            )
        );
        if (Phpfox::getUserParam('music.can_sponsor_album') || Phpfox::getUserParam('music.can_purchase_sponsor_album')) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('encourage_sponsor_album') => $this->url()->makeUrl('music.browse.album',
                        array('view' => 'my-album', 'sponsor' => 1))
                )
            ));
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
                'info' => _p('Sponsored Albums Limit'),
                'description' => _p('Define the limit of how many sponsored albums can be displayed when viewing the album section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Albums Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Albums</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => _p('"Sponsored Albums Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_sponsor_album_clean')) ? eval($sPlugin) : false);
    }
}
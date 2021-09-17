<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class SponsoredSongBlock extends \Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
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
        $aSponsorSong = \Phpfox::getService('music')->getRandomSponsoredSongs($iLimit, $iCacheTime);

        if (empty($aSponsorSong)) {
            return false;
        }

        foreach ($aSponsorSong as $aSong) {
            Phpfox::getService('ad.process')->addSponsorViewsCount($aSong['sponsor_id'], 'music', 'sponsorSong');
        }

        $this->template()->assign(array(
                'sHeader' => _p('sponsored_music_songs'),
                'aSponsorSong' => $aSponsorSong,
                'sDefaultThumbnail' => Phpfox::getParam('music.default_song_photo'),
                'sCustomPlayId' => 'js_sponsor_block_track_player'
            )
        );
        if (Phpfox::getUserParam('music.can_sponsor_song') || Phpfox::getUserParam('music.can_purchase_sponsor_song')) {
            $this->template()->assign([
                'aFooter' => array(
                    _p('encourage_sponsor_song') => $this->url()->makeUrl('music', array('view' => 'my'))
                )
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
                'info' => _p('Sponsored Songs Limit'),
                'description' => _p('Define the limit of how many sponsored songs can be displayed when viewing the song section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Songs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Songs</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => _p('"Sponsored Songs Limit" must be greater than or equal to 0')
            ],
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_sponsor_song_clean')) ? eval($sPlugin) : false);
    }
}
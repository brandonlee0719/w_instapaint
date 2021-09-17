<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class SongBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');

        $iLimit = $this->getParam('limit', 4);
        if(!(int)$iLimit)
        {
            return false;
        }
        $sExtra = Phpfox::getService('music')->getConditionsForSettingPageGroup('ms');
        $aSongs = Phpfox::getService('music')->getSongs($aUser['user_id'], null, $iLimit,false,$sExtra);

        if (!count($aSongs)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('latest_songs'),
                'sBlockJsId' => 'profile_music_song',
                'aSongs' => $aSongs,
                'bIsMusician' => true,
                'sDefaultThumbnail' => Phpfox::getParam('music.default_song_photo'),
                'sCustomPlayId' => 'js_my_block_track_player'
            )
        );

        if (Phpfox::getUserId() == $aUser['user_id']) {
            $this->template()->assign('sDeleteBlock', 'profile');
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
                'info' => _p('Latest Songs Limit'),
                'description' => _p('Define the limit of how many latest songs can be displayed when viewing the profile section.'),
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
                'title' => _p('"Latest Songs Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_song_clean')) ? eval($sPlugin) : false);

        $this->template()->clean('sCustomPlayId');
    }
}
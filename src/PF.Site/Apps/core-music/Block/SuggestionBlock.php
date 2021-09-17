<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class SuggestionBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $aSong = $this->getParam('aSong');
        if (!$aSong) {
            return false;
        }
        if (!isset($aSong['genres']) || !count($aSong['genres'])) {
            return false;
        }
        $iLimit = $this->getParam('limit', 4);
        if(!(int)$iLimit)
        {
            return false;
        }
        $aSuggestion = Phpfox::getService('music')->getSuggestSongs($aSong, $iLimit);

        if (!is_array($aSuggestion)) {
            return false;
        }

        if (!count($aSuggestion)) {
            return false;
        }
        
        $this->template()->assign(array(
                'sHeader' => _p('suggestion_uppercase'),
                'aSuggestSongs' => $aSuggestion,
                'sDefaultThumbnail' => Phpfox::getParam('music.default_song_photo'),
                'sCustomPlayId' => 'js_suggestion_block_track_player'
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
                'info' => _p('Suggestion Songs Limit'),
                'description' => _p('Define the limit of how many suggest songs can be displayed when viewing the song detail. Set 0 will hide this block'),
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
                'title' => _p('"Suggestion Songs Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_suggestion_clean')) ? eval($sPlugin) : false);
    }
}
<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class RelatedAlbumBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $aAlbum = $this->getParam('aAlbum');
        if (!$aAlbum) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if(!(int)$iLimit || $aAlbum['user_id'] == Phpfox::getUserId())
        {
            return false;
        }
        $sConds = 'AND ma.user_id =' . $aAlbum['user_id'] . ' AND ma.album_id <> ' . $aAlbum['album_id'];
        $aRelated = Phpfox::getService('music.album')->getAlbums(null, $iLimit, $sConds);
        $aUser = Phpfox::getService('user')->getUser($aAlbum['user_id']);

        if (!is_array($aRelated)) {
            return false;
        }

        if (!count($aRelated)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('more_from_name', [
                    'name' => strtr(':name', [
                        ':name' => $aUser['full_name'],
                    ])
                ]),
                'aRelatedAlbums' => $aRelated,
                'aFooter' => array(_p('view_more') => url('music.browse.album', ['user' => $aAlbum['user_id']]))
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
                'info' => _p('Related Albums Limit'),
                'description' => _p('Define the limit of how many related albums can be displayed when viewing the album detail. Set 0 will hide this block'),
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
                'title' => _p('"Related Albums Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_related_album_clean')) ? eval($sPlugin) : false);
    }
}
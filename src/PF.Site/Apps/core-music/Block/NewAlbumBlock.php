<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

defined('PHPFOX') or exit('NO DICE!');

class NewAlbumBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $aParentModule = $this->getParam('aParentModule');
        $iLimit = $this->getParam('limit', 4);
        if(!(int)$iLimit)
        {
            return false;
        }
        $aAlbums = \Phpfox::getService('music.album')->getAlbums($aParentModule, $iLimit);

        if (!count($aAlbums)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('new_albums'),
                'aNewAlbums' => $aAlbums,
                'sDefaultThumbnail' => \Phpfox::getParam('music.default_album_photo')
            )
        );

        if (count($aAlbums) == 5) {
            $this->template()->assign(array(
                    'aFooter' => array(
                        _p('view_more') => $this->url()->makeUrl('music.browse.album')
                    )
                )
            );
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
                'info' => _p('New Albums Limit'),
                'description' => _p('Define the limit of how many new albums can be displayed when viewing the song section. Set 0 will hide this block'),
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
                'title' => _p('"New Albums Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_new_album_clean')) ? eval($sPlugin) : false);
    }
}
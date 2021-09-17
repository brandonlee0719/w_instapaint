<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class UploadBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        $aSong = $this->getParam('aSong', '');
        if (empty($aSong)) {
            return false;
        }

        $this->template()->assign([
            'aForms' => $aSong,
            'sModule' => $aSong['module_id'],
            'bIsEdit' => false,
            'aGenres' => Phpfox::getService('music.genre')->getList(1),
            'aAlbums' => Phpfox::getService('music.album')->getAll(Phpfox::getUserId(),
                (empty($aSong['module_id'])) ? false : $aSong['module_id'], !empty($aSong['item_id']) ? $aSong['item_id'] : 0),
            'bIsUploaded' => true,
            'iPhotoMaxFileSize' => Phpfox::isModule('photo') ? Phpfox::getUserParam('photo.photo_max_upload_size') : 0,
        ]);
        if ($this->getParam('iAlbumId')) {
            $this->template()->assign([
                'bIsEditAlbum' => true
            ]);
        }
        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                    'type' => 'music_song',
                    'id' => 'js_file_holder_' . (isset($aSong['song_id']) ? $aSong['song_id'] : 0),
                    'edit_id' => 0,

                )
            );
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_upload_clean')) ? eval($sPlugin) : false);

        $this->clearParam('inline_album');
    }
}
<?php
namespace Apps\Core_Music\Block;

defined('PHPFOX') or exit('NO DICE!');

class AlbumRowsBlock extends \Phpfox_Component
{

    public function process()
    {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $custom = $this->getParam('custom_param_' . $this_feed_id);
            $this->template()->assign([
                'aAlbum' => $custom,
            ]);
        }
        $this->template()->assign([
            'bIsInFeed' => true,
        ]);
    }
}
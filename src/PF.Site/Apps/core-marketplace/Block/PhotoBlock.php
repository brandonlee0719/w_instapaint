<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class PhotoBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aListing = $this->getParam('aListing');

        $aImages = Phpfox::getService('marketplace')->getImages($aListing['listing_id']);


        $this->template()->assign(array(
                'aImages' => $aImages,
                'aForms' => $aListing,
                'iListingId' => $aListing['listing_id'],
                'iTotalImage' => Phpfox::getService('marketplace')->countImages($aListing['listing_id']),
                'iTotalImageLimit' => Phpfox::getUserParam('marketplace.total_photo_upload_limit'),
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_photo_clean')) ? eval($sPlugin) : false);
    }
}
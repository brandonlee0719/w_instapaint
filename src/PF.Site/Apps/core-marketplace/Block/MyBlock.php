<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class MyBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aListing = $this->getParam('aListing');
        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        list($iCnt, $aListings) = Phpfox::getService('marketplace')->getUserListings($aListing['listing_id'],
            $aListing['user_id'], $iLimit);

        if (!$iCnt) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('more_from_seller'),
                'aMyListings' => $aListings
            )
        );

        if ($iCnt > $iLimit) {
            $this->template()->assign(array(
                    'aFooter' => array(
                        _p('view_more') => $this->url()->makeUrl($aListing['user_name'], array('marketplace'))
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
                'info' => _p('More From Seller Limit'),
                'description' => _p('Define the limit of how many listings from this seller can be displayed when viewing the listing detail. Set 0 will hide this block'),
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
                'title' => _p('"More From Seller Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_block_my_clean')) ? eval($sPlugin) : false);
    }
}
<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class SponsoredBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        if (!Phpfox::isModule('ad')) {
            return false;
        }
        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        $iCacheTime = $this->getParam('cache_time', 5);

        $aItems = Phpfox::getService('marketplace')->getSponsorListings($iLimit, $iCacheTime);

        if (empty($aItems)) {
            return false;
        }

        foreach ($aItems as $aItem) {
            Phpfox::getService('ad.process')->addSponsorViewsCount($aItem['sponsor_id'], 'marketplace');
        }

        $this->template()->assign(array(
                'sHeader' => _p('sponsored_listing'),
                'aSponsorListings' => $aItems
            )
        );
        if (Phpfox::getUserParam('marketplace.can_sponsor_marketplace') || Phpfox::getUserParam('marketplace.can_purchase_sponsor')) {
            $this->template()->assign(array(
                    'aFooter' => array(
                        _p('encourage_sponsor_listing') =>
                            $this->url()->makeUrl('marketplace', array('view' => 'my'))
                    ),
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
                'info' => _p('Sponsored Listings Limit'),
                'description' => _p('Define the limit of how many sponsored listings can be displayed when viewing the marketplace section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Listings Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Listings</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => _p('"Sponsored Listings Limit" must be greater than or equal to 0')
            ],
        ];
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
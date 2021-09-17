<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class Sponsored extends Phpfox_Component
{
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
        $aSponsorPhotos = Phpfox::getService('photo')->getRandomSponsored($iLimit, $iCacheTime);

        if (!count($aSponsorPhotos)) {
            return false;
        }

        $this->template()->assign(array(
                'aSponsorPhotos' => $aSponsorPhotos,
                'sHeader' => _p('sponsored_photos')
            )
        );

        if (Phpfox::getUserParam('photo.can_sponsor_photo') || Phpfox::getUserParam('photo.can_purchase_sponsor')) {
            $this->template()
                ->assign([
                    'aFooter' => array(
                        _p('encourage_sponsor_photo') => $this->url()->makeUrl('photo', array('view' => 'my'))
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
                'info' => _p('Sponsored Photos Limit'),
                'description' => _p('Define the limit of how many sponsored photos can be displayed when viewing the photo section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Photos Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Photos</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => _p('"Sponsored Photos Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_featured_clean')) ? eval($sPlugin) : false);
    }
}
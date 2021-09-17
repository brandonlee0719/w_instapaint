<?php

namespace Apps\PHPfox_Videos\Block;

use Phpfox;
use Phpfox_Component;

class Sponsored extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        if (!Phpfox::isModule('ad')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        $iCacheTime = $this->getParam('cache_time', Phpfox::getParam('core.cache_time_default', 0));
        $aVideos = Phpfox::getService('v.video')->getSponsored($iLimit, $iCacheTime);

        if (empty($aVideos)) {
            return false;
        }

        $this->template()
            ->assign([
                'sHeader' => _p('sponsored_videos'),
                'aVideos' => $aVideos
            ]);

        if (user('v.can_sponsor_v') || user('v.can_purchase_sponsor')) {
            $this->template()
                ->assign([
                    'aFooter' => array(
                        _p('encourage_sponsor_video') => $this->url()->makeUrl('video', array('view' => 'my'))
                    )
                ]);
        }

        return 'block';
    }

    /**
     * @return array
     */
    function getSettings()
    {
        return [
            [
                'info' => _p('Sponsored Videos Limit'),
                'description' => _p('Define the limit of how many sponsored videos can be displayed when viewing the video section.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('sponsored_videos_cache_time_info'),
                'description' => _p('sponsored_videos_cache_time_description'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }

    /**
     * Validation
     *
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('sponsored_video_limit')
            ]
        ];
    }
}

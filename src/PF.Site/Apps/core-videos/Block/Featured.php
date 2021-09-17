<?php

namespace Apps\PHPfox_Videos\Block;

use Phpfox;
use Phpfox_Component;

class Featured extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $iLimit = $this->getParam('limit', 4);
        $iCacheTime = $this->getParam('cache_time', Phpfox::getParam('core.cache_time_default', 0));
        $aVideos = \Phpfox::getService('v.video')->getFeatured($iLimit, $iCacheTime);

        if (empty($aVideos)) {
            return false;
        }

        $this->template()
            ->assign([
                'sHeader' => _p('featured_videos'),
                'aVideos' => $aVideos,
            ]);

        return 'block';
    }

    /**
     * @return array
     */
    function getSettings()
    {
        return [
            [
                'info' => _p('Featured Videos Limit'),
                'description' => _p('Define the limit of how many featured videos can be displayed when viewing the video section.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('featured_videos_cache_time_info'),
                'description' => _p('featured_videos_cache_time_description'),
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
                'title' => _p('featured_video_limit')
            ]
        ];
    }
}

<?php

namespace Apps\PHPfox_Videos\Block;

use Phpfox;
use Phpfox_Component;

class Suggested extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 4);

        $aVideo = $this->getParam('aVideo');
        if (empty($aVideo)) {
            return false;
        }

        $aVideos = Phpfox::getService('v.video')->getRelatedVideos($aVideo['video_id'], $iLimit);

        if (!count($aVideos)) {
            return false;
        }

        $this->template()
            ->assign([
                'sHeader' => _p('suggested_videos'),
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
                'info' => _p('Suggested Videos Limit'),
                'description' => _p('Define the limit of how many suggested videos can be displayed when viewing a video detail.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
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
                'title' => _p('suggested_video_limit')
            ]
        ];
    }
}

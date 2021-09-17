<?php

namespace Apps\Core_Announcement\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class More extends Phpfox_Component
{
    public function process()
    {
        $iAnnouncementId = $this->getParam('announcement_id');
        $iLimit = $this->getParam('limit', 4);

        if (empty($iAnnouncementId) || !$iLimit) {
            return false;
        }

        $aAnnouncements = Phpfox::getService('announcement')->getMore($iAnnouncementId, $iLimit);
        if (empty($aAnnouncements)) {
            return false;
        }
        $this->template()
            ->assign([
                'aAnnouncements' => $aAnnouncements,
                'sHeader' => _p('more_announcements'),
                'aFooter' => [
                    _p('view_all_announcements') => [
                        'link' => $this->url()->makeUrl('announcement')
                    ]
                ]
            ]);

        return 'block';
    }

    /**
     * Block settings
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('announcement_block_more_setting_limit_info'),
                'description' => _p('announcement_block_more_setting_limit_description'),
                'value' => 4,
                'var_name' => 'limit',
                'type' => 'integer'
            ]
        ];
    }

    /**
     * Validation of block settings
     *
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('limit_must_greater_or_equal_0')
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_block_more_clean')) ? eval($sPlugin) : false);
    }
}

<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class GroupEvents extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aPage = $this->getParam('aPage');
        if (!Phpfox::isModule('event') || !$this->getParam('is_show', true) ||
            (!Phpfox::isAdmin() && !\Phpfox::getService('groups')->isMember($aPage['page_id']) && in_array($aPage['reg_method'],
                    [1, 2]))
        ) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        $events = Phpfox::getService('event')->getForParentBlock('groups', $aPage['page_id'], $iLimit);
        if (!$events) {
            return false;
        }

        $this->template()->assign([
            'sHeader' => _p('Group Events'),
            'events' => $events,
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
                'info' => _p('block_event_show_info'),
                'description' => '',
                'value' => false,
                'var_name' => 'is_show',
                'type' => 'boolean'
            ],
            [
                'info' => _p('block_event_limit_info'),
                'description' => _p('block_event_limit_description'),
                'value' => 4,
                'var_name' => 'limit',
                'type' => 'integer'
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
                'title' => _p('validator_groups_groupevents_limit'),
                'min' => 0
            ]
        ];
    }
}

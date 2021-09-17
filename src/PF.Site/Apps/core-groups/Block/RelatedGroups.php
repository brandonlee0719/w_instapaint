<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Plugin;

class RelatedGroups extends \Phpfox_Component
{
    public function process()
    {
        $aGroup = $this->getParam('aPage', false);
        $iLimit = $this->getParam('limit', 4);

        if (!$iLimit || !$aGroup || !$this->getParam('is_show', true)) {
            return false;
        }
        // get groups with the same category
        $aGroups = Phpfox::getService('groups')->getSameCategoryPages($aGroup['page_id'], $iLimit);

        if (!count($aGroups)) {
            return false;
        }

        $this->template()->assign([
            'aGroups' => $aGroups,
            'sDefaultCoverPath' => Phpfox::getParam('groups.default_cover_photo')
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
                'info' => _p('block_related_groups_show_info'),
                'description' => '',
                'value' => false,
                'var_name' => 'is_show',
                'type' => 'boolean'
            ],
            [
                'info' => _p('block_related_groups_info'),
                'description' => _p('block_related_groups_description'),
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
                'title' => _p('validator_groups_relatedgroups_limit'),
                'min' => 0
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_related_groups_clean')) ? eval($sPlugin) : false);
    }
}

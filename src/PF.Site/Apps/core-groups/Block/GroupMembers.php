<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class GroupMembers extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit || !$this->getParam('is_show', true)) {
            return false;
        }

        $aPage = $this->getParam('aPage');
        list($iTotalMembers, $aMembers) = \Phpfox::getService('groups')->getMembers($aPage['page_id'], $iLimit);

        if (empty($aMembers)) {
            return false;
        }

        if ($iTotalMembers > $iLimit) {
            $this->template()->assign([
                'aFooter' => [
                    _p('more') => [
                        'link' => 'javascript:void(0)',
                        'attr' => ' onclick="return $Core.box(\'like.browse\', 400, \'type_id=groups&amp;item_id=' . $aPage['page_id'] . '\');"'
                    ]
                ]
            ]);
        }
        $this->template()->assign([
                'sHeader' => _p('Members'),
                'aMembers' => $aMembers,
            ]
        );

        if (!PHPFOX_IS_AJAX || defined("PHPFOX_IN_DESIGN_MODE")) {
            return 'block';
        }

        return null;
    }

    /**
     * Block settings
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('block_members_show_info'),
                'description' => '',
                'value' => true,
                'var_name' => 'is_show',
                'type' => 'boolean'
            ],
            [
                'info' => _p('block_members_info'),
                'description' => _p('block_members_description'),
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
                'title' => _p('validator_groups_groupmembers_limit'),
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
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_like_clean')) ? eval($sPlugin) : false);
    }
}

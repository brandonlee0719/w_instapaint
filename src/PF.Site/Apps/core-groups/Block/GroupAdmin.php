<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class GroupAdmin extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 4);
        $aGroup = $this->getParam('aPage');

        if (!$iLimit || empty($aGroup) || !Phpfox::getService('groups')->hasPerm($aGroup['page_id'], 'groups.view_admins')) {
            return false;
        }

        $this->template()->assign([
            'sHeader' => _p('Admins'),
            'aPageAdmins' => Phpfox::getService('groups')->getPageAdmins($aGroup['page_id'], 1, $iLimit),
        ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('groups_block_admin_limit_info'),
                'description' => _p('groups_block_admin_limit_description'),
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
                'title' => _p('validator_groups_groupadmin_limit'),
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
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_admin_clean')) ? eval($sPlugin) : false);
    }
}

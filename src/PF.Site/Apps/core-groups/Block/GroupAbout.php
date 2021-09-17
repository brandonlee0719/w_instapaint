<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox_Component;
use Phpfox_Parse_Output;
use Phpfox_Plugin;

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class GroupAbout extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (!$this->getParam('show_about', true)) {
            return false;
        }

        $page = $this->getParam('aPage');
        if ($this->getParam('show_founder', true)) {
            $aUser = [
                'user_id' => $page['owner_user_id'],
                'profile_page_id' => $page['owner_profile_page_id'],
                'server_id' => $page['owner_server_id'],
                'user_name' => $page['owner_user_name'],
                'full_name' => $page['owner_full_name'],
                'gender' => $page['owner_gender'],
                'user_image' => $page['owner_user_image'],
                'is_invisible' => $page['owner_is_invisible'],
                'user_group_id' => $page['owner_user_group_id'],
                'language_id' => $page['owner_language_id'],
                'birthday' => $page['owner_birthday'],
                'country_iso' => $page['owner_country_iso'],
            ];
            $this->template()->assign('aUser', $aUser);
        } elseif (empty($sInfo)) {
            return false;
        }

        $this->template()->assign([
            'page' => $page
        ]);

        return null;
    }

    /**
     * Block settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('groups:block_about_show_info'),
                'description' => _p('groups:block_about_show_description'),
                'value' => true,
                'var_name' => 'show_about',
                'type' => 'boolean'
            ],
            [
                'info' => _p('groups:block_about_show_founder_info'),
                'description' => _p('groups:block_about_show_founder_description'),
                'value' => true,
                'var_name' => 'show_founder',
                'type' => 'boolean'
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_about_clean')) ? eval($sPlugin) : false);
    }
}

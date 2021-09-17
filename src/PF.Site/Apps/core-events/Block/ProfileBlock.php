<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ProfileBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aUser = $this->getParam('aUser');

        if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'event.display_on_profile')) {
            return false;
        }
        $iLimit = $this->getParam('limit',4);
        $aEvents = Phpfox::getService('event')->getForProfileBlock($aUser['user_id'],$iLimit);

        if (!count($aEvents) && !defined('PHPFOX_IN_DESIGN_MODE')) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('events_i_m_attending'),
                'sBlockJsId' => 'profile_event',
                'aEvents' => $aEvents
            )
        );

        if (count($aEvents) == 1) {
            $this->template()->assign('aFooter', array(
                    'View More' => $this->url()->makeUrl('event', array('user' => $aUser['user_id']))
                )
            );
        }

        if (Phpfox::getUserId() == $aUser['user_id']) {
            $this->template()->assign('sDeleteBlock', 'profile');
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
                'info' => _p('Profile Events Limit'),
                'description' => _p('Define the limit of how many profile events can be displayed when viewing the events section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
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
                'title' => _p('"Profile Events Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_block_profile_clean')) ? eval($sPlugin) : false);
    }
}
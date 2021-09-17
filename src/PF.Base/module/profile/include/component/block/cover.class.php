<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Block_Cover
 */
class Profile_Component_Block_Cover extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        if (($iPageId = $this->request()->get('page_id'))) {
            $this->template()->assign(array(
                'iPageId' => $iPageId
            ));
        }
        if (($iGroupId = $this->request()->get('groups_id'))) {
            $this->template()->assign(array(
                'iGroupId' => $iGroupId
            ));
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('profile.component_block_cover_clean')) ? eval($sPlugin) : false);
    }
}

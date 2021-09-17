<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Welcome
 */
class User_Component_Block_Welcome extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $sWelcomeContent = flavor()->active->content();
        if (empty($sWelcomeContent) && !request()->get('force-flavor')){
            return false;
        }

		$this->template()->assign([
            'sWelcomeContent' => $sWelcomeContent
        ]);

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('user.component_block_welcome_clean')) ? eval($sPlugin) : false);
    }
}

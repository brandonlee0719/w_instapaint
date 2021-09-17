<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Controller_App
 */
class Profile_Component_Controller_App extends Phpfox_Component
{
    public function process()
    {
        $data = Core\Event::trigger('profile', str_replace('@App/', '', $this->getParam('app_section')),
            $this->getParam('aUser'));

        $this->template()->assign([
            'data' => $data
        ]);
    }
}

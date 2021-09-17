<?php

namespace Apps\Core_Pages\Controller;

defined('PHPFOX') or exit('NO DICE!');

class ProfileController extends \Phpfox_Component
{
    public function process()
    {
        $this->setParam('bIsProfile', true);

        \Phpfox::getComponent('pages.index', array('bNoTemplate' => true), 'controller');
    }
}

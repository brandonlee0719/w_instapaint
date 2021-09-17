<?php

namespace Apps\Core_Captcha\Block;

use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Form extends Phpfox_Component
{
    public function process()
    {
        $sCaptchaType = Phpfox::getParam('captcha.captcha_type');

        $this->template()->assign(array(
                'sCaptchaType' => $sCaptchaType,
                'sRecaptchaPublicKey' => Phpfox::getParam('captcha.recaptcha_public_key'),
                'sImage' => $this->url()->makeUrl('captcha.image', array('id' => md5(rand(100, 1000)))),
                'sCaptchaData' => null,
                'sCatpchaType' => $this->getParam('captcha_type', null),
                'bCaptchaPopup' => $this->getParam('captcha_popup', false)
            )
        );

        (($sPlugin = Phpfox_Plugin::get('captcha.component_block_form_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('captcha.component_block_form_clean')) ? eval($sPlugin) : false);
    }
}

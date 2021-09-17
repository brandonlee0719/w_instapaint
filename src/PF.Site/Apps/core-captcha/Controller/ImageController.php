<?php

namespace Apps\Core_Captcha\Controller;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ImageController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iLimit = Phpfox::getParam('captcha.captcha_limit');
        $oServiceCaptcha = Phpfox::getService('captcha');
        $sId = $this->request()->get('id');
        $sCode = $oServiceCaptcha->generateCode($iLimit, $sId);
        $oServiceCaptcha->setHash($sCode);
        echo $oServiceCaptcha->displayCaptcha($sCode);
        exit;
    }
}

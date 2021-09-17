<?php

namespace Apps\Core_Captcha\Ajax;

use Phpfox_Ajax;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Ajax extends Phpfox_Ajax
{
    public function reload()
    {
        $sUrl = Phpfox::getLib('url')->makeUrl('captcha.image', array('id' => md5(rand(100, 1000))));
        $sId = htmlspecialchars($this->get('sId'));
        $sInput = htmlspecialchars($this->get('sInput'));
        $this->call('$("#' . $sId . '").attr("src", "' . $sUrl . '").on("load",function(){$(this).css("opacity",1)}); $("#' . $sInput . '").val(""); $("#' . $sInput . '").focus(); $("#js_captcha_process").html("");');
    }
}

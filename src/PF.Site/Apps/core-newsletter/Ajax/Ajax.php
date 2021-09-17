<?php

namespace Apps\Core_Newsletter\Ajax;

use Phpfox_Ajax;
use Phpfox;

class Ajax extends Phpfox_Ajax
{
    public function showPlain()
    {
        $sText = $this->get('sText');
        $sText = str_replace('</p>', PHP_EOL, $sText);
        $sText = Phpfox::getService('newsletter')->filterBbcodeTags(strip_tags($sText));
        $this->call('$("#txtPlain").val($Core.b64DecodeUnicode("' . base64_encode($sText) . '"));');
    }
}

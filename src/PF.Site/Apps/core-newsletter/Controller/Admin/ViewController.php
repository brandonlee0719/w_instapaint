<?php

namespace Apps\Core_Newsletter\Controller\Admin;

use Phpfox_Component;
use Phpfox;

class ViewController extends Phpfox_Component
{
    public function process()
    {
        $iId = $this->request()->getInt('id', 0);
        if (!$iId) {
            $this->url()->send('admincp.app', ['id' => 'Core_Newsletter'], _p('that_newsletter_does_not_exist'));
        }
        $sMode = $this->request()->get('mode', 'html');
        if ($sMode != 'html' && $sMode != 'plain') {
            $this->url()->send('admincp.app', ['id' => 'Core_Newsletter'],
                _p('please_choose_either_html_or_plain_text'));
        }
        $aNewsletter = Phpfox::getService('newsletter')->get($iId);
        if (!$aNewsletter) {
            $this->url()->send('admincp.app', ['id' => 'Core_Newsletter'], _p('that_newsletter_does_not_exist'));
        }
        $aNewsletter['mode'] = $sMode;
        $aNewsletter['text_plain'] = nl2br($aNewsletter['text_plain']);

        $this->template()->errorClearAll();
        $this->template()
            ->setTitle($aNewsletter['subject'])
            ->setTemplate('blank')
            ->assign(array(
                'aNewsletter' => $aNewsletter,
            ));
    }
}

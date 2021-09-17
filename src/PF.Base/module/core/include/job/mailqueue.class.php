<?php

class  Core_Job_MailQueue extends \Core\Queue\JobAbstract
{
    public function perform()
    {
        Phpfox::getLib('mail')->cronSend($this->getParams());
        $this->delete();
    }
}
<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Job_MailingInactive
 */
class User_Job_MailingInactive extends \Core\Queue\JobAbstract
{
    public function perform()
    {
        Phpfox::getService('user.process')->processInactiveJob($this->getJobId());
    }
}

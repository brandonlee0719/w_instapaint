<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Phpfox_Verify
 *
 */
class Phpfox_Verify
{

    /**
     * @var Phpfox_Verify_Driver_Interface
     */
    private $client;

    /**
     * Phpfox_Verify constructor.
     */
    public function __construct()
    {

    }

    /**
     * Generate one time token to SMS
     */
    public function generateOneTimeTokenToSMS()
    {
        $seeks = '0123456789';
        $result = '';

        for ($i = 0; $i < 6; ++$i) {
            $result .= substr($seeks, mt_rand(0, 9), 1);
        }

        return $result;
    }

    /**
     * @return object|Phpfox_Verify_Driver_Interface
     */
    private function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

        (($sPlugin = Phpfox_Plugin::get('core.create_sms_client_service')) ? eval($sPlugin) : false);

        if ($this->client) {
            return $this->client;
        }

        $sService = Phpfox::getParam('core.registration_sms_service');

        switch ($sService) {
            case 'twilio':
                $this->client = Phpfox::getLib('phpfox.verify.driver.twilio');
                break;
            case 'nexmo':
                $this->client = Phpfox::getLib('phpfox.verify.driver.nexmo');
                break;
            case 'clickatell':
                $this->client = Phpfox::getLib('phpfox.verify.driver.clickatell');
                break;
        }

        return $this->client;
    }

    /**
     * @param $to
     * @param $msg
     * @return mixed
     */
    public function sendSMS($to, $msg)
    {
        return $this->getClient()->sendSMS($to, $msg);
    }
}
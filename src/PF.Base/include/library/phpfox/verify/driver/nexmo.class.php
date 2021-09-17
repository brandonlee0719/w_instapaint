<?php

class Phpfox_Verify_Driver_Nexmo extends Phpfox_Verify_Driver_Abstract
{

    /**
     * @const https://rest.nexmo.com/
     */
    const BASE_URL = 'https://rest.nexmo.com/';


    /**
     * @link https://www.twilio.com
     *
     * @param $to
     * @param $msg
     * @return bool
     */
    public function sendSMS($to, $msg)
    {

        $from = Phpfox::getParam('core.nexmo_phone_number');
        $apiKey = Phpfox::getParam('core.nexmo_api_key');
        $apiSecret = Phpfox::getParam('core.nexmo_api_secret');

        $endpointUrl = self::BASE_URL . 'sms/json';
        $postFields = http_build_query([
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'from' => $from,
            'to' => $to,
            'text' => $msg,
        ]);

        $ch = curl_init($endpointUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);

        if (empty($response) || curl_error($ch)) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        $result  = json_decode($response, true);

        if(empty($result['messages'][0]['status']))
            return true;

        return false;
    }
}
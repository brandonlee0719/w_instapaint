<?php

class Phpfox_Verify_Driver_Twilio extends Phpfox_Verify_Driver_Abstract
{

    /**
     * @const https://api.twilio.com/2010-04-01/
     */
    const BASE_URL = 'https://api.twilio.com/2010-04-01/';


    /**
     * @link https://www.twilio.com
     *
     * @param string $to  To phone number
     * @param string $msg Message content
     *
     * @return bool
     */
    public function sendSMS($to, $msg)
    {

        $accountId = Phpfox::getParam('core.twilio_account_id');
        $authToken = Phpfox::getParam('core.twilio_auth_token');
        $from = Phpfox::getParam('core.twilio_phone_number');
        $userpwd = sprintf('%s:%s', $accountId, $authToken);

        $endpointUrl = self::BASE_URL . 'Accounts/' . $accountId
            . '/Messages.json';
        $postFields = http_build_query([
            'To'   => '+' . trim($to, '+'),
            'From' => '+' . trim($from, '+'),
            'Body' => $msg,
        ]);

        $ch = curl_init($endpointUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_USERPWD, $userpwd);

        $response = curl_exec($ch);
        $error = curl_errno($ch);
//        $message =  curl_error($ch);
        curl_close($ch);

        if ($error) {
            return false;
        }

        if (empty($response)) {
            return false;
        }

        $response = json_decode($response, true);

        if (isset($response['error_code']) && $response['error_code']) {
            return false;
        }

        return true;
    }
}
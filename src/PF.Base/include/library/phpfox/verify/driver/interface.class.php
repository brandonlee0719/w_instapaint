<?php

interface Phpfox_Verify_Driver_Interface
{
    /**
     * @param $to
     * @param $msg
     * @return bool
     */
    public function sendSMS($to, $msg);
}
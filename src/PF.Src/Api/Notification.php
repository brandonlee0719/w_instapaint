<?php

namespace Api;

class Notification extends \Core\Api
{
    public function post($app_id = null, $feed_id = 0, $user_id = 0, $force = true)
    {
        if (!$user_id) {
            $user_id = user()->id;
        }

        return \Phpfox::getService('notification.process')->add($app_id, $feed_id, $user_id, null, $force);
    }
}
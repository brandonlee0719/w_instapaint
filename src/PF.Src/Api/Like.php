<?php

namespace Api;

class Like extends \Core\Api
{
    public function post($feedId)
    {
        $this->auth();

        return \Phpfox::getService('like.process')->add('app', $feedId);
    }

    public function delete($feedId)
    {
        $this->auth();

        return \Phpfox::getService('like.process')->delete('app', $feedId);
    }
}
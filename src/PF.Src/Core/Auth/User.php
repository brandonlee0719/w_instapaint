<?php

namespace Core\Auth;

class User
{
    public function isLoggedIn($redirect = false)
    {
        return (\Phpfox::isUser($redirect) ? true : false);
    }

    public function membersOnly()
    {
        \Phpfox::isUser(true);
    }

    public function isAdmin($redirect = false)
    {
        return \Phpfox::isAdmin($redirect);
    }
}
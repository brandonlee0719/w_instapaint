<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminUsersController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        url()->send('user.browse');
    }
}

<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

/*
 * This controller redirects the current user to their corresponding dashboard,
 * and everyone else to the root route.
 */
class MyDashboardController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        if ($dashboardRoute = $securityService->getUserDashboardRoute()) {
            url()->send($dashboardRoute);
        } else {
            url()->send(user()->url);
        }
    }
}

<?php

/*
 * This plugin modifies the "My Dashboard" menu to point to the corresponding dashboard URL
 * depending on the current user.
 */

// Get instance of InstaPaint Security service class
$securityService = \Phpfox::getService('instapaint.security');

// Make sure the "My Dashboard" menu exists in $aMenus
if (isset($aMenus[$securityService::MY_DASHBOARD_MENU_ID])) {

    /*
     * If there is a dashboard route for the current user, user it for the menu URL,
     * otherwise delete the menu since it's irrelevant.
     */
    if ($dashboardRoute = $securityService->getUserDashboardRoute()) {
        $aMenus[$securityService::MY_DASHBOARD_MENU_ID]['url'] = $dashboardRoute;
    } else {
        unset($aMenus[$securityService::MY_DASHBOARD_MENU_ID]);
    }
}

<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminOrdersController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Admin service
        $adminService = \Phpfox::getService('instapaint.admin');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::ADMIN_GROUP_ID]);

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Admin Dashboard » Orders');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Orders');

        $template->buildSectionMenu('admin-dashboard', $instapaintService->getAdminDashboardMenu());

        $template->assign([
            'countOrdersForApproval' => $adminService->countOrdersForApproval(),
            'countOpenOrders' => $adminService->countOpenOrders(),
            'countShippedOrders' => $adminService->countShippedOrders(),
        ]);
    }
}

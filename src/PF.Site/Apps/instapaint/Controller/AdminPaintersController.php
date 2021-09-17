<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPaintersController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        //Get instance of painter service class
        $painterService = \Phpfox::getService('instapaint.painter');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::ADMIN_GROUP_ID]);

        // Count pending approval requests:
        $approvalRequestsCount = $painterService->countApprovalRequests();

        // Count approved painters:
        $approvedPaintersCount = $painterService->countApprovedPainters();

        // Count unapproved painters:
        $unapprovedPaintersCount = $painterService->countUnapprovedPainters();

        // Get phpFox core template service
        $template = $this->template();

        $template->assign([
            'approvalRequestsCount' => number_format($approvalRequestsCount),
            'approvedPaintersCount' => number_format($approvedPaintersCount),
            'unapprovedPaintersCount' => number_format($unapprovedPaintersCount)
        ]);

        // Set view title
        $template->setTitle('Admin Dashboard » Painters');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Painters');

        $template->buildSectionMenu('admin-dashboard', $instapaintService->getAdminDashboardMenu());
    }
}

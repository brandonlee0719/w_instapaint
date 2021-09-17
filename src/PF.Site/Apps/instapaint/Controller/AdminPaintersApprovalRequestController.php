<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPaintersApprovalRequestController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::ADMIN_GROUP_ID]);

        //Get instance of Painter service class
        $painterService = \Phpfox::getService('instapaint.painter');

        // We expect this to be an integer because the route handles this path:
        $approvalRequestId = $this->request()->getInt('req4');

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Admin Dashboard » Painter Approval Request');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Painter Approval Request');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
            $instapaintService->getAdminDashboardMenu(), // Menus array
            'Painters', // Reference menu name
            ['Painter Approval Request' => 'admin-dashboard.painters.approval-request.' . $approvalRequestId] // Menu to be inserted
        ));

        $approvalRequest = $painterService->getApprovalRequestById($approvalRequestId);

        if ($approvalRequest) {
            $approvalRequest['cf_about_me'] = htmlspecialchars($approvalRequest['cf_about_me']);
        }
        $template->assign([
            'approvalRequest' => $approvalRequest,
            'token' => $securityService->getCSRFToken()
        ]);
    }
}

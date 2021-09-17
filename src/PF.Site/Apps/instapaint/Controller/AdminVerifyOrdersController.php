<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class AdminVerifyOrdersController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'painter' => Phpfox::getService('instapaint.painter'),
            'admin' => Phpfox::getService('instapaint.admin')
        ];

        // Allow access to painters only
        $services['security']->allowAccess([
            $services['security']::ADMIN_GROUP_ID
        ]);

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Admin Dashboard » Approve/Reject Orders');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Approve/Reject Orders');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getAdminDashboardMenu(), // Menus array
            'Orders', // Reference menu name
            ['Approve/Reject Orders' => 'admin-dashboard.verify-orders'] // Menu to be inserted
        ));

        $template->assign([
            'orders' => $services['admin']->getOrdersForApproval()
        ]);

    }
}

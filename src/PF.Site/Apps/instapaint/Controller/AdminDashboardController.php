<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Faker\Provider\DateTime;
use Phpfox;

class AdminDashboardController extends \Phpfox_Component
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
            'stats' => Phpfox::getService('instapaint.stats'),
            'admin' => Phpfox::getService('instapaint.admin'),
        ];

        // Allow access to admins only
        $services['security']->allowAccess([$services['security']::ADMIN_GROUP_ID]);

        $stats = $services['stats']->getStats();

        // If this is an AJAX request, return stats JSON:
        if ($_SERVER['QUERY_STRING'] == 'ajax=stats') {
            header('Content-Type: application/json');
            echo json_encode($stats);
            exit();
        }

        // Get phpFox core template service
        $template = $this->template();

        $template->assign([
            'stats' => $stats,
            'countOrdersForApproval' => $services['admin']->countOrdersForApproval(),
        ]);

        $template->setHeader(['jscript/admin-dashboard.js' => 'app_instapaint']);

        // Set view title
        $template->setTitle('Admin Dashboard » General');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Admin Dashboard');

        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->getAdminDashboardMenu());
    }
}

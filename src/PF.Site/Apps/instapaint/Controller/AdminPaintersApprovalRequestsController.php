<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class AdminPaintersApprovalRequestsController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'painter' => Phpfox::getService('instapaint.painter')

        ];

        // Allow access to admins only
        $services['security']->allowAccess([$services['security']::ADMIN_GROUP_ID]);

        // Variables to simplify code
        $template = $this->template();
        $search = $this->search();

        // Set view title
        $template->setTitle('Admin Dashboard » Painter Approval Requests');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        // Set breadcrumb
        $template->setBreadCrumb("Painter Approval Requests");

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getAdminDashboardMenu(), // Menus array
            'Painters', // Reference menu name
            ['Painter Approval Requests' => 'admin-dashboard.painters.approval-requests'] // Menu to be inserted
        ));

        $search->set([
            'type'           => 'approval requests',
            'field'          => 'ar.approval_request_id',
            'ignore_blocked' => true,
            'search_tool'    => [
                'when_field' => 'request_timestamp',
                'when_end_field' => 'request_timestamp',
                'table_alias' => 'ar',
                'search'      => [
                    'action'        => $this->url()->makeUrl('/admin-dashboard/painters/approval-requests/'),
                    'default_value' => 'Search Approval Requests...',
                    'name'          => 'search',
                    'field'         => ['u.full_name', 'u.user_name'],
                ],
                'sort'        => [
                    'latest-requests'     => ['ar.request_timestamp', _p('Latest Requests')],
                    'latest-registrations' => ['u.joined', _p('Latest Sign-ups')],
                ],
                'show'        => [20, 40, 60],
            ],
        ]);

        // Configure search service
        $aBrowseParams = [
            'module_id' => 'instapaint-browse-painter-approval-requests',
            'alias'     => 'ar',
            'field'     => 'approval_request_id',
            'table'     => Phpfox::getT('instapaint_painter_approval_request')
        ];

        // Filter only approved requests
        $search->setCondition('AND is_approved = 0');

        $search->setContinueSearch(false);
        $search->browse()->params($aBrowseParams)->execute();

        $aItems = $this->search()->browse()->getRows();

        // assign variables to template
        $this->template()->assign([
                'aItems'       => $aItems
            ]
        );
    }
}

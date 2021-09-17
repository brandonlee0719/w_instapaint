<?php

namespace Apps\Instapaint\Controller;


use Phpfox;

//controllers must be a child of \Phpfox_Component class.

class AdminPaintersApprovedController extends \Phpfox_Component
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
        $template->setTitle('Admin Dashboard » Approved Painters');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        // Set breadcrumb
        $template->setBreadCrumb("Approved Painters");

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getAdminDashboardMenu(), // Menus array
            'Painters', // Reference menu name
            ['Approved Painters' => 'admin-dashboard.painters.approved'] // Menu to be inserted
        ));

        $search->set([
            'type'           => 'approved painters',
            'field'          => 'ar.approval_request_id',
            'ignore_blocked' => true,
            'search_tool'    => [
                'when_field' => 'approved_timestamp',
                'when_end_field' => 'approved_timestamp',
                'table_alias' => 'ar',
                'search'      => [
                    'action'        => $this->url()->makeUrl('/admin-dashboard/painters/approved/'),
                    'default_value' => 'Search Approved Painters...',
                    'name'          => 'search',
                    'field'         => ['u.full_name', 'u.user_name'],
                ],
                'sort'        => [
                    'latest-approvals'     => ['ar.approved_timestamp', _p('Latest Approvals')],
                    'latest-logins' => ['u.last_login', _p('Latest Logins')],
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
        $search->setCondition('AND is_approved = 1');

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
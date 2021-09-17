<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class AdminPaintersUnapprovedController extends \Phpfox_Component
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
        $template->setTitle('Admin Dashboard » Unapproved Painters');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        // Set breadcrumb
        $template->setBreadCrumb("Unapproved Painters");

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getAdminDashboardMenu(), // Menus array
            'Painters', // Reference menu name
            ['Unapproved Painters' => 'admin-dashboard.painters.unapproved'] // Menu to be inserted
        ));

        $search->set([
            'type'           => 'unapproved painters',
            'field'          => 'user.user_id',
            'ignore_blocked' => true,
            'search_tool'    => [
                'when_field' => 'joined',
                'when_end_field' => 'joined',
                'table_alias' => 'user',
                'search'      => [
                    'action'        => $this->url()->makeUrl('/admin-dashboard/painters/unapproved/'),
                    'default_value' => 'Search Unapproved Painters...',
                    'name'          => 'search',
                    'field'         => ['user.full_name', 'user.user_name'],
                ],
                'sort'        => [
                    'latest-registrations'     => ['user.joined', _p('Latest Sign-ups')],
                    'latest-logins' => ['user.last_login', _p('Latest Logins')],
                ],
                'show'        => [20, 40, 60],
            ],
        ]);

        // Configure search service
        $aBrowseParams = [
            'module_id' => 'instapaint-browse-users',
            'alias'     => 'user',
            'field'     => 'user_id',
            'table'     => Phpfox::getT('user')
        ];

        // Filter only approved requests
        $search->setCondition('AND user.user_group_id = 7');

        $search->setContinueSearch(false);
        $search->browse()->params($aBrowseParams)->execute();

        $aItems = $this->search()->browse()->getRows();

        // Set last_login equal to joined if last_login is 0:
        foreach ($aItems as $key => $value) {
            if ($value['last_login'] == 0) {
                $aItems[$key]['last_login'] = $value['joined'];
            }
        }

        // assign variables to template
        $this->template()->assign([
                'aItems'       => $aItems,
                'page' => $search->getPage()
            ]
        );
    }
}

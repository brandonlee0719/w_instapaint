<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPaintersSettingsController extends \Phpfox_Component
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

        //Get instance of Admin service class
        $adminService = \Phpfox::getService('instapaint.admin');

        // We expect this to be an integer because the route handles this path:
        $painterId = $this->request()->getInt('req4');

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Admin Dashboard » Global Painter Settings');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Global Painter Settings');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
            $instapaintService->getAdminDashboardMenu(), // Menus array
            'Painters', // Reference menu name
            ['Global Painter Settings' => 'admin-dashboard.painters.settings'] // Menu to be inserted
        ));

        $painter = $adminService->getApprovedPainter($painterId);

        $template->assign([
            'painter' => $painter,
            'token' => $securityService->getCSRFToken(),
            'defaultDailyJobsLimit' => $adminService->getDefaultPainterDailyJobsLimit()
        ]);

        // The form values
        $vals = $_POST['val'];

        if ($vals) { // Form was sent

            // If security token is not valid, show CSRF error message:
            if (!$securityService->checkCSRFToken($vals['token'])) {
                $this->template()->assign([
                    'csrfError' => true
                ]);
                return;
            }

            if ((int) $vals['daily_limit'] < 0) {
                \Phpfox_Error::set(_p('Daily limit is required'));
            }


            if (\Phpfox_Error::isPassed()) {

                if ($vals['delete_custom_daily_jobs_limits']) {
                    $adminService->deleteAllPainterDailyJobsLimits();
                }

                $adminService->setPainterDailyJobsLimit(0, (int) $vals['daily_limit']);
                $this->url()->send('/admin-dashboard/painters/',null,'Your changes were saved!');
            }

            // Pass sent fields to template so user doesn't have to re-enter them:
            $this->template()->assign([
                'val' => $vals
            ]);
        }

    }
}

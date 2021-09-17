<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminSettingsController extends \Phpfox_Component
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

        //Get instance of Packages service class
        $packagesService = \Phpfox::getService('instapaint.packages');

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Admin Dashboard » Global Settings');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('InstaPaint Settings');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
            $instapaintService->getAdminDashboardMenu() // Menus array
        ));

        $template->assign([
            'token' => $securityService->getCSRFToken(),
            'defaultDailyJobsLimit' => $adminService->getDefaultPainterDailyJobsLimit(),
            'expeditedMinDays' => $adminService->getExpeditedMinDays(),
            'styles' => $packagesService->getStyles()
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

            if ((int) $vals['expedited_min_days'] < 0) {
                \Phpfox_Error::set(_p('Expedited min days is required'));
            }


            if (\Phpfox_Error::isPassed()) {

                if ($vals['delete_custom_daily_jobs_limits']) {
                    $adminService->deleteAllPainterDailyJobsLimits();
                }

                $adminService->setPainterDailyJobsLimit(0, (int) $vals['daily_limit']);
                $adminService->setExpeditedMinDays((int) $vals['expedited_min_days']);

                // Update style prices:
                $adminService->updateStylePrices($vals['style_prices']);

                $this->url()->send('/admin-dashboard/settings/',null,'Your changes were saved!');
            }

            // Pass sent fields to template so user doesn't have to re-enter them:
            $this->template()->assign([
                'val' => $vals
            ]);
        }

    }
}

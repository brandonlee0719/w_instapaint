<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPackagesFrameSizeAddController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::ADMIN_GROUP_ID]);

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Get instance of Packages service class
        $packagesService = \Phpfox::getService('instapaint.packages');

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

            if (empty($vals['name'])) {
                \Phpfox_Error::set(_p('Name is required'));
            }

            if (empty($vals['description'])) {
                \Phpfox_Error::set(_p('Description is required'));
            }

            if ($vals['price'] == '') {
                \Phpfox_Error::set(_p('Price is required'));
            }

            if (!is_numeric($vals['price'])) {
                \Phpfox_Error::set(_p('Price must be a numeric value'));
            }

            if (\Phpfox_Error::isPassed()) {
                $packagesService->addFrameSize($vals['name'], $vals['description'], $vals['price']);
                $this->url()->send('admin-dashboard/packages',null,'New frame size was successfully added');
            }
        } else {
            // Get phpFox core template service
            $template = $this->template();

            // Set title
            $template->setTitle('Admin Dashboard » Add Frame Size');

            // Font Awesome
            $instapaintService = \Phpfox::getService('instapaint');
            $template->setHeader([
                $instapaintService::FONT_AWESOME_LINK
            ]);

            // Set template heading
            $template->setBreadCrumb('Add Frame Size');

            // Build menu
            $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
                $instapaintService->getAdminDashboardMenu(), // Menus array
                'Packages', // Reference menu name
                ['Add Frame Size' => 'admin-dashboard.packages.frame-size.add'] // Menu to be inserted
            ));

            // Pass security token to template:
            $template->assign([
                'token' => $securityService->getCSRFToken()
            ]);
        }
    }
}

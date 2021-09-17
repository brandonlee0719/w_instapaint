<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPackagesAddController extends \Phpfox_Component
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

            if (empty($vals['frame_size_id'])) {
                \Phpfox_Error::set(_p('Frame size is required'));
            }

            if (empty($vals['frame_type_id'])) {
                \Phpfox_Error::set(_p('Frame type is required'));
            }

            if (empty($vals['shipping_type_id'])) {
                \Phpfox_Error::set(_p('Shipping type is required'));
            }

            if (\Phpfox_Error::isPassed()) {
                if ($packagesService->packageExists($vals['frame_size_id'], $vals['frame_type_id'], $vals['shipping_type_id'])) {
                    $this->url()->send('admin-dashboard/packages',null,'This package already exists');
                }
                $packagesService->addPackage($vals['frame_size_id'], $vals['frame_type_id'], $vals['shipping_type_id']);
                $this->url()->send('admin-dashboard/packages',null,'New package was successfully added');
            }
        } else {
            // Get phpFox core template service
            $template = $this->template();

            // Set title
            $template->setTitle('Admin Dashboard » Add Package');

            // Font Awesome
            $instapaintService = \Phpfox::getService('instapaint');
            $template->setHeader([
                $instapaintService::FONT_AWESOME_LINK
            ]);

            // Set template heading
            $template->setBreadCrumb('Add Package');

            // Build menu
            $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
                $instapaintService->getAdminDashboardMenu(), // Menus array
                'Packages', // Reference menu name
                ['Add Package' => 'admin-dashboard.packages.add'] // Menu to be inserted
            ));

            // Get frame sizes:
            $frameSizes = $packagesService->getFrameSizes();

            // Get frame types:
            $frameTypes = $packagesService->getFrameTypes();

            // Get shipping types:
            $shippingTypes = $packagesService->getShippingTypes();

            // Check if there is at least one of each (frame size, frame type, and shipping type):
            if (!$frameSizes || !$frameTypes || !$shippingTypes) {
                $template->assign([
                    'error' => 'In order to add a new package, there must be at least one frame size, frame type, and shipping type.'
                ]);
                return;
            }

            // Pass frame sizes, frame types, and shipping types
            // Pass security token to template:
            $template->assign([
                'token' => $securityService->getCSRFToken(),
                'frameSizes' => $frameSizes,
                'frameTypes' => $frameTypes,
                'shippingTypes' => $shippingTypes
            ]);
        }
    }
}

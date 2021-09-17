<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPackagesDeleteController extends \Phpfox_Component
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

        // We expect this to be an integer because the route handles this path:
        $packageId = $this->request()->getInt('req4');

        // The form values
        $vals = $_POST['val'];

        // If form has been sent
        if ($vals) { // Form was sent, edit and redirect

            if (!$securityService->checkCSRFToken($vals['token'])) {
                $this->template()->assign([
                    'csrfError' => true
                ]);
                return;
            }
            if (!empty($vals)) {
                // Validate fields:
                if (empty($packageId) || !is_numeric($packageId)) {
                    \Phpfox_Error::set(_p('A valid ID is required'));
                }

                if (\Phpfox_Error::isPassed()) {
                    $packagesService->deletePackageById($packageId);
                    $packagesService->deleteExclusiveDiscounts($packageId); // Modify related discounts to exclude this package
                    $packagesService->deletePackageDiscount($packageId); // Delete discounts exclusive to this package
                    $this->url()->send('admin-dashboard/packages',null,'Package was successfully deleted');
                }
            }
        } else { // Form not sent yet

            // Let's try to pull this frame size from the database
            $package = $packagesService->getPackageById($packageId);

            // Get phpFox core template service
            $template = $this->template();

            // Set title
            $template->setTitle('Admin Dashboard » Delete Package');

            // Font Awesome
            $instapaintService = \Phpfox::getService('instapaint');
            $template->setHeader([
                $instapaintService::FONT_AWESOME_LINK
            ]);

            // Set template heading
            $template->setBreadCrumb('Delete Package');

            // Build menu
            $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
                $instapaintService->getAdminDashboardMenu(), // Menus array
                'Packages', // Reference menu name
                ['Delete Package' => 'admin-dashboard.packages.delete.' . $packageId] // Menu to be inserted
            ));

            // If frame size was not found in DB, let's show an error message:
            if (!$package) {
                $template->assign([
                    'error' => 'The package you are trying to delete does not exist.'
                ]);
            } else { // We found frame size in DB; show populated form
                $template->assign([
                    'package' => $package,
                    'token' => $securityService->getCSRFToken()
                ]);
            }
        }
    }
}

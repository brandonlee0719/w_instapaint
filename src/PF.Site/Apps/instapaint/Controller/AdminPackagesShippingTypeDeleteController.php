<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPackagesShippingTypeDeleteController extends \Phpfox_Component
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
        $shippingTypeId = $this->request()->getInt('req5');

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
                if (empty($shippingTypeId) || !is_numeric($shippingTypeId)) {
                    \Phpfox_Error::set(_p('A valid ID is required'));
                }

                if (\Phpfox_Error::isPassed()) {

                    // Make sure no package is using this frame size
                    if($packagesService->getPackagesBy('shipping_type_id', $shippingTypeId)) {
                        $this->template()->assign([
                            'error' => 'This shipping type cannot be deleted because a package is using it.'
                        ]);
                        return;
                    }

                    $packagesService->deleteShippingTypeById($shippingTypeId);
                    $this->url()->send('admin-dashboard/packages',null,'Shipping type was successfully deleted');
                }
            }
        } else { // Form not sent yet

            // Let's try to pull this shipping type from the database
            $shippingType = $packagesService->getShippingTypeById($shippingTypeId);

            // Get phpFox core template service
            $template = $this->template();

            // Set title
            $template->setTitle('Admin Dashboard » Delete Shipping Type');

            // Font Awesome
            $instapaintService = \Phpfox::getService('instapaint');
            $template->setHeader([
                $instapaintService::FONT_AWESOME_LINK
            ]);

            // Set template heading
            $template->setBreadCrumb('Delete Shipping Type');

            // Build menu
            $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
                $instapaintService->getAdminDashboardMenu(), // Menus array
                'Packages', // Reference menu name
                ['Delete Shipping Type' => 'admin-dashboard.packages.shipping-type.delete.' . $shippingTypeId] // Menu to be inserted
            ));

            // If shipping type was not found in DB, let's show an error message:
            if (!$shippingType) {
                $template->assign([
                    'error' => 'The shipping type you are trying to delete does not exist.'
                ]);
            } else { // We found shipping type in DB; show populated form
                $template->assign([
                    'shippingType' => $shippingType,
                    'token' => $securityService->getCSRFToken()
                ]);
            }
        }
    }
}

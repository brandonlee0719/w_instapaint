<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPackagesShippingTypeEditController extends \Phpfox_Component
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

            // If security token is not valid, show CSRF error message:
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
                    $packagesService->updateShippingType($shippingTypeId, $vals['name'], $vals['description'], $vals['price']);
                    $this->url()->send('admin-dashboard/packages',null,'Shipping type was successfully updated');
                }
            }
        } else { // Form not sent yet

            // Let's try to pull this shipping type from the database
            $shippingType = $packagesService->getShippingTypeById($shippingTypeId);

            // Get phpFox core template service
            $template = $this->template();

            // Set title
            $template->setTitle('Admin Dashboard » Edit Shipping Type');

            // Font Awesome
            $instapaintService = \Phpfox::getService('instapaint');
            $template->setHeader([
                $instapaintService::FONT_AWESOME_LINK
            ]);

            // Set template heading
            $template->setBreadCrumb('Edit Shipping Type');

            // Build menu
            $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
                $instapaintService->getAdminDashboardMenu(), // Menus array
                'Packages', // Reference menu name
                ['Edit Shipping Type' => 'admin-dashboard.packages.shipping-type.edit.' . $shippingTypeId] // Menu to be inserted
            ));

            // If shipping type was not found in DB, let's show an error message:
            if (!$shippingType) {
                $template->assign([
                    'error' => 'The shipping type you are trying to edit does not exist.'
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

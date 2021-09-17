<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPackagesFrameTypeEditController extends \Phpfox_Component
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
        $frameTypeId = $this->request()->getInt('req5');

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
                if (empty($frameTypeId) || !is_numeric($frameTypeId)) {
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
                    $packagesService->updateFrameType($frameTypeId, $vals['name'], $vals['description'], $vals['price']);
                    $this->url()->send('admin-dashboard/packages',null,'Frame type was successfully updated');
                }
            }
        } else { // Form not sent yet

            // Let's try to pull this frame size from the database
            $frameType = $packagesService->getFrameTypeById($frameTypeId);

            // Get phpFox core template service
            $template = $this->template();

            // Set title
            $template->setTitle('Admin Dashboard » Edit Frame Type');

            // Font Awesome
            $instapaintService = \Phpfox::getService('instapaint');
            $template->setHeader([
                $instapaintService::FONT_AWESOME_LINK
            ]);

            // Set template heading
            $template->setBreadCrumb('Edit Frame Type');

            // Build menu
            $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
                $instapaintService->getAdminDashboardMenu(), // Menus array
                'Packages', // Reference menu name
                ['Edit Frame Type' => 'admin-dashboard.packages.frame-type.edit.' . $frameTypeId] // Menu to be inserted
            ));

            // If frame size was not found in DB, let's show an error message:
            if (!$frameType) {
                $template->assign([
                    'error' => 'The frame type you are trying to edit does not exist.'
                ]);
            } else { // We found frame size in DB; show populated form
                $template->assign([
                    'frameType' => $frameType,
                    'token' => $securityService->getCSRFToken()
                ]);
            }
        }
    }
}

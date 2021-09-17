<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class ClientAddressesDeleteController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::CLIENT_GROUP_ID]);

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Get instance of Packages service class
        $userService = \Phpfox::getService('instapaint.client');

        // We expect this to be an integer because the route handles this path:
        $addressId = $this->request()->getInt('req4');

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
                if (empty($addressId) || !is_numeric($addressId)) {
                    \Phpfox_Error::set(_p('A valid ID is required'));
                }

                if (\Phpfox_Error::isPassed()) {

                    // Make sure address belongs to user
                    if($userService->addressBelongsToUser(user()->id, $addressId)) {

                        $deleted = $userService->deleteAddress($addressId);

                        if ($deleted) {

                            // Set default address if this address is the only one:
                            $clientService = \Phpfox::getService('instapaint.client');
                            $clientAddresses = $clientService->getClientAddresses(user()->id);
                            if (count($clientAddresses) == 1) {
                                $clientService->setDefaultAddress(user()->id, $clientAddresses[0]['address_id']);
                            }

                            $this->url()->send('client-dashboard/addresses',null,'Address was deleted successfully');
                        } else {
                            $this->template()->assign([
                                'error' => 'There was an error deleting this address.'
                            ]);
                        }

                    } else {
                        $this->template()->assign([
                            'error' => 'There was an error deleting this address.'
                        ]);
                    }

                }
            }
        } else { // Form not sent yet

            // Let's try to pull this frame type from the database
            $address = $userService->addressBelongsToUser(user()->id, $addressId);

            // Get phpFox core template service
            $template = $this->template();

            // Set title
            $template->setTitle('Client Dashboard » Delete Address');

            // Font Awesome
            $instapaintService = \Phpfox::getService('instapaint');
            $template->setHeader([
                $instapaintService::FONT_AWESOME_LINK
            ]);

            // Set template heading
            $template->setBreadCrumb('Delete Address');

            // Build menu
            $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
                $instapaintService->getClientDashboardMenu(), // Menus array
                'My Addresses', // Reference menu name
                ['Delete Address' => 'client-dashboard.addresses.delete.' . $addressId] // Menu to be inserted
            ));

            // If frame type was not found in DB, let's show an error message:
            if (!$address) {
                $template->assign([
                    'error' => 'The address you are trying to delete does not exist.'
                ]);
            } else { // We found frame type in DB; show populated form
                $template->assign([
                    'token' => $securityService->getCSRFToken(),
                    'address' => $address
                ]);
            }
        }
    }
}

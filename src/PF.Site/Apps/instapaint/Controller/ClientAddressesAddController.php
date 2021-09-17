<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientAddressesAddController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'client' => Phpfox::getService('instapaint.client')
        ];

        // Allow access to painters only
        $services['security']->allowAccess([
            $services['security']::CLIENT_GROUP_ID
        ]);

        // The form values
        $vals = $_POST['val'];

        if ($vals) { // Form was sent

            // If security token is not valid, show CSRF error message:
            if (!$services['security']->checkCSRFToken($vals['token'])) {
                $this->template()->assign([
                    'csrfError' => true
                ]);
                return;
            }

            if (empty($vals['country_iso'])) {
                \Phpfox_Error::set(_p('Country is required'));
            }


            if (empty($vals['full_name'])) {
                \Phpfox_Error::set(_p('Full name is required'));
            }

            if (empty($vals['street_address'])) {
                \Phpfox_Error::set(_p('Street address is required'));
            }

            if (empty($vals['city'])) {
                \Phpfox_Error::set(_p('City is required'));
            }

            if (empty($vals['state_province_region'])) {
                \Phpfox_Error::set(_p('State, region, or province is required'));
            }

            if (empty($vals['zip_code'])) {
                \Phpfox_Error::set(_p('Zip code is required'));
            }

            if (empty($vals['phone_number'])) {
                \Phpfox_Error::set(_p('Phone number is required'));
            }

            if (\Phpfox_Error::isPassed()) {
                $addedAddress = $services['client']->addAddress($vals);

                if ($addedAddress) {

                    // Set default address if this address is the only one:
                    $clientAddresses = $services['client']->getClientAddresses(user()->id);
                    if (count($clientAddresses) == 1) {
                        $services['client']->setDefaultAddress(user()->id, $clientAddresses[0]['address_id']);
                    }

                    $this->url()->send('client-dashboard/addresses',null,'New address was added successfully');
                } else {
                    $this->url()->send('client-dashboard/addresses',null,'There was an error adding address', null, 'danger');
                }
            }

            // Pass sent fields to template so user doesn't have to re-enter them:
            $this->template()->assign([
                'val' => $vals
            ]);
        }

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » Add Address');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Add Address');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getClientDashboardMenu(), // Menus array
            'My Addresses', // Reference menu name
            ['Add Address' => 'client-dashboard.addresses.add'] // Menu to be inserted
        ));

        // Get country list:
        $countries = $services['client']->getCountries();

        // Pass security token to template:
        $template->assign([
            'token' => $services['security']->getCSRFToken(),
            'countries' => $countries
        ]);

    }
}

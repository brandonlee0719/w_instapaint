<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientAddressesEditController extends \Phpfox_Component
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

        // We expect this to be an integer because the route handles this path:
        $addressId = $this->request()->getInt('req4');

        // Check that address belongs to user
        if (!$services['client']->addressBelongsToUser(user()->id, $addressId)) {
            $this->template()->assign([
                'error' => 'The address you are trying to delete does not exist.'
            ]);
            return;
        }

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

                // Check that address belongs to user
                if (!$services['client']->addressBelongsToUser(user()->id, $addressId)) {
                    $this->template()->assign([
                        'error' => 'The address you are trying to delete does not exist.'
                    ]);
                    return;
                } else {

                    $updatedAddress = $services['client']->updateAddress($addressId, $vals);

                    if ($updatedAddress) {
                        $this->url()->send('client-dashboard/addresses',null,'Address was updated successfully');
                    } else {
                        $this->url()->send('client-dashboard/addresses',null,'There was an error updating address', null, 'danger');
                    }
                }

            }

            // Pass sent fields to template so user doesn't have to re-enter them:
            $this->template()->assign([
                'val' => $vals
            ]);
        }

        // Get phpFox core template service
        $template = $this->template();

        $address = $services['client']->addressBelongsToUser(user()->id, $addressId);

        if (!$address) {
            $template->assign([
                'error' => 'The address you are trying to edit does not exist.'
            ]);
            return;
        } else {
            $template->assign([
                'val' => $address
            ]);
        }

        // Set view title
        $template->setTitle('Client Dashboard » Edit Address');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Edit Address');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getClientDashboardMenu(), // Menus array
            'My Addresses', // Reference menu name
            ['Edit Address' => 'client-dashboard.addresses.edit.' . $addressId] // Menu to be inserted
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

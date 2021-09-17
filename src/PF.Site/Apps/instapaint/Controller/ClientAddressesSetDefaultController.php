<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientAddressesSetDefaultController extends \Phpfox_Component
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

            // Check that address belongs to user
            if (!$services['client']->addressBelongsToUser(user()->id, $vals['address_id'])) {
                $this->template()->assign([
                    'error' => 'The address you are trying to delete does not exist.'
                ]);
                return;
            }

            if (\Phpfox_Error::isPassed()) {

                $addressWasSet = $services['client']->setDefaultAddress(user()->id,  $vals['address_id']);

                if ($addressWasSet) {
                    $this->url()->send('client-dashboard/addresses',null,'Default address was set successfully');
                } else {
                    $this->url()->send('client-dashboard/addresses',null,'There was an error setting detault address', null, 'danger');
                }

            }
        } else {
            $this->url()->send('client-dashboard/addresses');
        }

    }
}

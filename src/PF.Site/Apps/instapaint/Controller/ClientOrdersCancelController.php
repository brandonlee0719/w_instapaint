<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientOrdersCancelController extends \Phpfox_Component
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

            if (empty($vals['order_id'])) {
                \Phpfox_Error::set(_p('Order Id is required'));
            }

            if (\Phpfox_Error::isPassed()) {

                // Confirm order belongs to user:
                $order = $services['client']->getOrder(user()->id, $vals['order_id']);

                if ($order // Order belongs to user
                    && $order['order_status_id'] == 1 // Order status is pending payment
                    && $services['client']->cancelOrder(user()->id, $vals['order_id'])) { // Order was cancelled

                    // Redirect:
                    $this->url()->send('client-dashboard/orders',null,'The order was cancelled successfully');
                } else {
                    $this->url()->send('client-dashboard/orders',null,'There was an error cancelling the order', null, 'danger');
                }
            }

            // Pass sent fields to template so user doesn't have to re-enter them:
            $this->template()->assign([
                'val' => $vals
            ]);
        } else {
            $this->url()->send('client-dashboard/orders');
        }

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » Cancel Order');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Cancel Order');

        // Pass security token to template:
        $template->assign([
            'token' => $services['security']->getCSRFToken()
        ]);

    }
}

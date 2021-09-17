<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Core\Request\Exception;
use Phpfox;

class ClientThankYouController extends \Phpfox_Component
{
    public function process()
    {

        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'client' => Phpfox::getService('instapaint.client'),
        ];

        // Allow access to clients only
        $services['security']->allowAccess([
            $services['security']::CLIENT_GROUP_ID
        ]);

        // Get order ID from URL:
        $orderId = $this->request()->getInt('req3');

        // Order must exist, belong to this user, and have a paid (open) status:
        $order = $services['client']->getOrder(user()->id, $orderId);

        if ($order == false || (int) $order['order_status_id'] != 2) {

            // This order does not belong to user, does not exist, or isn't paid
            url()->send('/client-dashboard/orders/');
        }

        $order = $services['client']->getOrder(user()->id, $orderId);

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Thank you for your order');

        // Font Awesome
        $template->setHeader([
            $services['instapaint']::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Thank you for your order!');

        $template->assign([
            'orderId' => $orderId,
            'order' => $order
        ]);

    }
}

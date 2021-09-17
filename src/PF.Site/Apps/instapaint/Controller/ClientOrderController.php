<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientOrderController extends \Phpfox_Component
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
        $orderId = $this->request()->getInt('req3');

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » Order Details');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Order Details');


        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getClientDashboardMenu(), // Menus array
            'My Orders', // Reference menu name
            ['Order Details' => 'client-dashboard.order.' . $orderId] // Menu to be inserted
        ));

        $order = $services['client']->getOrder(user()->id, $orderId);

        $template->assign([
            'order' => $order,
            'token' => $services['security']->getCSRFToken(),
        ]);

        // Handle payment success message:

        if (isset($_GET['payment_success']) && (int) $order['order_status_id'] != 1 && (int) $order['order_status_id'] != 3) {
            $template->assign([
                'payment_success' => true
            ]);
        }
    }
}

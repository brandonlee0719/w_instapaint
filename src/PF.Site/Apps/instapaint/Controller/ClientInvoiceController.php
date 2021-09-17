<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientInvoiceController extends \Phpfox_Component
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
        $template->setTitle('Client Dashboard » Invoice');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        //$template->setBreadCrumb('Invoice');


        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getClientDashboardMenu(), // Menus array
            'My Orders', // Reference menu name
            ['Invoice' => 'client-dashboard.invoice.' . $orderId] // Menu to be inserted
        ));


        $template->assign([
            'order' => $services['client']->getOrder(user()->id, $orderId),
            'token' => $services['security']->getCSRFToken()
        ]);

    }
}

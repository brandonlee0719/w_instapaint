<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientOrdersController extends \Phpfox_Component
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
            'events' => Phpfox::getService('instapaint.events')
        ];

        // Allow access to painters only
        $services['security']->allowAccess([
            $services['security']::CLIENT_GROUP_ID
        ]);

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » My Orders');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('My Orders');

        $template->buildSectionMenu('client-dashboard', $services['instapaint']->getClientDashboardMenu());

        // Button
        $template->menu('Add new order', $this->url()->makeUrl('/client-dashboard/orders/add'));

        $template->assign([
            'orders' => $services['client']->getOrders(user()->id)
        ]);

        // Check if client has partial order:
        $packagesService = \Phpfox::getService('instapaint.packages');

        $partialOrder = $packagesService->getPartialOrderByUser(user()->id);

        if ($partialOrder) {

            url()->send('/first-order-complete/');

        }
    }
}

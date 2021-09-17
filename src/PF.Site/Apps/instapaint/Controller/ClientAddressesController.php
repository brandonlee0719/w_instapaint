<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientAddressesController extends \Phpfox_Component
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

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » My Addresses');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('My Addresses');

        $template->buildSectionMenu('client-dashboard', $services['instapaint']->getClientDashboardMenu());

        // Button
        $template->menu('Add new address', $this->url()->makeUrl('/client-dashboard/addresses/add'));

        $template->assign([
            'addresses' => $services['client']->getClientAddresses(user()->id),
            'token' => $services['security']->getCSRFToken()
        ]);

        // Check if client has partial order:
        $packagesService = \Phpfox::getService('instapaint.packages');

        $partialOrder = $packagesService->getPartialOrderByUser(user()->id);

        if ($partialOrder) {
            url()->send('/first-order-complete/');
        }
    }
}

<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class ClientDashboardController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Get instance of Instapaint Client service class
        $clientService = \Phpfox::getService('instapaint.client');

        // Allow access to painters only
        $securityService->allowAccess([
            $securityService::CLIENT_GROUP_ID
        ]);

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » General');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Client Dashboard');

        $template->buildSectionMenu('client-dashboard', $instapaintService->getClientDashboardMenu());

        $totalOrders = $clientService->countOrders(user()->id);
        $pendingPaymentOrders = $clientService->countPendingPaymentOrders(user()->id);
        $openOrders = $clientService->countOpenOrders(user()->id);
        $completedOrders = $clientService->countCompletedOrders(user()->id);

        $template->assign([
            'total_orders' => $totalOrders,
            'pending_payment_orders' => $pendingPaymentOrders,
            'open_orders' => $openOrders,
            'completed_orders' => $completedOrders
        ]);

        // Check if client has partial order:

        $packagesService = \Phpfox::getService('instapaint.packages');

        $partialOrder = $packagesService->getPartialOrderByUser(user()->id);

        if ($partialOrder) {

            url()->send('/first-order-complete/');

            $package = $packagesService->getPackageDetailed($partialOrder['package_id']);

            $template->assign([
                'partial_order' => $partialOrder,
                'package' => $package
            ]);
        }
    }
}

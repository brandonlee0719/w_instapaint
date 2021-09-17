<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class PainterTakeOrderController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'painter' => Phpfox::getService('instapaint.painter')
        ];

        // Allow access to painters only
        $services['security']->allowAccess([
            $services['security']::APPROVED_PAINTER_GROUP_ID
        ]);

        // We expect this to be an integer because the route handles this path:
        $orderId = $this->request()->getInt('req3');

        $takenOrdersLastDay = $services['painter']->getTakenOrdersSince(user()->id, '1 day ago');
        $maxDailyOrders = $services['painter']->getMaxDailyOrders();

        if ($takenOrdersLastDay >= $maxDailyOrders) {
            $this->url()->send('/painter-dashboard/available-orders/', null, 'Daily limit reached, try again later', null, 'danger');
        }

        if ($services['painter']->orderIsTaken($orderId)) {
            // Order is already taken
            $this->url()->send('/painter-dashboard/orders/', null, 'Order was already taken', null, 'danger');
        } else {
            // Check that order is available:
            if ($services['painter']->orderIsAvailable($orderId)) {
                // Take this order:
                $services['painter']->takeOrder(user()->id, $orderId);
                $this->url()->send('/painter-dashboard/', null, 'Congratulations! You took the order!', null, 'success');
            } else {
                // This order is not open or does not exist
                $this->url()->send('/painter-dashboard/orders/', null, 'Order not available', null, 'danger');
            }
        }

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Painter Dashboard » Available Orders');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Available Orders');

        $template->buildSectionMenu('painter-dashboard', $services['instapaint']->getPainterDashboardMenu());

        $template->assign([
            'orders' => $services['painter']->getAvailableOrders()
        ]);
    }
}

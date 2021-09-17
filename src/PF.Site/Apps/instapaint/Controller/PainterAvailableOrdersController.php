<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class PainterAvailableOrdersController extends \Phpfox_Component
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

        $takenOrdersLastDay = $services['painter']->getTakenOrdersSince(user()->id, '1 day ago');
        $maxDailyOrders = $services['painter']->getMaxDailyOrders();

        if ($takenOrdersLastDay >= $maxDailyOrders) {
            $template->assign([
                'orderLimitReached' => true,
                'maxDailyOrders' => $maxDailyOrders
            ]);
        } else {
            $template->assign([
                'orders' => $services['painter']->getAvailableOrders()
            ]);
        }
    }
}

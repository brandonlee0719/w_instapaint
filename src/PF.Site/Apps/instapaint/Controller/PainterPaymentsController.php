<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class PainterPaymentsController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Allow access to painters only
        $securityService->allowAccess([
            $securityService::APPROVED_PAINTER_GROUP_ID
        ]);

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Painter Dashboard » Payments');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('My Payments');

        $template->buildSectionMenu('painter-dashboard', $instapaintService->getPainterDashboardMenu());
    }
}

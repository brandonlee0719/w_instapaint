<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class PainterDashboardController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        // Allow access to painters only
        $securityService->allowAccess([
            $securityService::PAINTER_GROUP_ID,
            $securityService::APPROVED_PAINTER_GROUP_ID
        ]);

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Get instance of Painter service class
        $painterService = \Phpfox::getService('instapaint.painter');

        $painterIsApproved = user()->group->id == $securityService::APPROVED_PAINTER_GROUP_ID;

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Painter Dashboard » General');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        if ($painterIsApproved) { // Painter is approved:
            $template->setBreadCrumb('Painter Dashboard');
            $template->buildSectionMenu('painter-dashboard', $instapaintService->getPainterDashboardMenu());
            $template->assign([
                'painterIsApproved' => true,
                'countTakenOrders' => $painterService->countTakenOrders(user()->id),
                'countOrdersSentForApproval' => $painterService->countOrdersSentForApproval(user()->id),
                'countOrdersApprovedForShipping' => $painterService->countOrdersApprovedForShipping(user()->id),
                'countOpenOrders' => $painterService->countOrdersPainting(user()->id),
                'countShippedOrders' => $painterService->countShippedOrders(user()->id),
                'countAvailableOrders' => $painterService->countAvailableOrders()
            ]);
        } else { // Painter is not approved:

            $links =[
                'profile' => user()->url,
                'profileInfo' => '/user/profile/',
                'addWorkSamples' => '/photo/add/'
            ];

            // Check if painter has requested approval:
            $painterRequestedApproval = $painterService->hasRequestedApproval();

            if ($painterRequestedApproval) { // Painter has requested approval:
                $template->assign([
                    'painterRequestedApproval' => true,
                    'links' => $links
                ]);
            } else {
                $template->assign([
                    'token' => $securityService->getCSRFToken(),
                    'links' => $links
                ]);
            }
        }
    }
}

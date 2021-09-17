<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPackagesController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Get instance of Packages service class
        $packagesService = \Phpfox::getService('instapaint.packages');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::ADMIN_GROUP_ID]);

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Admin Dashboard » Packages');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Packages');

        $template->buildSectionMenu('admin-dashboard', $instapaintService->getAdminDashboardMenu());


        $template->menu('New frame size', 'admin-dashboard.packages.frame-size.add');
        $template->menu('New frame type', 'admin-dashboard.packages.frame-type.add');
        $template->menu('New shipping type', 'admin-dashboard.packages.shipping-type.add');
        $template->menu('New package', 'admin-dashboard.packages.add');

        // Assign variables to template
        $this->template()->assign([
                'frameSizes' => $packagesService->getFrameSizes(),
                'frameTypes' => $packagesService->getFrameTypes(),
                'shippingTypes' => $packagesService->getShippingTypes(),
                'packages' => $packagesService->getPackages()
            ]
        );
    }
}

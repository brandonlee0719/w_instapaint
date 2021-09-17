<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminDiscountsAddController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::ADMIN_GROUP_ID]);

        //Get instance of Instapaint service class
        $instapaintService = \Phpfox::getService('instapaint');

        // Get instance of Packages service class
        $packagesService = \Phpfox::getService('instapaint.packages');

        $packages = $packagesService->getPackages();

        $this->template()->assign([
            'packages' => $packages
        ]);

        // The form values
        $vals = $_POST['val'];



        if ($vals) { // Form was sent

            // If security token is not valid, show CSRF error message:
            if (!$securityService->checkCSRFToken($vals['token'])) {
                $this->template()->assign([
                    'csrfError' => true
                ]);
                return;
            }

            if (empty($vals['name'])) {
                \Phpfox_Error::set(_p('Name is required'));
            }

            if (empty($vals['amount'])) {
                \Phpfox_Error::set(_p('Amount is required'));
            }

            // If expiration date is not empty, make sure it's a future date:
            if (!empty($vals['expiration'])) {
                $dateTimestamp = strtotime($vals['expiration']);
                if ($dateTimestamp < time()) {
                    \Phpfox_Error::set(_p('Expiration date must be a future date'));
                }
            }

            if (\Phpfox_Error::isPassed()) {
                $addedDiscount = $packagesService->addDiscount(
                    $vals['name'],
                    $vals['packages'],
                    $vals['code'],
                    $vals['amount'],
                    strtotime($vals['expiration']),
                    $vals['is_global_discount']
                );

                if ($addedDiscount) {
                    $this->url()->send('admin-dashboard/discounts',null,'New discount was added successfully');
                } else {
                    $this->url()->send('admin-dashboard/discounts',null,'There was an error adding discount', null, 'danger');
                }
            }

            // Pass sent fields to template so user doesn't have to re-enter them:
            $this->template()->assign([
                'val' => $vals
            ]);
        }

        // Get phpFox core template service
        $template = $this->template();

        // Set title
        $template->setTitle('Admin Dashboard » Add Discount');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        // Set template heading
        $template->setBreadCrumb('Add Discount');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $instapaintService->insertMenuAfter(
            $instapaintService->getAdminDashboardMenu(), // Menus array
            'Discounts', // Reference menu name
            ['Add Discount' => 'admin-dashboard.discounts.add'] // Menu to be inserted
        ));

        // Pass security token to template:
        $template->assign([
            'token' => $securityService->getCSRFToken()
        ]);

    }
}

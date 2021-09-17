<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminDiscountsDeleteController extends \Phpfox_Component
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

            if (empty($vals['discount_id'])) {
                \Phpfox_Error::set(_p('Discount Id is necessary'));
                if (!is_numeric($vals['discount_id'])) {
                    \Phpfox_Error::set(_p('Discount Id must be an integer'));
                }
            }

            if (\Phpfox_Error::isPassed()) {
                $deletedDiscount = $packagesService->deleteDiscountById($vals['discount_id']);

                if ($deletedDiscount) {
                    $this->url()->send('admin-dashboard/discounts',null,'Discount was deleted successfully');
                } else {
                    $this->url()->send('admin-dashboard/discounts',null,'There was an error deleting the discount', null, 'danger');
                }
            }
        } else {
            $this->url()->send('admin-dashboard/discounts');
        }
    }
}

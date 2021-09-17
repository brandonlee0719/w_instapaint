<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class AdminRejectOrderController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'painter' => Phpfox::getService('instapaint.painter'),
            'admin' => Phpfox::getService('instapaint.admin')
        ];

        // Allow access to painters only
        $services['security']->allowAccess([
            $services['security']::ADMIN_GROUP_ID
        ]);

        // We expect this to be an integer because the route handles this path:
        $orderId = $this->request()->getInt('req3');

        $order = $services['admin']->getOrderForApproval($orderId);

        if ($order && (int) $order['order_status_id'] == 2 && !$order['is_shipped']) {
            // Order status is open and it's approved and it's not shipped

            // The form values
            $vals = $_POST['val'];

            if ($vals) { // Form was sent

                // If security token is not valid, show CSRF error message:
                if (!$services['security']->checkCSRFToken($vals['token'])) {
                    $this->template()->assign([
                        'csrfError' => true
                    ]);
                    return;
                }

                if (empty(trim($vals['feedback']))) {
                    \Phpfox_Error::set(_p('Feedback is required'));
                }

                if (\Phpfox_Error::isPassed()) {
                    // Complete order:

                    $services['admin']->rejectOrder($orderId, $vals['feedback']);
                    $this->url()->send('/admin-dashboard/verify-orders/', null, 'The order was rejected!', null, 'success');
                }

                // Pass sent fields to template so user doesn't have to re-enter them:
                $this->template()->assign([
                    'val' => $vals
                ]);
            } else {
                // Form was not sent
            }
        } else {

            // Painter can't complete this order
            $this->url()->send('/admin-dashboard/', null, 'The order could not be rejected', null, 'danger');
        }


        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Admin Dashboard » Reject Order');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Reject Order');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getAdminDashboardMenu(), // Menus array
            'Orders', // Reference menu name
            ['Reject Order' => 'admin-dashboard.reject-order.' . $orderId] // Menu to be inserted
        ));

        $template->assign([
            'order' => $order,
            'token' => $services['security']->getCSRFToken()
        ]);
    }
}

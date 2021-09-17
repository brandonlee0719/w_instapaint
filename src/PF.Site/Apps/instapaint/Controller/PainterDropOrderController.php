<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class PainterDropOrderController extends \Phpfox_Component
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

        $orderTakenByUser = $services['painter']->getOrderTakenByPainter(user()->id, $orderId);

        if ($orderTakenByUser && (int) $orderTakenByUser['order_status_id'] == 2 && !$orderTakenByUser['is_approved'] && (!$orderTakenByUser['order_approval_request_id'] || $orderTakenByUser['is_denied'])) {
            // Order is taken by user and its status is open

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


                if (empty(trim($vals['drop_reason']))) {
                    \Phpfox_Error::set(_p('A cancellation reason must be provided'));
                }

                if (\Phpfox_Error::isPassed()) {
                    $services['painter']->dropOrder(user()->id, $orderId, $vals['drop_reason']);
                    $this->url()->send('/painter-dashboard/orders/', null, 'The order was cancelled successfully', null, 'success');
                }

                // Pass sent fields to template so user doesn't have to re-enter them:
                $this->template()->assign([
                    'val' => $vals
                ]);
            } else {
                // Form was not sent
            }

        } else {
            // Painter can't cancel this order
            $this->url()->send('/painter-dashboard/orders/', null, 'Order could not be canceled', null, 'danger');
        }

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Painter Dashboard » Cancel Order');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Confirm Order Cancellation');

        // Form is not sent
        $template->assign([
            'token' => $services['security']->getCSRFToken()
        ]);
    }
}

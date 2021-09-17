<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class PainterCompleteOrderController extends \Phpfox_Component
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

                if (empty($vals['temp_file'])) {
                    \Phpfox_Error::set(_p('Photo is required'));
                }

                if (\Phpfox_Error::isPassed()) {
                    // Complete order:

                    $tempFile = Phpfox::getService('core.temp-file')->get($vals['temp_file']);
                    $services['painter']->completeOrder(user()->id, $orderId, $tempFile['path']);

                    //Remove this temporary row in `phpfox_temp_file` table
                    Phpfox::getService('core.temp-file')->delete($vals['temp_file']);

                    $this->url()->send('/painter-dashboard/', null, 'The order was sent for approval', null, 'success');
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
            $this->url()->send('/painter-dashboard/orders/', null, 'The order could not be completed', null, 'danger');
        }


        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Painter Dashboard » Complete Order');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Complete Order');

        // Build menu
        $template->buildSectionMenu('painter-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getPainterDashboardMenu(), // Menus array
            'My Orders', // Reference menu name
            ['Complete Order' => 'painter-dashboard.complete-order.' . $orderId] // Menu to be inserted
        ));

        $template->assign([
            'order' => $services['painter']->getOrder($orderId),
            'token' => $services['security']->getCSRFToken()
        ]);

    }
}

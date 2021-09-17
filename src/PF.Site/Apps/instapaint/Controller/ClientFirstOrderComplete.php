<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientFirstOrderComplete extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'client' => Phpfox::getService('instapaint.client'),
            'packages' => Phpfox::getService('instapaint.packages'),
        ];

        // Allow access to painters only
        $services['security']->allowAccess([
            $services['security']::CLIENT_GROUP_ID
        ]);

        // If this is an AJAX request, return stats JSON:
        if ($_SERVER['QUERY_STRING'] == 'ajax=packages') {
            header('Content-Type: application/json');
            echo json_encode($services['packages']->getPackagesForForm());
            exit();
        }

        // If this is an AJAX request, return sale JSON:
        if ($_GET['ajax'] == 'sale' && is_int((int) $_GET['package_id'])) {
            header('Content-Type: application/json');
            echo json_encode($services['packages']->getSaleForPackage((int) $_GET['package_id']));
            exit();
        }

        // If this is an AJAX request, return coupon JSON:
        if ($_GET['ajax'] == 'coupon' && is_int((int) $_GET['package_id']) && !empty($_GET['coupon'])) {
            header('Content-Type: application/json');
            echo json_encode($services['packages']->validateCoupon($_GET['coupon'], $_GET['package_id']));
            exit();
        }

        // Handle order cancellation:
        if (isset($_GET['cancel']) && $_GET['cancel'] == 'true') {
            $services['packages']->clearPartialOrder(user()->id);
        }

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

            if (empty($vals['shipping_address'])) {
                \Phpfox_Error::set(_p('Shipping address is required'));
            }

            if (\Phpfox_Error::isPassed()) {

                $partialOrder = $services['packages']->getPartialOrderByUser(user()->id);


                // If there's a coupon, validate it, otherwise try to get a sale discount:
                if (!empty($vals['coupon_code'])) {
                    $discount = $services['packages']->validateCoupon($vals['coupon_code'], $partialOrder['package_id']);

                    // If coupon is not valid, try to get sale:
                    if (!$discount) {
                        $discount = $services['packages']->getSaleForPackage($partialOrder['package_id']);
                    }
                } else {
                    $discount = $services['packages']->getSaleForPackage($partialOrder['package_id']);
                }

                // Make sure this address belongs to this user:
                $address = $services['client']->addressBelongsToUser(user()->id, $vals['shipping_address']);

                if ($address) {
                    $oderDetails = [
                        'package' => $services['packages']->getPackageDetailed($partialOrder['package_id'])[0],
                        'discount' => $discount,
                        'shipping_address' => $address,
                        'style' => $services['packages']->getStyleInfo($partialOrder['style'])
                    ];

                    // Process photo path:
                    $photoExtension = str_replace($partialOrder['unique_id'], '', $partialOrder['photo_path']);
                    $photoPath = 'partial_orders/' . $partialOrder['unique_id'] . '/' . $partialOrder['unique_id'] . '%s' . $photoExtension;

                    $addedOrder = $services['packages']->addOrder(user()->id, $vals['shipping_address'], $partialOrder['package_id'], json_encode($oderDetails), $photoPath, 0, $partialOrder['order_notes'], $partialOrder['is_expedited'], $partialOrder['faces'], $partialOrder['expedited_days'], $partialOrder['style']);

                }

                if ($addedOrder) {
                    $services['packages']->clearPartialOrder(user()->id);
                    $this->url()->send('client-dashboard/order/' . $addedOrder,null,'Your order was successfully added, proceed with your payment', null, 'success', false);
                } else {
                    $this->url()->send('client-dashboard/orders',null,'There was an error adding the order', null, 'danger');
                }
            }

            // Pass sent fields to template so user doesn't have to re-enter them:
            $this->template()->assign([
                'val' => $vals
            ]);
        }

        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » Complete your first order');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Complete your first order!');



        // Get frame sizes list:
        $frameSizes = $services['packages']->getFrameSizes();

        // Get frame types list:
        $frameTypes = $services['packages']->getFrameTypes();

        // Get shipping types list:
        $shippingTypes = $services['packages']->getShippingTypes();

        // Get addresses:
        $addresses = $services['client']->getClientAddresses(user()->id);

        // Pass security token to template:
        $template->assign([
            'token' => $services['security']->getCSRFToken(),
            'frameSizes' => $frameSizes,
            'frameTypes' => $frameTypes,
            'shippingTypes' => $shippingTypes,
            'addresses' => $addresses
        ]);

        // Check if client has partial order:

        $partialOrder = $services['packages']->getPartialOrderByUser(user()->id);

        if ($partialOrder) {
            $package = $services['packages']->getPackageDetailed($partialOrder['package_id']);

            // Faces info:
            $facesPrice = ((int) $partialOrder['faces'] == 0 ? 0.00 : $partialOrder['faces'] - 1) * 25.00;
            $faces = [
                'number' => (int) $partialOrder['faces'],
                'price' => $facesPrice,
                'price_formatted' => '$' . number_format($facesPrice, 2)
            ];

            $template->assign([
                'partial_order' => $partialOrder,
                'package' => $package[0],
                'total_price' => $partialOrder['is_expedited'] ? $package[0]['total_price'] + 80.00 : $package[0]['total_price'],
                'faces' => $faces,
                'style' => $services['packages']->getStyleInfo($partialOrder['style'])
            ]);
        } else {
            url()->send('/client-dashboard/');
        }

    }
}

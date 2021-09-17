<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientOrdersAddController extends \Phpfox_Component
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
            'settings' => Phpfox::getService('instapaint.settings')
        ];

        // Allow access to clients only
        $services['security']->allowAccess([
            $services['security']::CLIENT_GROUP_ID
        ]);

        $settings = $services['settings']->getSettings();

        $expeditedMinDate = date('Y-m-d', strtotime('+ ' . $settings['expedited_min_days'] . ' days'));

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

            if (empty($vals['frame_size'])) {
                \Phpfox_Error::set(_p('Frame size is required'));
            }

            if (empty($vals['frame_type'])) {
                \Phpfox_Error::set(_p('Frame type is required'));
            }

            if (empty($vals['shipping_type'])) {
                \Phpfox_Error::set(_p('Shipping type is required'));
            }

            if (empty($vals['temp_file'])) {
                \Phpfox_Error::set(_p('Photo is required'));
            }

            $expeditedDays = ceil ( ( strtotime($vals['expedited_date']) - time() ) / 60 / 60 / 24 );

            if (empty($vals['expedited_date']) || (int) $expeditedDays < $settings['expedited_min_days']) {
                \Phpfox_Error::set(_p('Please select a valid expedited service date'));
                $error = 'Please select a valid expedited service date';
            }

            if (!isset($vals['style']) || (int) $vals['style'] < 0 || (int) $vals['style'] > 13) {
                \Phpfox_Error::set(_p('Please select a valid style'));
                $error = 'Please select a valid style';
            }

            if (\Phpfox_Error::isPassed()) {

                // Get matching package id:
                $matchingPackage = $services['packages']->getMatchingPackage($vals['frame_size'], $vals['frame_type'] , $vals['shipping_type']);

                // If there's a coupon, validate it, otherwise try to get a sale discount:
                if (!empty($vals['coupon_code']) && $matchingPackage) {
                    $discount = $services['packages']->validateCoupon($vals['coupon_code'], $matchingPackage['package_id']);

                    // If coupon is not valid, try to get sale:
                    if (!$discount) {
                        $discount = $services['packages']->getSaleForPackage($matchingPackage['package_id']);
                    }
                } else {
                    $discount = $services['packages']->getSaleForPackage($matchingPackage['package_id']);
                }

                // Make sure this address belongs to this user:
                $address = $services['client']->addressBelongsToUser(user()->id, $vals['shipping_address']);

                if ($matchingPackage && $address) {
                    $oderDetails = [
                        'package' => $services['packages']->getPackageDetailed($matchingPackage['package_id'])[0],
                        'discount' => $discount,
                        'shipping_address' => $address
                    ];

                    $tempFile = Phpfox::getService('core.temp-file')->get($vals['temp_file']);
                    $addedOrder = $services['packages']->addOrder(user()->id, $vals['shipping_address'], $matchingPackage['package_id'], json_encode($oderDetails), $tempFile['path'], $tempFile['server_id'], $vals['notes'], $vals['expedited'], $vals['faces'], $expeditedDays, $vals['style']);
                    //Remove this temporary row in `phpfox_temp_file` table
                    Phpfox::getService('core.temp-file')->delete($vals['temp_file']);
                }

                if ($addedOrder) {
                    \Phpfox::getService('instapaint.events')->addEvent(user()->id,array("action"=>"partial_order_added"));                    
                    $this->url()->send('client-dashboard/order/' . $addedOrder . '/',null,'Your order was successfully added, proceed with your payment', null, 'success', false);
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
        $template->setTitle('Client Dashboard » Add New Order');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('New Order');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getClientDashboardMenu(), // Menus array
            'My Orders', // Reference menu name
            ['Add New Order' => 'client-dashboard.orders.add'] // Menu to be inserted
        ));

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
            'addresses' => $addresses,
            'packages' => json_encode($services['packages']->getPackagesForForm()),
            'settings' => $settings,
            'expeditedMinDate' => $expeditedMinDate
        ]);

    }
}

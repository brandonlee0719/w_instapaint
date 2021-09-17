<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Core\Request\Exception;
use Phpfox;

class ClientOrdersPayController extends \Phpfox_Component
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
            'events' => Phpfox::getService('instapaint.events')
        ];

        // Allow access to clients only
        $services['security']->allowAccess([
            $services['security']::CLIENT_GROUP_ID
        ]);

        // Get order ID from URL:
        $orderId = $this->request()->getInt('req4');

        // Order must exist, belong to this user, and have a pending payment status:
        $order = $services['client']->getOrder(user()->id, $orderId);

        if ($order == false || (int) $order['order_status_id'] != 1) {

            // This order does not belong to user, does not exist, or ist not pending payment
            url()->send('/client-dashboard/orders/');
        }

        $isLive = true;

        if ($isLive) {
            $clientId = 'AVvvtdVJRJV8JKAJwVhe7roYNXLOPHV2kPPG3PKTIk10ffcF6-HnerZ9hYhIcXYfH3DXI78AJJAErK7p';
            $clientSecret = 'EGTrKw2H_sovxqQ37Lmokep2_FUunIn2V4-TGn3a7ULrc6arnFAogf63KEryHl-PgixNRGV-BbbxVy12';
        } else {
            $clientId = 'AdiQmYyYCxbdjKkorvS8pfuMJ38I7A4hKUJc7QcGsTsRBg6eE4KkMfZUrLDtjcpDxBDD6tU2dBqKfqQf';
            $clientSecret = 'EM8YO27LcMmHTfCOlUSvWPuHVknqzLdqvZFqjrDsw1hxH18K7K_N6Fs7TZDFYA7L66-ZYMa-JvwjLu-4';
        }

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );

        if ($isLive) {
            $apiContext->setConfig(
                array(
                    'log.LogEnabled' => true,
                    'log.FileName' => 'PF.Base/file/log/PayPal.log',
                    'log.LogLevel' => 'FINE',
                    'mode' => 'live',
                )
            );
        }

        // If user approved payment via PayPal:
        if (isset($_GET['paymentId'], $_GET['token'], $_GET['PayerID'])) {
            $paymentId = $_GET['paymentId'];
            $payerId = $_GET['PayerID'];

            $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

            $execution = new \PayPal\Api\PaymentExecution();
            $execution->setPayerId($payerId);

            try {
                $result = $payment->execute($execution, $apiContext);
            } catch (Exception $e) {
                $this->url()->send('/client-dashboard/orders/',null,'There was an error with your payment, please try again.', null, 'danger');
            }

            $paymentMethod = $payment->getPayer()->getPaymentMethod();

            $paymentDetails = json_encode(['payment_id' => $paymentId, 'payment_method' => $paymentMethod]);

            $services['client']->payOrder($orderId, $paymentDetails);
            $services['events']->addEvent(user()->id, array("action"=>"order_completed"));

            \Phpfox::getService('notification.process')->add('instapaint_ClientOrderPayed', $orderId, user()->id, user()->id, true);
            $this->url()->send('/client-dashboard/thank-you/' . $orderId);
        }

        // Order exists, belongs to client, and is pending payment
        // Allow client to pay for this order

        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');

        $item = new \PayPal\Api\Item();
        $item->setName('Oil painting (' . $order['order_details']['package']['frame_size_name'] . ', ' . $order['order_details']['package']['frame_type_name'] . ')')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($order['price_with_discount']);

        $shippingAddress = new \PayPal\Api\ShippingAddress();
        //var_dump($order['order_details']['shipping_address']); exit();
        $shippingAddress->setCountryCode($order['order_details']['shipping_address']['country_iso'])
            ->setCity($order['order_details']['shipping_address']['city'])
            ->setLine1($order['order_details']['shipping_address']['street_address'])
            ->setPostalCode($order['order_details']['shipping_address']['zip_code'])
            ->setState($order['order_details']['shipping_address']['state_province_region']);

        $itemList = new \PayPal\Api\ItemList();
        $itemList->setItems([$item])
            ->setShippingAddress($shippingAddress);

        $amount = new \PayPal\Api\Amount();
        $amount->setTotal($order['price_with_discount'])
            ->setCurrency('USD');

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription('Instapaint Oil Painting')
            ->setInvoiceNumber($order['order_id']);

        $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';

        $returnUrl = $_SERVER['SERVER_NAME'] == 'localhost' ? "{$protocol}://{$_SERVER['SERVER_NAME']}/client-dashboard/orders/pay/{$orderId}" : "{$protocol}://{$_SERVER['HTTP_HOST']}/client-dashboard/orders/pay/$orderId";
        $cancelUrl = $_SERVER['SERVER_NAME'] == 'localhost' ? "{$protocol}://{$_SERVER['SERVER_NAME']}/client-dashboard/order/{$orderId}" : "{$protocol}://{$_SERVER['HTTP_HOST']}/client-dashboard/order/$orderId";

        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl);

        $payment = new \PayPal\Api\Payment();

        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        // After Step 3
        try {
            $payment->create($apiContext);
            url()->send($payment->getApprovalLink());
        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            // This will print the detailed information on the exception.
            //REALLY HELPFUL FOR DEBUGGING

            $exceptionData = json_decode($ex->getData());
            $errorName = $exceptionData->name;

            if ($errorName == 'VALIDATION_ERROR') {
                $errorMessage = 'These fields are invalid:';

                foreach ($exceptionData->details as $key => $detail) {
                    $separator = $key == 0 ? ' ' : ', ';
                    $errorMessage .= $separator . $detail->field;
                }
            } else {
                $errorMessage = "There was an error with your order, make sure your information is correct and try again.";
            }

            $this->url()->send('client-dashboard/order/' . $orderId,null,$errorMessage, null, 'danger', false);
            exit();
        }

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

            if (\Phpfox_Error::isPassed()) {

                // Get matching package id:
                $matchingPackage = $services['packages']->getMatchingPackage($vals['frame_size'], $vals['frame_type'] , $vals['shipping_type']);

                // If there's a coupon, validate it, otherwise try to get a sale discount:
                if (!empty($vals['coupon_code']) && $matchingPackage) {
                    $discount = $services['packages']->validateCoupon($vals['coupon_code'], $matchingPackage['package_id'][0]);

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
                    $addedOrder = $services['packages']->addOrder(user()->id, $vals['shipping_address'], $matchingPackage['package_id'], json_encode($oderDetails), $tempFile['path'], $tempFile['server_id'], $vals['notes']);
                    //Remove this temporary row in `phpfox_temp_file` table
                    Phpfox::getService('core.temp-file')->delete($vals['temp_file']);
                }

                if ($addedOrder) {
                    $this->url()->send('client-dashboard/orders',null,'New order was added successfully');
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
            'addresses' => $addresses
        ]);

    }
}

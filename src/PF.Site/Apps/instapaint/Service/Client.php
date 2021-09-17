<?php

namespace Apps\Instapaint\Service;

class Client extends \Phpfox_Service
{
    public function getClientAddresses($userId) {
        $rows = db()
            ->select("a.address_id, a.client_user_id, a.country_iso, a.full_name, a.street_address, a.street_address_2, a.city, a.state_province_region, a.zip_code, a.dial_code_iso, a.phone_number, a.security_access_code, a.can_receive_on_saturday, a.can_receive_on_sunday, c.name AS country_name, c2.name AS dial_code_country_name, c2.dial_code, IF(da.default_address_id, TRUE, FALSE) as is_default")
            ->from('phpfox_instapaint_address', 'a')
            ->where(['a.client_user_id' => $userId])
            ->leftJoin('phpfox_instapaint_default_address', 'da', 'a.address_id = da.address_id AND a.client_user_id = da.client_user_id')
            ->leftJoin('phpfox_country', 'c', 'a.country_iso = c.country_iso')
            ->leftJoin('phpfox_country', 'c2', 'a.dial_code_iso = c2.country_iso')
            ->order('a.address_id ASC')
            ->executeRows();

        return $rows;
    }

    public function getCountries() {
        return db()->select('*')
            ->from(':country')
            ->order('ordering, name ASC')
            ->executeRows();
    }

    public function addAddress($vals) {
        return db()->insert(':instapaint_address', [
            'client_user_id' => user()->id,
            'country_iso' => $vals['country_iso'],
            'full_name' => $vals['full_name'],
            'street_address' => $vals['street_address'],
            'street_address_2' => $vals['street_address_2'],
            'city' => $vals['city'],
            'state_province_region' => $vals['state_province_region'],
            'zip_code' => $vals['zip_code'],
            'dial_code_iso' => $vals['dial_code_iso'],
            'phone_number' => $vals['phone_number'],
            'security_access_code' => $vals['security_access_code'],
            'can_receive_on_saturday' => isset($vals['can_receive_on_saturday']) ? 1 : 0,
            'can_receive_on_sunday' => isset($vals['can_receive_on_sunday']) ? 1 : 0,
        ]);
    }

    public function updateAddress($addressId, $vals) {
        return db()->update(':instapaint_address', [
            'client_user_id' => user()->id,
            'country_iso' => $vals['country_iso'],
            'full_name' => $vals['full_name'],
            'street_address' => $vals['street_address'],
            'street_address_2' => $vals['street_address_2'],
            'city' => $vals['city'],
            'state_province_region' => $vals['state_province_region'],
            'zip_code' => $vals['zip_code'],
            'dial_code_iso' => $vals['dial_code_iso'],
            'phone_number' => $vals['phone_number'],
            'security_access_code' => $vals['security_access_code'],
            'can_receive_on_saturday' => $vals['can_receive_on_saturday'] ? 1 : 0,
            'can_receive_on_sunday' => $vals['can_receive_on_sunday'] ? 1 : 0,
        ], [
            'client_user_id' => user()->id,
            'address_id' => $addressId
        ]);
    }

    public function addressBelongsToUser($userId, $addressId) {
        return db()->select("a.address_id, a.client_user_id, a.country_iso, a.full_name, a.street_address, a.street_address_2, a.city, a.state_province_region, a.zip_code, a.dial_code_iso, a.phone_number, a.security_access_code, a.can_receive_on_saturday, a.can_receive_on_sunday, c.name AS country_name, c2.name AS dial_code_country_name, c2.dial_code")
            ->from('phpfox_instapaint_address', 'a')
            ->leftJoin('phpfox_country', 'c', 'a.country_iso = c.country_iso')
            ->leftJoin('phpfox_country', 'c2', 'a.dial_code_iso = c2.country_iso')
            ->where(['client_user_id' => $userId, 'address_id' => $addressId])
            ->executeRow();
    }

    public function deleteAddress($id) {
        return db()->delete(':instapaint_address', ['address_id' => $id]);
    }

    public function setDefaultAddress($userId, $addressId) {
        // Delete default address(es) of this user:
        db()->delete(':instapaint_default_address', ['client_user_id' => $userId]);

        // Add new default address:
        return db()->insert(':instapaint_default_address', [
            'client_user_id' => $userId,
            'address_id' => $addressId
        ]);
    }

    public function addOrder($vals) {
        return db()->insert(':instapaint_order', [
            'client_user_id' => user()->id,
            'order_status_id' => 1,
            'created_timestamp' => time(),
            'shipping_address_id' => $vals['address_id'],
            'package_id' => $vals['package_id'],
        ]);
    }

    public function getOrders($userId) {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, u.full_name as painter_full_name,
               u.user_image as painter_user_image,
               u.user_name as painter_user_name')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->leftJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin('phpfox_user', 'u', 'op.painter_user_id = u.user_id')
            ->where(['o.client_user_id' => $userId])
            ->order('o.order_id DESC')
            ->executeRows();

        $ordersProcessed = [
            'open' => [],
            'completed' => [],
            'pending_payment' => []
        ];

        foreach ($orders as $key => $order) {

            $order['painter_user_image'] = str_replace('%s', '_120_square', $order['painter_user_image']);

            // Decode JSON data:
            $orderDetails = json_decode($order['order_details'], true);
            $order['order_details'] = $orderDetails;

            // Compute total price with discount:
            $packagePrice = $order['order_details']['package']['total_price'];
            $discountPercentage = $order['order_details']['discount'] ? (int) $order['order_details']['discount']['discount_percentage'] : 0;

            $expeditedPrice = $order['is_expedited'] ? 80 : 0;

            // Faces price:
            $facesPrice = ((int) $order['faces'] == 0 ? 0.00 : $order['faces'] - 1) * 25.00;

            // Style price:
            if (!isset($order['order_details']['style'])) {
                $order['order_details']['style'] = [
                    'name' => 'other',
                    'price' => 0.00,
                    'price_str' => '0.00'
                ];
            }

            $discountPrice = ($packagePrice + $expeditedPrice + $facesPrice + $order['order_details']['style']['price']) * $discountPercentage/100;

            $order['price_with_discount'] =  ($packagePrice + $expeditedPrice + $facesPrice + $order['order_details']['style']['price']) - $discountPrice;

            // Format prices for user display:
            $order['faces_price_formatted'] = '$' . number_format($facesPrice, 2);
            $order['price_with_discount_formatted'] = '$' . number_format($order['price_with_discount'], 2);
            $order['frame_size_price_formatted'] = '$' . number_format($order['order_details']['package']['frame_size_price'], 2);
            $order['frame_type_price_formatted'] = '$' . number_format($order['order_details']['package']['frame_type_price'], 2);
            $order['shipping_type_price_formatted'] = '$' . number_format($order['order_details']['package']['shipping_type_price'], 2);
            $order['discount_price_formatted'] = '$' . number_format($discountPrice, 2);

            // Format expedited days:
            if ($order['is_expedited']) {
                $order['expedited_days'] = date('F j, Y', strtotime( '+' . $order['expedited_days'] . ' days', $order['created_timestamp']));
            } else {
                $order['expedited_days'] = null;
            }

            // Format style:
            $order['style'] = (int) $order['style'];
            $order['style_name'] = $order['order_details']['style']['name'];

            // Image path formatted:
            $order['photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['image_path']);

            // Painting path formatted:
            $order['painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['finished_painting_path']);
            $order['original_painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['finished_painting_path']);

            if ($order['status_name'] == 'Open') {
                array_push($ordersProcessed['open'], $order);
            } else if ($order['status_name'] == 'Completed' && $order['is_approved']) {
                array_push($ordersProcessed['completed'], $order);
            } else if ($order['status_name'] == 'Pending Payment') {
                array_push($ordersProcessed['pending_payment'], $order);
            }
        }

        return $ordersProcessed;
    }

    public function getOrder($userId, $orderId) {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, u.full_name as painter_full_name,
               u.user_image as painter_user_image,
               u.user_name as painter_user_name')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->leftJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin('phpfox_user', 'u', 'op.painter_user_id = u.user_id')
            ->where(['o.client_user_id' => $userId, 'o.order_id' => $orderId])
            ->order('o.order_id DESC')
            ->executeRows();

        $ordersProcessed = [];

        foreach ($orders as $key => $order) {

            $order['painter_user_image'] = str_replace('%s', '_120_square', $order['painter_user_image']);

            // Decode JSON data:
            $orderDetails = json_decode($order['order_details'], true);
            $order['order_details'] = $orderDetails;

            // Compute total price with discount:
            $packagePrice = $order['order_details']['package']['total_price'];
            $discountPercentage = $order['order_details']['discount'] ? (int) $order['order_details']['discount']['discount_percentage'] : 0;

            $expeditedPrice = $order['is_expedited'] ? 80 : 0;

            // Faces price:
            $facesPrice = ((int) $order['faces'] == 0 ? 0.00 : $order['faces'] - 1) * 25.00;

            // Style price:
            if (!isset($order['order_details']['style'])) {
                $order['order_details']['style'] = [
                    'name' => 'other',
                    'price' => 0.00,
                    'price_str' => '0.00'
                ];
            }

            $discountPrice = ($packagePrice + $expeditedPrice + $facesPrice + $order['order_details']['style']['price']) * $discountPercentage/100;

            $order['price_with_discount'] =  ($packagePrice + $expeditedPrice + $facesPrice + $order['order_details']['style']['price']) - $discountPrice;

            // Format prices for user display:
            $order['faces_price_formatted'] = '$' . number_format($facesPrice, 2);
            $order['price_with_discount_formatted'] = '$' . number_format($order['price_with_discount'], 2);
            $order['frame_size_price_formatted'] = '$' . number_format($order['order_details']['package']['frame_size_price'], 2);
            $order['frame_type_price_formatted'] = '$' . number_format($order['order_details']['package']['frame_type_price'], 2);
            $order['shipping_type_price_formatted'] = '$' . number_format($order['order_details']['package']['shipping_type_price'], 2);
            $order['discount_price_formatted'] = '$' . number_format($discountPrice, 2);

            // Format expedited days:
            if ($order['is_expedited']) {
                $order['expedited_days'] = date('F j, Y', strtotime( '+' . $order['expedited_days'] . ' days', $order['created_timestamp']));
            } else {
                $order['expedited_days'] = null;
            }

            // Format style:
            $order['style'] = (int) $order['style'];
            $order['style_name'] = $order['order_details']['style']['name'];

            // Image path formatted:
            $order['photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['image_path']);

            // Trim and format order notes:
            $order['order_notes'] = htmlspecialchars(trim($order['order_notes']));

            // Format full date:
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($order['created_timestamp']);
            $dateTime->setTimezone(new \DateTimeZone(\Phpfox::getService('instapaint.stats')->getTimeZoneName()));
            $order['created_date'] = $dateTime->format('F j, Y');

            array_push($ordersProcessed, $order);
        }

        return $ordersProcessed ? $ordersProcessed[0] : false;
    }

    public function cancelOrder($userId, $orderId) {
        return db()->update(':instapaint_order', ['order_status_id' => 4], "client_user_id = $userId AND order_id = $orderId");
    }

    /**
     * Count total orders (ignoring cancelled orders)
     */
    public function countOrders($userId) {
        $row = db()->getRow("
            SELECT COUNT(*) AS count
            FROM phpfox_instapaint_order
            WHERE client_user_id = $userId AND order_status_id IN (1,2,3)");

        return (int) $row['count'];
    }

    public function countPendingPaymentOrders($userId) {
        $row = db()->getRow("
            SELECT COUNT(*) AS count
            FROM phpfox_instapaint_order
            WHERE client_user_id = $userId AND order_status_id = 1");

        return (int) $row['count'];
    }

    public function countOpenOrders($userId) {
        $row = db()->getRow("
            SELECT COUNT(*) AS count
            FROM phpfox_instapaint_order
            WHERE client_user_id = $userId AND order_status_id = 2");

        return (int) $row['count'];
    }

    public function countCompletedOrders($userId) {
        $row = db()->getRow("
            SELECT COUNT(*) AS count
            FROM phpfox_instapaint_order
            WHERE client_user_id = $userId AND order_status_id = 3");

        return (int) $row['count'];
    }

    public function payOrder($orderId, $paymentDetails) {
        // Change order status to paid:
        db()->update(':instapaint_order', ['order_status_id' => 2], ['order_id' => $orderId]);

        // Add payment details to order:
        db()->update(':instapaint_order', ['payment_details' => $paymentDetails], ['order_id' => $orderId]);

        // Update timestamp:
        db()->update(':instapaint_order', ['updated_timestamp' => time()], ['order_id' => $orderId]);

        // Update updated:
        db()->update(':instapaint_order', ['updater_user_id' => user()->id], ['order_id' => $orderId]);

    }

}

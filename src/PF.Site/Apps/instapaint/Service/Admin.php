<?php

namespace Apps\Instapaint\Service;

class Admin extends \Phpfox_Service
{
    public function updateStylePrices($stylePrices) {
        foreach ($stylePrices as $styleId => $price) {
            db()->update(':instapaint_style', [
                'price' => (float) $price
            ], "style_id = $styleId");
        }
    }

    public function getShippedOrders() {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp, u.full_name as painter_full_name,
               u.user_image as painter_user_image,
               u.user_name as painter_user_name,
               c.user_name as client_user_name,
               c.full_name as client_full_name,
               c.email as client_email')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->join('phpfox_user', 'u', 'op.painter_user_id = u.user_id')
            ->leftJoin(':user', 'c', 'o.client_user_id = c.user_id')
            ->where('ar.is_shipped IS NOT NULL')
            ->order('o.updated_timestamp DESC')
            ->executeRows();

        $ordersProcessed = [
            'shipped_orders' => []
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
            $order['original_photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['image_path']);

            // Painting path formatted:
            $order['painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['finished_painting_path']);
            $order['original_painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['finished_painting_path']);

            // Shipment receipt path formatted:
            if ($order['shipment_receipt_path']) {
                $order['original_shipment_receipt_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['shipment_receipt_path']);
                $order['shipment_receipt_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['shipment_receipt_path']);
            }

            if ($order['status_name'] == 'Open' && $order['order_approval_request_id'] && !$order['is_approved'] && !$order['is_denied']) {

                array_push($ordersProcessed['approval_request_sent'], $order);

            } elseif ($order['order_status_id'] == 3 && $order['order_approval_request_id'] && $order['is_approved'] && $order['is_shipped']) {
                array_push($ordersProcessed['shipped_orders'], $order);
            } else if ($order['status_name'] == 'Open') {
                if ($order['is_approved']) {
                    array_push($ordersProcessed['approved_by_admin'], $order);
                } else {
                    array_push($ordersProcessed['open'], $order);
                }
            } else if ($order['status_name'] == 'Completed') {
                array_push($ordersProcessed['completed'], $order);
            } else if ($order['status_name'] == 'Pending Payment') {
                array_push($ordersProcessed['pending_payment'], $order);
            }
        }

        return $ordersProcessed;
    }

    public function getOrder($orderId) {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->where(['o.order_id' => $orderId])
            ->order('o.order_id DESC')
            ->executeRows();

        $ordersProcessed = [];

        foreach ($orders as $key => $order) {
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

    public function getOpenOrders() {

        $orders = db()->select('o.*, os.name_phrase as status_name, os.description_phrase as status_description, u.full_name as painter_full_name,
               u.user_image as painter_user_image,
               u.user_name as painter_user_name,
               c.user_name as client_user_name,
               c.full_name as client_full_name,
               c.email as client_email')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->leftJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin('phpfox_user', 'u', 'op.painter_user_id = u.user_id')
            ->leftJoin(':user', 'c', 'o.client_user_id = c.user_id')
            ->where('o.order_status_id = 2')
            ->order('o.order_id DESC')
            ->executeRows();

        $ordersProcessed = [
            'open' => []
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
            $order['original_photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['image_path']);

            // Painting path formatted:
            $order['painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['finished_painting_path']);
            $order['original_painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['finished_painting_path']);

            if ($order['status_name'] == 'Open' && $order['order_approval_request_id'] && !$order['is_approved'] && !$order['is_denied']) {

                array_push($ordersProcessed['approval_request_sent'], $order);

            } elseif ($order['order_status_id'] == 3 && $order['order_approval_request_id'] && $order['is_approved'] && $order['is_shipped']) {
                array_push($ordersProcessed['shipped_orders'], $order);
            } else if ($order['status_name'] == 'Open') {
                if ($order['is_approved']) {
                    array_push($ordersProcessed['approved_by_admin'], $order);
                } else {
                    array_push($ordersProcessed['open'], $order);
                }
            } else if ($order['status_name'] == 'Completed') {
                array_push($ordersProcessed['completed'], $order);
            } else if ($order['status_name'] == 'Pending Payment') {
                array_push($ordersProcessed['pending_payment'], $order);
            }
        }

        return $ordersProcessed;
    }

    public function getOrdersForApproval() {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp, u.full_name as painter_full_name,
               u.user_image as painter_user_image,
               u.user_name as painter_user_name,
               c.user_name as client_user_name,
               c.full_name as client_full_name,
               c.email as client_email')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->leftJoin(':user', 'c', 'o.client_user_id = c.user_id')
            ->join('phpfox_user', 'u', 'op.painter_user_id = u.user_id')
            ->order('o.updated_timestamp DESC')
            ->executeRows();

        $ordersProcessed = [
            'approval_request_sent' => []
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
            $order['original_photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['image_path']);

            // Painting path formatted:
            $order['painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['finished_painting_path']);
            $order['original_painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['finished_painting_path']);

            if ($order['status_name'] == 'Open' && $order['order_approval_request_id'] && !$order['is_approved'] && !$order['is_denied']) {

                array_push($ordersProcessed['approval_request_sent'], $order);

            }
        }

        return $ordersProcessed;
    }

    public function getOrderForApproval($orderId) {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp, u.full_name as painter_full_name,
               u.user_image as painter_user_image,
               u.user_name as painter_user_name,
               c.full_name as client_full_name,
               c.email as client_email')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->leftJoin(':user', 'c', 'o.client_user_id = c.user_id')
            ->join('phpfox_user', 'u', 'op.painter_user_id = u.user_id')
            ->where(['o.order_id' => $orderId])
            ->order('o.updated_timestamp DESC')
            ->executeRows();

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
            $order['original_photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['image_path']);

            // Painting path formatted:
            $order['painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['finished_painting_path']);
            $order['original_painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['finished_painting_path']);

            if ($order['status_name'] == 'Open' && $order['order_approval_request_id'] && !$order['is_approved'] && !$order['is_denied']) {

                return $order;

            }
        }
    }

    public function rejectOrder($orderId, $feedback) {
        db()->update(':instapaint_order_approval_request', [
            'is_denied' => 1,
            'reviewed_timestamp' => time(),
            'feedback' => $feedback
        ], "order_id = $orderId");

        db()->update(':instapaint_order', [
            'updated_timestamp' => time(),
            'updater_user_id' => user()->id,
        ], "order_id = $orderId");
    }

    public function approveOrder($orderId) {
        db()->update(':instapaint_order_approval_request', [
            'is_approved' => 1,
            'reviewed_timestamp' => time()
        ], "order_id = $orderId");

        db()->update(':instapaint_order', [
            'updated_timestamp' => time(),
            'updater_user_id' => user()->id,
        ], "order_id = $orderId");
    }

    public function countShippedOrders() {
        return db()->select('*')
            ->from(':instapaint_order')
            ->where([
                'order_status_id' => 3
            ])
            ->getCount();
    }

    public function countOpenOrders() {
        return db()->select('*')
            ->from(':instapaint_order')
            ->where([
                'order_status_id' => 2
            ])
            ->getCount();
    }

    public function countOrdersForApproval() {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->order('o.updated_timestamp DESC')
            ->executeRows();
        $count = 0;

        foreach ($orders as $key => $order) {
            if ($order['status_name'] == 'Open' && $order['order_approval_request_id'] && !$order['is_approved'] && !$order['is_denied']) {

                $count++;

            }
        }

        return $count;
    }

    public function getApprovedPainter($painterId) {
        $result = db()
            ->select('*')
            ->from('phpfox_user')
            ->where(['user_id' => $painterId])
            ->executeRow();

        // Format image string:
        if ($result) {
            $result['user_image'] = str_replace('%s', '_120_square', $result['user_image']);
        }

        return $result;
    }

    /*
     * Return daily jobs limit for a specific painter or default limit (painter_user_id = 0)
     */
    public function getMaxDailyOrders($userId) {
        $dailyLimit = db()->select('*')
            ->from(':instapaint_painter_daily_jobs_limit')
            ->where(['painter_user_id' => $userId])
            ->executeRow();

        if (!$dailyLimit) {
            $defaultDailyLimit = db()->select('*')
                ->from(':instapaint_painter_daily_jobs_limit')
                ->where(['painter_user_id' => 0])
                ->executeRow();

            return (int) $defaultDailyLimit['daily_limit'];
        }

        return (int) $dailyLimit['daily_limit'];
    }

    public function setPainterDailyJobsLimit($painterId, $limit) {
        db()->delete(':instapaint_painter_daily_jobs_limit', ['painter_user_id' => $painterId]);

        db()->insert(':instapaint_painter_daily_jobs_limit',[
            'painter_user_id' => $painterId,
            'daily_limit' => $limit
        ]);
    }

    public function getDefaultPainterDailyJobsLimit() {
        $default = db()->select('daily_limit')
            ->from(':instapaint_painter_daily_jobs_limit')
            ->where([
                'painter_user_id' => 0
            ])
            ->executeRow();
        return (int) $default['daily_limit'];
    }

    public function getExpeditedMinDays() {
        $result = db()->select('value')
            ->from(':instapaint_setting')
            ->where([
                'name' => 'expedited_min_days'
            ])
            ->executeRow();
        return (int) $result['value'];
    }

    public function setExpeditedMinDays($value) {
        db()->update(':instapaint_setting', [
            'value' => $value
        ], "name = 'expedited_min_days'");
    }

    public function deleteAllPainterDailyJobsLimits() {
        return db()->delete(':instapaint_painter_daily_jobs_limit', 'painter_user_id IS NOT NULL');
    }
}

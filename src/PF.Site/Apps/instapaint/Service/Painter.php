<?php

namespace Apps\Instapaint\Service;

class Painter extends \Phpfox_Service
{
    /**
     * Request painter approval
     *
     * @return bool true if request was added successfully, false otherwise
     */
    public function requestApproval() {
        try {
            $userId = user()->id;
        } catch (\Exception $e) {
            return false;
        }
        return db()->insert(\Phpfox::getT('instapaint_painter_approval_request'),[
            'user_id'=> $userId,
            'request_timestamp' => time(),
            'is_approved'=> 0
        ]);
    }

    public function hasRequestedApproval() {
        try {
            $userId = user()->id;
        } catch (\Exception $e) {
            return false;
        }
        return db()->select('*')
            ->from(':instapaint_painter_approval_request')
            ->where(['user_id' => $userId])
            ->executeRow();
    }

    public function getApprovalRequests() {
        $rows = db()
            ->select('
               ar.approval_request_id,
               ar.user_id,
               u.joined as user_joined_timestamp,
               ar.request_timestamp as approval_request_timestamp,
               u.full_name as user_full_name')
            ->from('phpfox_instapaint_painter_approval_request', 'ar')
            ->join('phpfox_user', 'u', 'ar.user_id = u.user_id')
            ->where([
                'is_approved' => 0
            ])
            ->executeRows();

        return $rows;
    }

    public function getApprovalRequestById($requestId) {
        $result = db()
            ->select('
               ar.approval_request_id,
               ar.user_id,
               u.joined as user_joined_timestamp,
               ar.request_timestamp as approval_request_timestamp,
               u.full_name as user_full_name,
               u.user_image,
               u.user_name,
               uc.cf_about_me')
            ->from('phpfox_instapaint_painter_approval_request', 'ar')
            ->join('phpfox_user', 'u', 'ar.user_id = u.user_id')
            ->leftJoin('phpfox_user_custom', 'uc', 'uc.user_id = u.user_id')
            ->where(['approval_request_id' => $requestId])
            ->executeRow();

        // Format image string:
        if ($result) {
            $result['user_image'] = str_replace('%s', '_120_square', $result['user_image']);
        }

        return $result;
    }

    public function approve($approvalRequest) {
        $securityService = \Phpfox::getService('instapaint.security');

        $requestWasUpdated = db()->update(
            ':instapaint_painter_approval_request',
            ['is_approved' => 1, 'approver_user_id' => user()->id, 'approved_timestamp' => time()],
            'approval_request_id = ' . $approvalRequest['approval_request_id']
        );

        $userWasUpdated = db()->update(
            ':user',
            ['user_group_id' => $securityService::APPROVED_PAINTER_GROUP_ID],
            'user_id = ' . $approvalRequest['user_id']
        );

        return $requestWasUpdated && $userWasUpdated;
    }

    public function denyApprovalRequest($approvalRequest) {
        return db()->delete(':instapaint_painter_approval_request','approval_request_id = ' . $approvalRequest['approval_request_id']);
    }

    public function countApprovalRequests() {

        return db()->select('*')
            ->from(':instapaint_painter_approval_request', 'ar')
            ->join(':user','u', 'ar.user_id = u.user_id')
            ->where(['ar.is_approved' => 0])
            ->getCount();
    }

    public function countApprovedPainters() {

        $securityService = \Phpfox::getService('instapaint.security');
        return db()->select('*')
            ->from(':user')
            ->where(['user_group_id' => $securityService::APPROVED_PAINTER_GROUP_ID])
            ->getCount();
    }

    public function countUnapprovedPainters() {

        $securityService = \Phpfox::getService('instapaint.security');
        return db()->select('*')
            ->from(':user')
            ->where(['user_group_id' => $securityService::PAINTER_GROUP_ID])
            ->getCount();
    }

    /**
     * Add share permission granted by painter,
     * this means this painter allows Instapaint
     * to use their work for promotional purposes
     * on social media.
     *
     * @param int $painterUserId The user id of the painter
     * @return int Inserted id
     */
    public function addSharePermission($painterUserId) {
        return db()->insert(':instapaint_painter_share_permission', ['painter_user_id' => $painterUserId]);
    }

    public function getAvailableOrders() {
        $orders = db()->select('o.*, os.name_phrase as status_name, os.description_phrase as status_description, c.user_name as client_user_name, c.full_name as client_full_name, c.email as client_email')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->leftJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':user', 'c', 'o.client_user_id = c.user_id')
            ->where('o.order_status_id = 2 AND op.order_id is null')
            ->order('o.order_id DESC')
            ->executeRows();

        $ordersProcessed = [
            'open' => [],
            'completed' => [],
            'pending_payment' => []
        ];

        foreach ($orders as $key => $order) {
            // Decode JSON data:
            $orderDetails = json_decode($order['order_details'], true);
            $order['order_details'] = $orderDetails;

            // Compute total price with discount:
            $packagePrice = $order['order_details']['package']['total_price'];
            $discountPercentage = $order['order_details']['discount'] ? (int) $order['order_details']['discount']['discount_percentage'] : 0;
            $discountPrice = $packagePrice * $discountPercentage/100;

            $order['price_with_discount'] =  $packagePrice - $discountPrice;

            // Format prices for user display:
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
            if (!isset($order['order_details']['style'])) {
                $order['order_details']['style'] = [
                    'name' => 'other',
                    'price' => 0.00,
                    'price_str' => '0.00'
                ];
            }
            $order['style'] = (int) $order['style'];
            $order['style_name'] = $order['order_details']['style']['name'];

            // Image path formatted:
            $order['photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['image_path']);
            $order['original_photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['image_path']);

            if ($order['status_name'] == 'Open') {
                array_push($ordersProcessed['open'], $order);
            } else if ($order['status_name'] == 'Completed') {
                array_push($ordersProcessed['completed'], $order);
            } else if ($order['status_name'] == 'Pending Payment') {
                array_push($ordersProcessed['pending_payment'], $order);
            }
        }

        return $ordersProcessed;
    }

    public function orderIsTaken($orderId) {
        return db()->select('*')
            ->from(':instapaint_order_painter')
            ->where(['order_id' => $orderId])
            ->getCount();
    }

    public function orderIsAvailable($orderId) {
        return db()->select('*')
            ->from(':instapaint_order')
            ->where(['order_id' => $orderId, 'order_status_id' => 2])
            ->getCount();
    }

    public function takeOrder($userId, $orderId) {
        db()->insert(':instapaint_order_painter', [
            'order_id' => $orderId,
            'painter_user_id' => $userId,
            'assigned_by_user_id' => $userId,
            'assigned_timestamp' => time(),
            'updater_user_id' => $userId,
            'updated_timestamp' => time()
        ]);

        db()->update(':instapaint_order', [
            'updated_timestamp' => time()
        ], "order_id = $orderId");
    }

    public function getTakenOrdersSince($painterUserId, $timeAgo) {
        $sinceTimestamp = strtotime($timeAgo);

        $takenOrders = db()->getRows(
            "SELECT COUNT(*) as count
            FROM phpfox_instapaint_order_painter
            WHERE assigned_timestamp >= $sinceTimestamp
            AND painter_user_id = $painterUserId"
        );

        return $takenOrders ? (int) $takenOrders[0]['count'] : 0;
    }

    /*
     * Return daily jobs limit for a specific painter or default limit (painter_user_id = 0)
     */
    public function getMaxDailyOrders() {
        $dailyLimit = db()->select('*')
            ->from(':instapaint_painter_daily_jobs_limit')
            ->where(['painter_user_id' => user()->id])
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

    public function getTakenOrders($painterUserId) {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp, c.user_name as client_user_name, c.full_name as client_full_name, c.email as client_email')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->leftJoin(':user', 'c', 'o.client_user_id = c.user_id')
            ->where('op.painter_user_id = ' . $painterUserId)
            ->order('o.updated_timestamp DESC')
            ->executeRows();

        $ordersProcessed = [
            'open' => [],
            'completed' => [],
            'pending_payment' => [],
            'approval_request_sent' => [],
            'approved_by_admin' => [],
            'shipped_orders' => []
        ];

        foreach ($orders as $key => $order) {
            // Decode JSON data:
            $orderDetails = json_decode($order['order_details'], true);
            $order['order_details'] = $orderDetails;

            // Compute total price with discount:
            $packagePrice = $order['order_details']['package']['total_price'];
            $discountPercentage = $order['order_details']['discount'] ? (int) $order['order_details']['discount']['discount_percentage'] : 0;
            $discountPrice = $packagePrice * $discountPercentage/100;

            $order['price_with_discount'] =  $packagePrice - $discountPrice;

            // Format prices for user display:
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
            if (!isset($order['order_details']['style'])) {
                $order['order_details']['style'] = [
                    'name' => 'other',
                    'price' => 0.00,
                    'price_str' => '0.00'
                ];
            }
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

    public function getOrderTakenByPainter($painterUserId, $orderId) {
        $order = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->where('op.painter_user_id = ' . $painterUserId . ' AND op.order_id = ' . $orderId)
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->order('o.order_id DESC')
            ->executeRow();
        return $order;
    }

    public function dropOrder($painterUserId, $orderId, $reason) {
        db()->delete(':instapaint_order_painter', "painter_user_id = $painterUserId AND order_id = $orderId ");

        db()->insert(':instapaint_order_drop', [
            'order_id' => $orderId,
            'painter_user_id' => $painterUserId,
            'reason' => $reason,
            'timestamp' => time()
        ]);

        $this->clearOrderApprovalRequest($orderId);
    }

    public function getOrder($orderId) {
        $orders = db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp, c.user_name as client_user_name, c.full_name as client_full_name, c.email as client_email')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->where(['o.order_id' => $orderId])
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->leftJoin(':user', 'c', 'o.client_user_id = c.user_id')
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
            $discountPrice = $packagePrice * $discountPercentage/100;

            $order['price_with_discount'] =  $packagePrice - $discountPrice;

            // Format prices for user display:
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
            if (!isset($order['order_details']['style'])) {
                $order['order_details']['style'] = [
                    'name' => 'other',
                    'price' => 0.00,
                    'price_str' => '0.00'
                ];
            }
            $order['style'] = (int) $order['style'];
            $order['style_name'] = $order['order_details']['style']['name'];

            // Image path formatted:
            $order['photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['image_path']);
            $order['original_photo_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['image_path']);


            // Painting path formatted:
            $order['painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '_500', $order['finished_painting_path']);
            $order['original_painting_path'] = '/PF.Base/file/pic/photo/' . str_replace('%s', '', $order['finished_painting_path']);

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

    /*
     * Add an order approval request
     */
    public function completeOrder($painterUserId, $orderId, $paintingPath) {
        $this->clearOrderApprovalRequest($orderId);

        db()->insert(':instapaint_order_approval_request', [
            'order_id' => $orderId,
            'painter_user_id' => $painterUserId,
            'request_timestamp' => time(),
            'finished_painting_path' => $paintingPath
        ]);

        db()->update(':instapaint_order', [
            'updated_timestamp' => time()
        ], "order_id = $orderId");
    }

    public function clearOrderApprovalRequest($orderId) {
        db()->delete(':instapaint_order_approval_request', ['order_id' => $orderId]);
    }

    public function countTakenOrders($userId) {
        return db()->select('*')
            ->from(':instapaint_order_painter')
            ->where(['painter_user_id' => $userId])
            ->getCount();
    }

   public function countOrdersSentForApproval($painterUserId) {
       return db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp')
           ->from(':instapaint_order', 'o')
           ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
           ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
           ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
           ->where('op.painter_user_id = ' . $painterUserId . ' AND ar.order_approval_request_id AND ar.reviewed_timestamp IS NULL' )
           ->order('o.updated_timestamp DESC')
           ->getCount();
   }

    public function countOrdersApprovedForShipping($painterUserId) {
        return db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->where('op.painter_user_id = ' . $painterUserId . ' AND ar.order_approval_request_id AND ar.reviewed_timestamp IS NOT NULL AND ar.is_approved = 1 AND ar.is_shipped IS NULL' )
            ->order('o.updated_timestamp DESC')
            ->getCount();
    }

    public function countOpenOrders($painterUserId) {
        return db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->where('op.painter_user_id = ' . $painterUserId . ' AND ar.order_approval_request_id IS NULL' )
            ->order('o.updated_timestamp DESC')
            ->getCount();
    }

    public function countOrdersPainting($painterUserId) {
        return db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->where('op.painter_user_id = ' . $painterUserId . ' AND ar.is_shipped IS NULL AND ((ar.is_approved IS NOT NULL AND ar.is_denied IS NOT NULL) OR (ar.is_approved IS NULL AND ar.is_denied = 1) OR ar.order_approval_request_id IS NULL)')
            ->order('o.updated_timestamp DESC')
            ->getCount();
    }

    public function countShippedOrders($painterUserId) {
        return db()->select('ar.*, o.*, os.name_phrase as status_name, os.description_phrase as status_description, op.assigned_timestamp')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->innerJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->leftJoin(':instapaint_order_approval_request', 'ar', 'o.order_id = ar.order_id')
            ->where('op.painter_user_id = ' . $painterUserId . ' AND ar.order_approval_request_id AND ar.reviewed_timestamp IS NOT NULL AND ar.is_approved = 1 AND ar.is_shipped = 1' )
            ->order('o.updated_timestamp DESC')
            ->getCount();
    }

    public function shipOrder($painterUserId, $orderId, $shippingNotes, $shipmentReceiptPath) {
        db()->update(':instapaint_order_approval_request', [
            'is_shipped' => 1,
            'shipped_timestamp' => time(),
            'shipping_notes' => trim($shippingNotes),
            'shipment_receipt_path' => $shipmentReceiptPath
        ], "order_id = $orderId AND painter_user_id = $painterUserId");

        db()->update(':instapaint_order', [
            'order_status_id' => 3,
            'updated_timestamp' => time(),
            'updater_user_id' => $painterUserId
        ], "order_id = $orderId");
    }

    public function countAvailableOrders() {
        $orders = db()->select('o.*, os.name_phrase as status_name, os.description_phrase as status_description')
            ->from(':instapaint_order', 'o')
            ->join(':instapaint_order_status', 'os', 'o.order_status_id = os.order_status_id')
            ->leftJoin(':instapaint_order_painter', 'op', 'o.order_id = op.order_id')
            ->where('o.order_status_id = 2 AND op.order_id is null')
            ->order('o.order_id DESC')
            ->getCount();

        return $orders;
    }

}

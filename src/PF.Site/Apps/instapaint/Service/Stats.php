<?php

namespace Apps\Instapaint\Service;

class Stats extends \Phpfox_Service
{
    public function countUsers() {
        return db()->select('*')
            ->from(':user')
            ->getCount();
    }

    public function countOnlineUsers() {
        $nowTimestamp = time();
        $minutesAgo = 15;
        $secondsAgoTimestamp = $nowTimestamp - $minutesAgo * 60;
        $result = db()->getRow(
            'SELECT COUNT(*) as count
            FROM phpfox_log_session
            WHERE last_activity >= ' . $secondsAgoTimestamp
        );

        return (int) $result['count'];
    }

    public function countUnapprovedPainters() {
       return \Phpfox::getService('instapaint.painter')->countUnapprovedPainters();
    }

    public function countApprovedPainters() {
        return \Phpfox::getService('instapaint.painter')->countApprovedPainters();
    }

    public function countClients() {
        $securityService = \Phpfox::getService('instapaint.security');

        return db()->select('*')
            ->from(':user')
            ->where(['user_group_id' => $securityService::CLIENT_GROUP_ID])
            ->getCount();
    }

    public function countAdmins() {
        $securityService = \Phpfox::getService('instapaint.security');

        return db()->select('*')
            ->from(':user')
            ->where(['user_group_id' => $securityService::ADMIN_GROUP_ID])
            ->getCount();
    }

    public function countPainterApprovalRequests() {
        return \Phpfox::getService('instapaint.painter')->countApprovalRequests();
    }

    public function getUsersCountByCountry() {
        return db()->getRows(
            'SELECT country_iso, COUNT(country_iso) as count
            FROM phpfox_user
            WHERE (country_iso IS NOT NULL) AND (country_iso != "")
            GROUP BY country_iso'
        );
    }

    public function getUserJoinsSince($timeAgo) {
        $sinceTimestamp = strtotime($timeAgo);

        $users = db()->getRows(
            "SELECT joined
            FROM phpfox_user
            WHERE joined >= $sinceTimestamp
            ORDER BY joined ASC"
        );

        foreach ($users as $key => $user) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($user['joined']);
            $dateTime->setTimezone(new \DateTimeZone($this->getTimeZoneName()));
            $dateFormated = $dateTime->format('Y-m-d');
            $users[$key]['joined_date'] = $dateFormated;
        }

        $userJoinsByDate = []; // Array to hold count of user joins by date

        // Populate $userJoinsByDate:
        foreach ($users as $key => $user) {
            if (isset($userJoinsByDate[$user['joined_date']])) {
                $userJoinsByDate[$user['joined_date']]++;
            } else {
                $userJoinsByDate[$user['joined_date']] = 1;
            }
        }

        return $userJoinsByDate;
    }

    public function countOrders() {
        return db()->select('*')
            ->from(':instapaint_order')
            ->getCount();
    }

    public function countOpenOrders() {
        return db()->select('*')
            ->from(':instapaint_order')
            ->where(['order_status_id' => 2])
            ->getCount();
    }

    public function countCancelledOrders() {
        return db()->select('*')
            ->from(':instapaint_order')
            ->where(['order_status_id' => 4])
            ->getCount();
    }

    public function countCompletedOrders() {
        return db()->select('*')
            ->from(':instapaint_order')
            ->where(['order_status_id' => 3])
            ->getCount();
    }

    public function countPendingPaymentOrders() {
        return db()->select('*')
            ->from(':instapaint_order')
            ->where(['order_status_id' => 1])
            ->getCount();
    }

    public function getOrdersSince($timeAgo) {
        return [];
    }

    public function countDiscounts() {
        return db()->select('*')
            ->from(':instapaint_discount')
            ->getCount();
    }

    public function countActiveDiscounts() {
        $now = time();

        $activeDiscounts = db()->getRow(
            "SELECT COUNT(*) as count
            FROM phpfox_instapaint_discount
            WHERE expiration_timestamp > $now
            OR expiration_timestamp IS NULL
            OR expiration_timestamp = 0"
        );

        return (int) $activeDiscounts['count'];
    }

    public function countExpiredDiscounts() {
        return $this->countDiscounts() - $this->countActiveDiscounts();
    }

    public function countActiveCoupons() {
        $now = time();

        $activeCoupons = db()->getRow(
            "SELECT COUNT(*) as count
            FROM phpfox_instapaint_discount
            WHERE (expiration_timestamp > $now
            OR expiration_timestamp IS NULL
            OR expiration_timestamp = 0)
            AND (coupon_code IS NOT NULL
            AND coupon_code != '')"
        );

        return (int) $activeCoupons['count'];
    }

    public function countExpiredCoupons() {
        $now = time();

        $expiredCoupons = db()->getRow(
            "SELECT COUNT(*) as count
            FROM phpfox_instapaint_discount
            WHERE (expiration_timestamp < 100
            AND expiration_timestamp != 0)
            AND (coupon_code IS NOT NULL
            AND coupon_code != '')"
        );

        return (int) $expiredCoupons['count'];
    }

    public function countActiveSales() {
        $now = time();

        $activeSales = db()->getRow(
            "SELECT COUNT(*) as count
            FROM phpfox_instapaint_discount
            WHERE (expiration_timestamp > $now
            OR expiration_timestamp IS NULL
            OR expiration_timestamp = 0)
            AND (coupon_code IS NULL
            OR coupon_code = '')"
        );

        return (int) $activeSales['count'];
    }

    public function countExpiredSales() {
        $now = time();

        $expiredSales = db()->getRow(
            "SELECT COUNT(*) as count
            FROM phpfox_instapaint_discount
            WHERE (expiration_timestamp < $now
            AND expiration_timestamp != 0)
            AND (coupon_code IS NULL
            OR coupon_code = '')"
        );

        return (int) $expiredSales['count'];
    }

    public function countFrameSizes() {
        return db()->select('*')
            ->from(':instapaint_frame_size')
            ->getCount();
    }

    public function countFrameTypes() {
        return db()->select('*')
            ->from(':instapaint_frame_type')
            ->getCount();
    }

    public function countShippingTypes() {
        return db()->select('*')
            ->from(':instapaint_shipping_type')
            ->getCount();
    }

    public function countPackages() {
        return db()->select('*')
            ->from(':instapaint_package')
            ->getCount();
    }

    public function getTopSellerPackages() {
        return [
            1 => [
                'name' => 'Example Package 1',
                'sales' => 20
            ],
            2 => [
                'name' => 'Example Package 2',
                'sales' => 15
            ],
            3 => [
                'name' => 'Example Package 3',
                'sales' => 9
            ] // Get to 10...
        ];
    }

    public function getPackagesSoldSince($timeAgo) {
        return [];
    }

    public function countPhotos() {
        return db()->select('photo_id')
            ->from(':photo')
            ->getCount();
    }

    public function countPhotoAlbums() {
        return db()->select('album_id')
            ->from(':photo_album')
            ->getCount();
    }

    public function countStatuses() {
        return db()->select('status_id')
            ->from(':user_status')
            ->getCount();
    }

    public function getStats() {
        $usersCount = $this->countUsers();
        $onlineUsersCount = $this->countOnlineUsers();
        $approvedPaintersCount = $this->countApprovedPainters();
        $unapprovedPaintersCount = $this->countUnapprovedPainters();
        $clientsCount = $this->countClients();
        $adminsCount = $this->countAdmins();
        $painterApprovalRequestsCount = $this->countPainterApprovalRequests();
        $usersByCountry = $this->getUsersCountByCountry();
        $userJoinsLast7Days = $this->getUserJoinsSince('7 days ago');
        $userJoinsLast28Days = $this->getUserJoinsSince('28 days ago');

        $ordersCount = $this->countOrders();
        $pendingPaymentOrders = $this->countPendingPaymentOrders();
        $cancelledOrdersCount = $this->countCancelledOrders();
        $openOrders = $this->countOpenOrders();
        $completedOrders = $this->countCompletedOrders();
        $ordersLast7Days = $this->getOrdersSince('7 days ago');
        $ordersLast28Days = $this->getOrdersSince('28 days ago');

        $activeDiscountsCount = $this->countActiveDiscounts();
        $expiredDiscountsCount = $this->countExpiredDiscounts();
        $discountsCount = $activeDiscountsCount + $expiredDiscountsCount;
        $activeCouponsCount = $this->countActiveCoupons();
        $expiredCouponsCount = $this->countExpiredCoupons();
        $activeSalesCount = $this->countActiveSales();
        $expiredSalesCount = $this->countExpiredSales();
        $couponsCount = $activeCouponsCount + $expiredCouponsCount;
        $salesCount = $activeSalesCount + $expiredSalesCount;

        $frameSizesCount = $this->countFrameSizes();
        $frameTypesCount = $this->countFrameTypes();
        $shippingTypesCount = $this->countShippingTypes();
        $packagesCount = $this->countPackages();
        $topSellerPackages = $this->getTopSellerPackages();
        $packagesSoldLast7Days = $this->getPackagesSoldSince('7 days ago');
        $packagesSoldLast28Days = $this->getPackagesSoldSince('28 days ago');

        $photosCount = $this->countPhotos();
        $albumsCount = $this->countPhotoAlbums();
        $statusesCount = $this->countStatuses();

        $stats =[
            'counts' => [
                'users' => $usersCount,
                'online_users' => $onlineUsersCount,
                'admins' => $adminsCount,
                'clients' => $clientsCount,
                'approved_painters' => $approvedPaintersCount,
                'unapproved_painters' => $unapprovedPaintersCount,
                'painter_approval_requests' => $painterApprovalRequestsCount,
                'orders' => $ordersCount,
                'pending_payment_orders' => $pendingPaymentOrders,
                'cancelled_orders' => $cancelledOrdersCount,
                'open_orders' => $openOrders,
                'completed_orders' => $completedOrders,
                'discounts' => $discountsCount,
                'active_discounts' => $activeDiscountsCount,
                'expired_discounts' => $expiredDiscountsCount,
                'coupons' => $couponsCount,
                'sales' => $salesCount,
                'active_coupons' => $activeCouponsCount,
                'expired_coupons' => $expiredCouponsCount,
                'active_sales' => $activeSalesCount,
                'expired_sales' => $expiredSalesCount,
                'frame_sizes' => $frameSizesCount,
                'frame_types' => $frameTypesCount,
                'shipping_types' => $shippingTypesCount,
                'packages' => $packagesCount,
                'photos' => $photosCount,
                'albums' => $albumsCount,
                'statuses' => $statusesCount
            ],
            'users_by_country' => $usersByCountry,
            'user_joins_last_7_days' => $userJoinsLast7Days,
            'user_joins_last_28_days' => $userJoinsLast28Days,
            'orders_last_7_days' => $ordersLast7Days,
            'orders_last_28_days' => $ordersLast28Days,
            'top_seller_packages' => $topSellerPackages,
            'packages_sold_last_7_days' => $packagesSoldLast7Days,
            'packages_sold_last_28_days' => $packagesSoldLast28Days,
        ];

        return $stats;
    }

    public function getTimeZoneName() {
        $result = db()->select('time_zone')
            ->from(':user')
            ->where(['user_id' => user()->id])
            ->executeRow();

        $aTimeZones = \Phpfox::getService('core')->getTimeZones();

        if (!$result['time_zone']) {
            return 'UTC';
        }

        return $aTimeZones[$result['time_zone']];
    }
}

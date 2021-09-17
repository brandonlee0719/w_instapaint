<?php

namespace Apps\Instapaint\Service;

use upload;

class Packages extends \Phpfox_Service
{
    const validStyles = [0, 5, 7, 11, 17, 15, 9, 18, 12, 13, 6, 2, 3, 14, 16];

    public function isValidStyle($styleId) {
        return in_array($styleId, self::validStyles);
    }

    public function getStyleInfo($styleId) {

        $styleInfo = db()->select('*')
            ->from(':instapaint_style')
            ->where(['style_id' => $styleId])
            ->executeRows()[0];

        $styleInfo['price'] = (float) $styleInfo['price'];
        $styleInfo['price_str'] = number_format($styleInfo['price'], 2);

        return $styleInfo;
    }

    public function getStyles() {
        $styles = db()->select('*')
            ->from(':instapaint_style')
            ->executeRows();
        foreach ($styles as $key => $value) {
            $styles[$key]['price_str'] = number_format($styles[$key]['price'], 2);
        }
        return $styles;
    }

    public function getFrameSizes() {
        return db()->select('*')
            ->from(':instapaint_frame_size')
            ->executeRows();
    }

    public function getFrameSizeById($frameSizeId) {
        return db()->select('*')
            ->from(':instapaint_frame_size')
            ->where(['frame_size_id' => $frameSizeId])
            ->executeRow();
    }

    public function updateFrameSize($frameSizeId, $frameSizeName, $frameSizeDescription, $frameSizePrice) {
        // Convert to HTML entities
        $frameSizeName = htmlentities($frameSizeName);
        $frameSizeDescription = htmlentities($frameSizeDescription);
        return db()->update(':instapaint_frame_size', ['name_phrase' => $frameSizeName, 'description_phrase' => $frameSizeDescription, 'price_usd' => $frameSizePrice], ['frame_size_id' => $frameSizeId]);
    }

    public function deleteFrameSizeById($frameSizeId) {
        return db()->delete(':instapaint_frame_size', ['frame_size_id' => $frameSizeId]);
    }

    public function addFrameSize($frameSizeName, $frameSizeDescription, $frameSizePrice) {
        // Convert to HTML entities
        $frameSizeName = htmlentities($frameSizeName);
        $frameSizeDescription = htmlentities($frameSizeDescription);

        return db()->insert(\Phpfox::getT('instapaint_frame_size'),[
            'name_phrase'=> $frameSizeName,
            'description_phrase' => $frameSizeDescription,
            'price_usd'=> $frameSizePrice
        ]);
    }



    public function getFrameTypes() {
        return db()->select('*')
            ->from(':instapaint_frame_type')
            ->executeRows();
    }

    public function getFrameTypeById($frameTypeId) {
        return db()->select('*')
            ->from(':instapaint_frame_type')
            ->where(['frame_type_id' => $frameTypeId])
            ->executeRow();
    }

    public function updateFrameType($frameTypeId, $frameTypeName, $frameTypeDescription, $frameTypePrice) {
        // Convert to HTML entities
        $frameTypeName = htmlentities($frameTypeName);
        $frameTypeDescription = htmlentities($frameTypeDescription);
        return db()->update(':instapaint_frame_type', ['name_phrase' => $frameTypeName, 'description_phrase' => $frameTypeDescription, 'price_usd' => $frameTypePrice], ['frame_type_id' => $frameTypeId]);
    }

    public function deleteFrameTypeById($frameTypeId) {
        return db()->delete(':instapaint_frame_type', ['frame_type_id' => $frameTypeId]);
    }

    public function addFrameType($frameTypeName, $frameTypeDescription, $frameTypePrice) {
        // Convert to HTML entities
        $frameTypeName = htmlentities($frameTypeName);
        $frameTypeDescription = htmlentities($frameTypeDescription);
        return db()->insert(\Phpfox::getT('instapaint_frame_type'),[
            'name_phrase'=> $frameTypeName,
            'description_phrase'=> $frameTypeDescription,
            'price_usd'=> $frameTypePrice
        ]);
    }



    public function getShippingTypes() {
        return db()->select('*')
            ->from(':instapaint_shipping_type')
            ->executeRows();
    }

    public function getShippingTypeById($shippingTypeId) {
        return db()->select('*')
            ->from(':instapaint_shipping_type')
            ->where(['shipping_type_id' => $shippingTypeId])
            ->executeRow();
    }

    public function updateShippingType($shippingTypeId, $shippingTypeName, $shippingTypeDescription, $shippingTypePrice) {
        // Convert to HTML entities
        $shippingTypeName = htmlentities($shippingTypeName);
        $shippingTypeDescription = htmlentities($shippingTypeDescription);
        return db()->update(':instapaint_shipping_type', ['name_phrase' => $shippingTypeName, 'description_phrase' => $shippingTypeDescription, 'price_usd' => $shippingTypePrice], ['shipping_type_id' => $shippingTypeId]);
    }

    public function deleteShippingTypeById($shippingTypeId) {
        return db()->delete(':instapaint_shipping_type', ['shipping_type_id' => $shippingTypeId]);
    }

    public function addShippingType($shippingTypeName, $shippingTypeDescription, $shippingTypePrice) {
        // Convert to HTML entities
        $shippingTypeName = htmlentities($shippingTypeName);
        $shippingTypeDescription = htmlentities($shippingTypeDescription);
        return db()->insert(\Phpfox::getT('instapaint_shipping_type'),[
            'name_phrase'=> $shippingTypeName,
            'description_phrase'=> $shippingTypeDescription,
            'price_usd'=> $shippingTypePrice
        ]);
    }



    public function getPackages() {
        $rows = db()
            ->select('
               pkg.package_id,
               fz.frame_size_id as frame_size_id,
               fz.name_phrase as frame_size_name,
               fz.description_phrase as frame_size_description,
               fz.price_usd as frame_size_price,
               ft.frame_type_id as frame_type_id,
               ft.name_phrase as frame_type_name,
               ft.description_phrase as frame_type_description,
               ft.price_usd as frame_type_price,
               st.shipping_type_id as shipping_type_id,
               st.name_phrase as shipping_type_name,
               st.description_phrase as shipping_type_description,
               st.price_usd as shipping_type_price')
            ->from('phpfox_instapaint_package', 'pkg')
            ->join('phpfox_instapaint_frame_size', 'fz', 'pkg.frame_size_id = fz.frame_size_id')
            ->join('phpfox_instapaint_frame_type', 'ft', 'pkg.frame_type_id = ft.frame_type_id')
            ->join('phpfox_instapaint_shipping_type', 'st', 'pkg.shipping_type_id = st.shipping_type_id')
            ->executeRows();

        foreach ($rows as $key => $row) {
            $rows[$key]['total_price'] = $row['frame_size_price'] + $row['frame_type_price'] + $row['shipping_type_price'];
        }

        return $rows;
    }

    public function addPackage($frameSizeId, $frameTypeId, $shippingTypeId) {
        return db()->insert(\Phpfox::getT('instapaint_package'),[
            'frame_size_id'=> $frameSizeId,
            'frame_type_id' => $frameTypeId,
            'shipping_type_id'=> $shippingTypeId
        ]);
    }

    public function packageExists($frameSizeId, $frameTypeId, $shippingTypeId) {
        return db()->select('*')
            ->from(':instapaint_package')
            ->where(['frame_size_id' => $frameSizeId, 'frame_type_id' => $frameTypeId, 'shipping_type_id' => $shippingTypeId])
            ->executeRow();
    }

    public function getPackageById($packageId) {
        return db()->select('*')
            ->from(':instapaint_package')
            ->where(['package_id' => $packageId])
            ->executeRow();
    }

    public function deletePackageById($packageId) {
        return db()->delete(':instapaint_package', ['package_id' => $packageId]);
    }

    public function getPackagesBy($field, $fieldValue) {
        return db()->select('*')
            ->from(':instapaint_package')
            ->where([$field => $fieldValue])
            ->executeRows();
    }

    public function addDiscount($name, $packages, $code, $amount, $expiration, $isGlobal) {
        $addedDiscount = db()->insert(':instapaint_discount', [
            'name' => $name,
            'coupon_code' => $code ? $code : null,
            'expiration_timestamp' => $expiration ? $expiration : null,
            'discount_percentage' => $amount,
            'time_stamp' => time()
        ]);

        if (!$isGlobal) {
            foreach ($packages as $package) {
                db()->insert(':instapaint_package_discount', [
                    'discount_id' => $addedDiscount,
                    'package_id' => $package
                ]);
            }
        }

        if ($addedDiscount) {
            return true;
        } else {
            return false;
        }
    }

    public function getDiscountById($discountId) {
        $discount = db()->select('discount_id, name, coupon_code as code, expiration_timestamp as expiration, discount_percentage as amount')
            ->from(':instapaint_discount')
            ->where(['discount_id' => $discountId])
            ->executeRow();

        $packageDiscounts = db()->select('package_id')
            ->from(':instapaint_package_discount')
            ->where(['discount_id' => $discountId])
            ->executeRows();

        $packageDiscountIds = [];

        foreach ($packageDiscounts as $packageDiscount) {
            $packageDiscountIds[] = $packageDiscount['package_id'];
        }

        if ($discount) {
            $discount['packages'] = $packageDiscountIds;

            if ($discount['expiration']) {
                $discount['expiration'] = date('Y-m-d', $discount['expiration']);
            }

            if (empty($packageDiscountIds)) {
                $discount['is_global_discount'] = true;
            }
        }

        return $discount;
    }

    public function deleteDiscountById($discountId) {
        db()->delete(':instapaint_discount', ['discount_id' => $discountId]);
        db()->delete(':instapaint_package_discount', ['discount_id' => $discountId]);
        return true;
    }

    public function updateDiscount($discountId, $discount) {
        db()->update(':instapaint_discount', ['name' => $discount['name'], 'coupon_code' => $discount['code'], 'expiration_timestamp' => strtotime($discount['expiration']), 'discount_percentage' => $discount['amount']], ['discount_id' => $discountId]);

        // Delete all package discounts of this discount:
        db()->delete(':instapaint_package_discount', ['discount_id' => $discountId]);

        // Add package discounts:
        if (!$discount['is_global_discount']) {
            foreach ($discount['packages'] as $package) {
                db()->insert(':instapaint_package_discount', [
                    'discount_id' => $discountId,
                    'package_id' => $package
                ]);
            }
        }

        return true;
    }

    /**
     * Delete discount only in instapaint_package_discount table,
     * useful when deleting a package so related discounts are modified
     * to not include the deleted package.
     *
     * @param int $packageId The package ID
     *
     * @return bool Success or failure
     */
    public function deletePackageDiscount($packageId) {
        return db()->delete(':instapaint_package_discount', ['package_id' => $packageId]);
    }

    /**
     * Delete discounts that apply only to the specified package,
     * this way when the package is deleted the discount will be deleted too
     *
     * @param int $packageId The package Id
     */
    public function deleteExclusiveDiscounts($packageId) {

        $exclusiveDiscounts = db()->getRows(
            'select * from
            (select discount_id
            from phpfox_instapaint_package_discount
            group by discount_id
            having count(discount_id) = 1) as uniques
            join phpfox_instapaint_package_discount as pd
            on uniques.discount_id = pd.discount_id
            where package_id = ' . (int) $packageId . ';'
        );

        foreach ($exclusiveDiscounts as $exclusiveDiscount) {

            db()->delete(':instapaint_discount', ['discount_id' => $exclusiveDiscount['discount_id']]);
        }
    }

    public function getPackagesForForm() {
        $rows = db()
            ->select('
               pkg.package_id,
               fz.frame_size_id as frame_size_id,
               fz.name_phrase as frame_size_name,
               fz.description_phrase as frame_size_description,
               fz.price_usd as frame_size_price,
               ft.frame_type_id as frame_type_id,
               ft.name_phrase as frame_type_name,
               ft.description_phrase as frame_type_description,
               ft.price_usd as frame_type_price,
               st.shipping_type_id as shipping_type_id,
               st.name_phrase as shipping_type_name,
               st.description_phrase as shipping_type_description,
               st.price_usd as shipping_type_price')
            ->from('phpfox_instapaint_package', 'pkg')
            ->join('phpfox_instapaint_frame_size', 'fz', 'pkg.frame_size_id = fz.frame_size_id')
            ->join('phpfox_instapaint_frame_type', 'ft', 'pkg.frame_type_id = ft.frame_type_id')
            ->join('phpfox_instapaint_shipping_type', 'st', 'pkg.shipping_type_id = st.shipping_type_id')
            ->executeRows();

        foreach ($rows as $key => $row) {
            $rows[$key]['total_price'] = $row['frame_size_price'] + $row['frame_type_price'] + $row['shipping_type_price'];
        }

        return $rows;
    }

    public function getPackageDetailed($packageId) {
        $rows = db()
            ->select('
               pkg.package_id,
               fz.frame_size_id as frame_size_id,
               fz.name_phrase as frame_size_name,
               fz.description_phrase as frame_size_description,
               fz.price_usd as frame_size_price,
               ft.frame_type_id as frame_type_id,
               ft.name_phrase as frame_type_name,
               ft.description_phrase as frame_type_description,
               ft.price_usd as frame_type_price,
               st.shipping_type_id as shipping_type_id,
               st.name_phrase as shipping_type_name,
               st.description_phrase as shipping_type_description,
               st.price_usd as shipping_type_price')
            ->from('phpfox_instapaint_package', 'pkg')
            ->join('phpfox_instapaint_frame_size', 'fz', 'pkg.frame_size_id = fz.frame_size_id')
            ->join('phpfox_instapaint_frame_type', 'ft', 'pkg.frame_type_id = ft.frame_type_id')
            ->join('phpfox_instapaint_shipping_type', 'st', 'pkg.shipping_type_id = st.shipping_type_id')
            ->where(['pkg.package_id' => $packageId])
            ->executeRows();

        foreach ($rows as $key => $row) {
            $rows[$key]['total_price'] = $row['frame_size_price'] + $row['frame_type_price'] + $row['shipping_type_price'];
        }

        return $rows;
    }

    /**
     * Gets sale (not discount) with highest priority for specific package
     *
     * Discount prioritization
     *
     * Packages(products) can have multiple discounts enabled at one time. However, the discounts do not sum up. Discounts are prioritized based on their position in the list below (the higher the discount type appears in the list, the higher the priority):
     *
     * 1. Coupon code;
     * 2. Sale of a specific product;
     * 3. Sale of all products.
     *
     */
    public function getSaleForPackage($packageId) {
        $nowTimestamp = time();

        // Get latest valid package sale
        $packageDiscount = db()->getRow("SELECT *
            FROM phpfox_instapaint_package_discount AS pd
            JOIN phpfox_instapaint_discount AS d
            ON pd.discount_id = d.discount_id
            WHERE pd.package_id = $packageId
            AND (d.expiration_timestamp > $nowTimestamp
                         OR d.expiration_timestamp IS NULL
                         OR d.expiration_timestamp = 0)
            AND (d.coupon_code = ''
                         OR d.coupon_code IS NULL)
            ORDER BY d.discount_id DESC
            LIMIT 1");

        if ($packageDiscount) return $packageDiscount;


        // Get latest valid global sale
        $globalDiscount = db()->getRow("SELECT * FROM
            (SELECT d.*, pd.package_discount_id
            FROM phpfox_instapaint_discount AS d
            LEFT JOIN phpfox_instapaint_package_discount AS pd
            ON d.discount_id = pd.discount_id) AS ALL_DISCOUNTS
            WHERE package_discount_id IS NULL
            AND (expiration_timestamp > $nowTimestamp
                 OR expiration_timestamp IS NULL
                 OR expiration_timestamp = 0)
            AND (coupon_code = ''
                 OR coupon_code IS NULL)
            ORDER BY discount_id DESC
            LIMIT 1");

        if ($globalDiscount) return $globalDiscount;

        return false;
    }

    public function validateCoupon($couponCode, $packageId) {
        $nowTimestamp = time();

        // Get latest valid package sale
        $packageCoupon = db()->getRow("SELECT *
            FROM phpfox_instapaint_package_discount AS pd
            JOIN phpfox_instapaint_discount AS d
            ON pd.discount_id = d.discount_id
            WHERE pd.package_id = $packageId
            AND (d.expiration_timestamp > $nowTimestamp
                         OR d.expiration_timestamp IS NULL
                         OR d.expiration_timestamp = 0)
            AND (d.coupon_code = '$couponCode'
                         AND d.coupon_code IS NOT NULL)
            ORDER BY d.discount_id DESC
            LIMIT 1");

        if ($packageCoupon) return $packageCoupon;

        // Get latest valid global sale
        $globalCoupon = db()->getRow("SELECT * FROM
            (SELECT d.*, pd.package_discount_id
            FROM phpfox_instapaint_discount AS d
            LEFT JOIN phpfox_instapaint_package_discount AS pd
            ON d.discount_id = pd.discount_id) AS ALL_DISCOUNTS
            WHERE package_discount_id IS NULL
            AND (expiration_timestamp > $nowTimestamp
                 OR expiration_timestamp IS NULL
                 OR expiration_timestamp = 0)
            AND (coupon_code = '$couponCode'
                 AND coupon_code IS NOT NULL)
            ORDER BY discount_id DESC
            LIMIT 1");

        if ($globalCoupon) return $globalCoupon;

        return false;
    }

    function getMatchingPackage($frameSizeId, $frameTypeId, $shippingTypeId) {
        return db()->select('*')
            ->from(':instapaint_package')
            ->where([
                'frame_size_id' => $frameSizeId,
                'frame_type_id' => $frameTypeId,
                'shipping_type_id' => $shippingTypeId
            ])->executeRow();
    }

    function addOrder($clientUserId, $shippingAddressId, $packageId, $details, $imagePath, $serverId, $notes, $expedited, $faces, $expeditedDays, $style) {
        return db()->insert(':instapaint_order', [
            'order_status_id' => 1,
            'created_timestamp' => time(),
            'client_user_id' => $clientUserId,
            'shipping_address_id' => $shippingAddressId,
            'package_id' => $packageId,
            'order_details' => $details,
            'image_path' => $imagePath,
            'server_id' => $serverId,
            'order_notes' => $notes,
            'is_expedited' => (int) $expedited,
            'faces' => (int) $faces,
            'expedited_days' => (int) $expedited == 1 ? (int) $expeditedDays : null,
            'style' => (int) $style
        ]);
    }

    public function savePartialOrder($vals, $photoPath, $thumbnailPath, $uniqueId, $packageId, $isExpedited, $faces, $expeditedDays, $style) {
        return db()->insert(':instapaint_partial_order', [
            'unique_id' => $uniqueId,
            'photo_path' => $photoPath,
            'thumbnail_path' => $thumbnailPath,
            'order_notes' => $vals['order_notes'],
            'package_id' => $packageId,
            'is_expedited' => (int) $isExpedited,
            'faces' => (int) $faces,
            'expedited_days' => (int) $isExpedited == 1 ? (int) $expeditedDays : null,
            'style' => (int) $style
        ]);
    }

    public function savePartialOrderPhoto($tempFile, $uniqueId) {

        // Check file size
        if ($tempFile['error'] == 1) {
            return ['error', 'Your file must be less than 30 MB'];
        }

        // Check file type
        if ($tempFile['type'] == 'image/jpeg' || $tempFile['type'] == 'image/png') {

        } else {
            return ['error', 'This file type is not valid, it must be JPEG or PNG'];
        }

        // Validate the file MIME type:
        $fileMIME = mime_content_type($tempFile['tmp_name']);
        if($fileMIME !== 'image/jpeg' && $fileMIME !== 'image/png') {
            return ['error', 'The file must be a PNG or JPEG image.'];
        }

        $handle = new upload($tempFile);
        $finalFiles = [];
        if ($handle->uploaded) {
            $handle->file_safe_name = false;
            $handle->file_new_name_body   = $uniqueId . '_500';
            $handle->image_resize         = true;
            $handle->image_x              = 500;
            $handle->image_ratio_y        = true;
            $handle->process(\Phpfox::getParam('photo.dir_photo') . 'partial_orders/' . $uniqueId);
            $finalFiles['thumbnail'] = $handle->file_dst_name;
            $handle->file_new_name_body   = $uniqueId;
            $handle->image_resize         = false;
            $handle->process(\Phpfox::getParam('photo.dir_photo') . 'partial_orders/' . $uniqueId);
            if ($handle->processed) {
                $finalFiles['original'] = $handle->file_dst_name;
                $handle->clean();
                return ['success', $finalFiles];
            } else {
                return ['error', $handle->error];
            }
        }
    }

    public function partialOrderSetUser($userId, $orderUniqueId) {
        $status = db()->update(':instapaint_partial_order', ['user_id' => $userId], "unique_id = '$orderUniqueId'");
        \Phpfox::getService('instapaint.events')->addEvent($userId,array("action"=>"partial_order_added"));
        return $status;

    }

    public function getPartialOrderByUser($userId) {
        return db()->select('*')
            ->from(':instapaint_partial_order')
            ->where(['user_id' => $userId])
            ->executeRow();
    }

    public function clearPartialOrder($userId) {
        return db()->delete(':instapaint_partial_order',['user_id' => $userId]);
    }

}

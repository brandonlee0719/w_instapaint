<?php

namespace Apps\Instapaint\Service;

use upload;

class GiftCards extends \Phpfox_Service
{

    /**
     * @return array Prices in cents for each card value
     */
    public function getPrices() {
        return [
            50 => 5000,
            75 => 7500,
            125 => 12000,
            200 => 18000,
            300 => 27000,
            500 => 46000,
            750 => 70000,
            1000 => 90000
        ];
    }

    /**
     * @return false|string Json of gift card values and prices
     */
    public function getPricesJson() {
        return json_encode($this->getPrices());
    }

    /**
     * Save purchase in DB
     *
     * @param $cardValue
     * @param $cardPrice
     * @param $clientName
     * @param $clientEmail
     * @param $recipientName
     * @param $recipientEmail
     * @param $stripeCustomerId
     * @param $stripeChargeId
     * @return int
     */
    public function savePurchase(
        $cardValue,
        $cardPrice,
        $clientName,
        $clientEmail,
        $recipientName,
        $recipientEmail,
        $stripeCustomerId,
        $stripeChargeId
    ) {
        return db()->insert(':instapaint_gift_card_purchase', [
            'gift_card_value' => (float) $cardValue,
            'gift_card_price' => (float) $cardPrice,
            'client_name' => $clientName,
            'client_email' => $clientEmail,
            'recipient_name' => $recipientName,
            'recipient_email' => $recipientEmail,
            'stripe_customer_id' => $stripeCustomerId,
            'stripe_charge_id' => $stripeChargeId,
            'purchase_timestamp' => time()
        ]);
    }
}

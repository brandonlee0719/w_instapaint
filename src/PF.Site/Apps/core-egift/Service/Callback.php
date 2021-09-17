<?php


namespace Apps\Core_eGifts\Service;

use Phpfox_Service;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

class Callback extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
    }

    /**
     * Handles API callback for payment gateways.
     *
     * @param array $aParams ARRAY of params passed from the payment gateway after a payment has been made.
     *
     * @return bool|null FALSE if payment is not valid|Nothing returned if everything went well.
     */
    public function paymentApiCallback($aParams)
    {
        Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
        Phpfox::log('Attempting to retrieve purchase from the database');

        define('PHPFOX_API_CALLBACK', true); // used to override security checks in the processes
        // we get the sponsored ad
        $iId = preg_replace("/[^0-9]/", '', $aParams['item_number']);
        $aInvoice = Phpfox::getService('egift')->getEgiftInvoice((int)$iId);
        if (empty($aInvoice)) {
            Phpfox::log('egift not found.');
            return false;
        }

        Phpfox::log('Found the invoice.');
        Phpfox::log('Purchase seems valid: ' . var_export($aInvoice, true));

        if ($aParams['status'] == 'completed') {
            if ($aParams['total_paid'] == $aInvoice['price']) {
                Phpfox::log('Paid correct price');
            } else {
                Phpfox::log('Paid incorrect price');
                return false;
            }
        } else {
            Phpfox::log('Payment is not marked as "completed".');
            return false;
        }

        Phpfox::log('Handling purchase');

        db()->update(Phpfox::getT('egift_invoice'), [
            'status' => $aParams['status'],
            'time_stamp_paid' => PHPFOX_TIME
        ], 'invoice_id = ' . $aInvoice['invoice_id']);

        Phpfox::getService('egift.process')->sendNotification($aInvoice);


        Phpfox::log('Handling complete');
        return null;
    }

    /**
     * @param $aRow
     * @return array
     */
    public function getNotificationSend($aRow)
    {
        $aNotification = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name')
            ->from(Phpfox::getT('feed_comment'), 'fc')
            ->join(Phpfox::getT('feed'), 'f', 'f.item_id = fc.feed_comment_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.parent_user_id')
            ->where('f.feed_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        $sType = 'comment-id';

        if (empty($aNotification) || !isset($aNotification['user_id'])) {
            return array();
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aRow);
        return array(
            'message' => _p('user_sent_egift_notification_send', array('user_name' => $sUsers)),
            'link' => Phpfox::getLib('url')->makeUrl($aNotification['user_name'],
                array($sType => $aNotification['feed_comment_id'])),
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('egift.service_callback__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

<?php


namespace Apps\Core_eGifts\Service;

use Phpfox_Service;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox;

class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('egift');
    }

    /**
     * Creates an invoice for a non-free egift
     *
     * @param int $iRefId in v3 this is the feed_id
     * @param int $iUserTo the user that will receive the egift
     * @param array $aEgift the egift to send
     *
     * @return mixed
     */
    public function addInvoice($iRefId, $iUserTo, $aEgift)
    {
        $iPrice = $aEgift['price'][Phpfox::getService('user')->getCurrency()];
        $bIsFree = ($iPrice == 0);

        $aInsert = [
            'user_from' => Phpfox::getUserId(),
            'user_to' => $iUserTo,
            'egift_id' => $aEgift['egift_id'],
            'feed_id' => $iRefId,
            'currency_id' => Phpfox::getService('user')->getCurrency(),
            'price' => $iPrice,
            'time_stamp_created' => PHPFOX_TIME,
            'status' => 'pending'
        ];

        if ($bIsFree) {
            $aInsert['status'] = 'completed';
            $aInsert['time_stamp_paid'] = PHPFOX_TIME;
        }

        // If sending in the birthday or user
        $aReceivedUser = Phpfox::getService('user')->getUser($iUserTo);
        if ($aReceivedUser['birthday_search'] && (date('d/m', $aReceivedUser['birthday_search']) == date('d/m',
                    time()))) {
            $aInsert['birthday_id'] = db()->insert(Phpfox::getT('friend_birthday'), array(
                'birthday_user_sender' => Phpfox::getUserId(),
                'birthday_user_receiver' => $iUserTo,
                'birthday_message' => $aEgift['message'],
                'time_stamp' => PHPFOX_TIME,
                'egift_id' => $aEgift['egift_id'],
                'status_id' => 0
            ));
        } else {
            $aInsert['birthday_id'] = 0;
        }

        /* Create an invoice*/
        $iInvoice = db()->insert(Phpfox::getT('egift_invoice'), $aInsert);

        return $iInvoice;
    }

    /**
     * Adds a gift
     *
     * @param array $aVals
     * @return bool
     */
    public function addGift(array $aVals)
    {
        (($sPlugin = Phpfox_Plugin::get('egift.service_process_addgift__start')) ? eval($sPlugin) : false);

        if (!isset($aVals['category'])) {
            $aVals['category'] = 0;
        }

        // Prepare params for inserting into database
        $aInsert = array(
            'category_id' => (int)$aVals['category'],
            'user_id' => Phpfox::getUserId(),
            'time_stamp' => PHPFOX_TIME,
            'title' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255),
            'price' => serialize($aVals['currency'])
        );

        $iId = db()->insert(Phpfox::getT('egift'), $aInsert);

        // Process image for egift
        if (!empty($aVals['size']) && $aVals['error'] == UPLOAD_ERR_OK) {
            $this->_processImages($iId);
        }

        $this->cache()->remove('egift_item');
        (($sPlugin = Phpfox_Plugin::get('egift.service_process_addgift__end')) ? eval($sPlugin) : false);
        return $iId;
    }

    /**
     * Updating a exits egift
     *
     * @param $iId
     * @param $aVals
     * @return bool|resource
     */
    public function updateGift($iId, $aVals)
    {
        (($sPlugin = Phpfox_Plugin::get('egift.service_process_updategift__start')) ? eval($sPlugin) : false);

        if (!isset($aVals['category'])) {
            $aVals['category'] = 0;
        }

        // Prepare params for updating
        $aUpdate = array(
            'category_id' => (int)$aVals['category'],
            'title' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255),
            'price' => serialize($aVals['currency'])
        );

        $bSuccess = db()->update($this->_sTable, $aUpdate, 'egift_id = ' . $iId);

        // Process image for egift
        if ($bSuccess && !empty($aVals['size']) && $aVals['error'] == UPLOAD_ERR_OK) {
            $this->_processImages($iId);
        }

        $this->cache()->remove('egift_item');
        (($sPlugin = Phpfox_Plugin::get('egift.service_process_updategift__end')) ? eval($sPlugin) : false);
        return $bSuccess;
    }

    /**
     * This function deletes an egift and the images that it uses.
     *
     * @param int $iId `phpfox_egift`.`egift_id`
     *
     * @return bool Success?
     */
    public function deleteGift($iId)
    {
        $aGift = Phpfox::getService('egift')->getEgift($iId);

        //Remove image
        $oFile = Phpfox::getLib('file');
        $sEgiftImage = $aGift['file_path'];
        $sPicStorage = Phpfox::getParam('egift.dir_egift');
        if (file_exists($sPicStorage . sprintf($sEgiftImage, '_75_square'))) {
            $oFile->unlink($sPicStorage . sprintf($sEgiftImage, '_75_square'));
        }
        if (file_exists($sPicStorage . sprintf($sEgiftImage, ''))) {
            $oFile->unlink($sPicStorage . sprintf($sEgiftImage, ''));
        }

        // Delete
        db()->delete(Phpfox::getT('egift'), 'egift_id = ' . (int)$iId);
        $this->cache()->remove('egift_item');
        return true;
    }

    /**
     * Process image and save to egift entry
     *
     * @param $iItemId
     */
    private function _processImages($iItemId)
    {
        $aType = ['jpg', 'gif', 'png', 'jpeg'];
        $oFile = Phpfox::getLib('file');
        $oImage = Phpfox::getLib('image');
        $sPicStorage = Phpfox::getParam('egift.dir_egift');

        if ($iItemId) {
            $sEgiftImage = db()
                ->select('file_path')
                ->from($this->_sTable)
                ->where('egift_id = ' . $iItemId)
                ->execute('getField');
        }

        if (!empty($sEgiftImage)) {
            if (file_exists($sPicStorage . sprintf($sEgiftImage, '_75_square'))) {
                $oFile->unlink($sPicStorage . sprintf($sEgiftImage, '_75_square'));
            }

            if (file_exists($sPicStorage . sprintf($sEgiftImage, '_120'))) {
                $oFile->unlink($sPicStorage . sprintf($sEgiftImage, '_120'));
            }
        }

        // If dir not exist then create it
        if (!is_dir($sPicStorage)) {
            @mkdir($sPicStorage, 0777, 1);
            @chmod($sPicStorage, 0777);
        }

        // Load image for create new
        if ($aImage = $oFile->load('file', $aType)) {
            $sFileName = $oFile->upload('file', $sPicStorage, uniqid());

            // Generate for many sizes for multi purpose
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''),
                $sPicStorage . sprintf($sFileName, '_75_square'), 75, 75, false);
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_120'),
                120, 120, false);

            db()->update($this->_sTable, array(
                'file_path' => $sFileName,
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            ), 'egift_id = ' . $iItemId);
        }
    }

    /**************************************************************************************************************************/
    /*============================= OLD FUNCTIONS SECTION (SHOULD NOT USE AND SHOULD BE REMOVED) =============================*/
    /**************************************************************************************************************************/

    /**
     * This function stores categories in the database and clears cache. It only stores language phrases so
     * if a category is going to be added it will create a language phrase for it.
     *
     * @param $aVals
     * @return int
     */
    public function addCategory($aVals)
    {
        return Phpfox::getService('egift.category.process')->addCategory($aVals);
    }

    /**
     * This function deletes a category and the language phrases associated with it.
     *
     * @param int $iId
     *
     * @return bool true on success, Phpfox_Error otherwise
     */
    public function deleteCategory($iId)
    {
        return Phpfox::getService('egift.category.process')->deleteCategory($iId, array('delete_type' => 1));
    }

    /**
     * This function updates language phrases that belong to a category for egifts.
     *
     * @param array $aVal
     *
     * @return boolean Success?
     */
    public function editCategory($aVal)
    {
        if (!is_array($aVal['edit'])) {
            return Phpfox_Error::set(_p('wrong_format_to_edit_a_phrase_dot'));
        }

        $sDefault = Phpfox::getService('language')->getDefaultLanguage();
        foreach ($aVal['edit'] as $sLanguage => $iCategory) {
            $sValue = reset($iCategory);
            $iCategory = array_keys($iCategory);
            $iCategory = reset($iCategory);

            $aCategory = Phpfox::getService('egift.category')->getCategoryById($iCategory);
            if (empty($aCategory)) {
                return Phpfox_Error::set(_p('that_category_doesnt_exist'));
            }

            if ($sLanguage == $sDefault && empty($sValue)) {
                Phpfox_Error::set(_p('category_name_is_required'));
                return false;
            }

            if (empty($sValue)) {
                $sValue = $aVal['edit'][$sDefault][$iCategory];
            }

            $this->database()
                ->update(Phpfox::getT('language_phrase'), ['text' => $sValue],
                    'language_id = "' . Phpfox::getLib('parse.input')
                        ->clean($sLanguage) . '" AND var_name = "' . $aCategory['phrase'] . '"');
        }
        foreach ($aVal['dates'] as $iId => $aDates) {
            if (!isset($aDates['do_schedule']) || $aDates['do_schedule'] != true) {
                $iStart = null;
                $iEnd = null;
            } else {
                $iStart = mktime(0, 0, 0,
                    (isset($aDates['start_month']) && !empty($aDates['start_month']) ? (int)$aDates['start_month'] : date('m')),
                    (isset($aDates['start_day']) && !empty($aDates['start_day']) ? (int)$aDates['start_day'] : date('d')),
                    (isset($aDates['start_year']) && !empty($aDates['start_year']) ? (int)$aDates['start_year'] : date('Y')));
                $iEnd = mktime(23, 59, 59,
                    (isset($aDates['end_month']) && !empty($aDates['end_month']) ? (int)$aDates['end_month'] : date('m')),
                    (isset($aDates['end_day']) && !empty($aDates['end_day']) ? (int)$aDates['end_day'] : date('d')),
                    (isset($aDates['end_year']) && !empty($aDates['end_year']) ? (int)$aDates['end_year'] : date('Y')));
            }
            $this->database()->update(Phpfox::getT('egift_category'), [
                'time_start' => $iStart,
                'time_end' => $iEnd
            ], 'category_id = ' . (int)$iId);
        }

        $this->cache()->remove();

        return true;
    }

    /**
     * Sets the order for the categories. This affects only the categories.
     *
     * @param array $aVals The keys in this array are the category_ids and the values the order to set
     *
     * @return void
     */
    public function setOrder($aVals)
    {
        $iCnt = 0;
        foreach ($aVals as $mKey => $mOrdering) {
            $iCnt++;

            $this->database()
                ->update(Phpfox::getT('egift_category'), ['ordering' => $iCnt], 'category_id = ' . $this->database()
                        ->escape($mKey) . '');
        }
        $this->cache()->remove('egift_category');
    }

    /**
     * @param $aInvoice
     */
    public function sendNotification($aInvoice)
    {
        // If we send in the birthday time
        if ($aInvoice['birthday_id']) {
            db()->update(Phpfox::getT('friend_birthday'), [
                'status_id' => '1'
            ], 'birthday_id = ' . $aInvoice['birthday_id']);

            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('friend_birthday', $aInvoice['birthday_id'],
                    $aInvoice['user_to'], $aInvoice['user_from']);
            }
            $sLink = Phpfox::getLib('url')->makeUrl('friend.mybirthday', array('id' => $aInvoice['birthday_id']));
            $sFullName = db()->select('full_name')->from(Phpfox::getT('user'))
                ->where('user_id = ' . (int)$aInvoice['user_from'])
                ->execute('getSlaveField');

            Phpfox::getLib('mail')->to($aInvoice['user_to'])
                ->subject(array(
                    'friend.full_name_wishes_you_a_happy_birthday_on_site_title',
                    array('full_name' => $sFullName, 'site_title' => Phpfox::getParam('core.site_title'))
                ))
                ->message(array(
                    'friend.full_name_wrote_to_congratulate_you_on_your_birthday_on_site_title',
                    array(
                        'full_name' => $sFullName,
                        'site_title' => Phpfox::getParam('core.site_title'),
                        'link' => $sLink
                    )
                ))
                ->notification('friend.receive_new_birthday')
                ->send();
        }

        Phpfox::getService('notification.process')->add('egift_send', $aInvoice['feed_id'],
            $aInvoice['user_to'], $aInvoice['user_from']);
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
        if ($sPlugin = Phpfox_Plugin::get('egift.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
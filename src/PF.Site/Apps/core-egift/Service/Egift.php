<?php


namespace Apps\Core_eGifts\Service;

use Phpfox_Service;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox;

class Egift extends Phpfox_Service
{
    /**
     * @var array
     */
    private $_aSizes;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('egift');
        /* This array holds the sizes for the thumbnails of the egifts */
        $this->_aSizes = [120];
    }

    /**
     * @return array
     */
    public function getSizes()
    {
        return $this->_aSizes;
    }

    /***
     * This function returns every egift available
     *
     * @param int $iCategoryId
     * @param int $iPage
     * @param int $iLimit
     * @param int $iCount
     * @return array|int|string
     */
    public function getEgifts($iCategoryId = 0, $iPage = 0, $iLimit = 10, &$iCount = 0)
    {
        $sWhere = '1';
        $sOrder = 'time_stamp DESC';

        // Query in a category
        if ($iCategoryId) {
            $sWhere = 'e.category_id = ' . $iCategoryId;
            $sOrder = 'ordering ASC';
        }

        $sCacheId = $this->cache()->set('egift_total_' . $iCategoryId);
        if (!($iCount = $this->cache()->get($sCacheId))) {
            $iCount = db()->select('COUNT(*)')
                ->join(Phpfox::getT('egift_category'), 'ec', 'ec.category_id = e.category_id')
                ->from($this->_sTable, 'e')
                ->where($sWhere)
                ->executeField();

            $this->cache()->save($sCacheId, $iCount);
        }

        $aEgifts = db()->select('e.*, ec.phrase as category_name')
            ->join(Phpfox::getT('egift_category'), 'ec', 'ec.category_id = e.category_id')
            ->from($this->_sTable, 'e')
            ->where($sWhere)
            ->limit($iPage, $iLimit)
            ->order($sOrder)
            ->execute('getSlaveRows');

        foreach ($aEgifts as &$aGift) {
            $aGift['category_name'] = _p($aGift['category_name']);
            $aGift['currency_id'] = Phpfox::getService('user')->getCurrency();
            $aGift['currency'] = unserialize($aGift['price']);
            $aGift['price'] = $aGift['currency'][$aGift['currency_id']];
        }
        return $aEgifts;
    }

    /**
     * Gets only one gift
     *
     * @param int $iId
     *
     * @return array
     */
    public function getEgift($iId)
    {
        $aRow = db()->select('*')->from($this->_sTable)->where('egift_id = ' . $iId)->executeRow();
        if (!empty($aRow)) {
            $aRow['price'] = unserialize($aRow['price']);

            if (!empty($aRow['file_path'])) {
                $aRow['current_image'] = Phpfox::getLib('image.helper')->display(array(
                    'path' => 'egift.url_egif',
                    'server_id' => $aRow['server_id'],
                    'title' => $aRow['title'],
                    'file' => $aRow['file_path'],
                    'suffix' => '_120',
                    'return_url' => true
                ));
            }
        }
        return $aRow;
    }

    /**
     * Gets the invoice for when processing the callback from paypal
     *
     * @param int $iId
     *
     * @return array
     */
    public function getEgiftInvoice($iId)
    {
        return db()
            ->select('*')
            ->from(Phpfox::getT('egift_invoice'))
            ->where('invoice_id = ' . (int)$iId)
            ->execute('getSlaveRow');
    }

    /**
     * This function returns an array with all the e-cards that this user has sent,
     * in cronological order (newer first), with their invoice if available
     *
     * @param int $iUser
     *
     * @return array
     */
    public function getSentEcards($iUser)
    {
        $aAll = db()
            ->select('*, ei.status AS invoice_status, (CASE WHEN ei.time_stamp_paid IS NULL THEN ei.time_stamp_created ELSE ei.time_stamp_paid END) AS time_stamp')
            ->from(Phpfox::getT('egift_invoice'), 'ei')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ei.user_to')
            ->where('ei.user_from = ' . (int)$iUser)
            ->execute('getSlaveRows');

        return $aAll;
    }

    /**
     * This function is similar to getSentEcards except this one is tailored to the adminCP
     * the information it gets is different, allows more filters and groups information differently.
     * Its purpsoe is to get information about invoices
     */
    public function getInvoices()
    {
        $aInvoices = db()
            ->select('ei.*, ' . Phpfox::getUserField('userTo', 'to_') . ',' . Phpfox::getUserField('userFrom', 'from_'))
            ->from(Phpfox::getT('egift_invoice'), 'ei')
            ->join(Phpfox::getT('user'), 'userTo', 'userTo.user_id = ei.user_to')
            ->join(Phpfox::getT('user'), 'userFrom', 'userFrom.user_id = ei.user_from')
            ->order('ei.invoice_id DESC')
            ->execute('getSlaveRows');

        foreach ($aInvoices as $sKey => $aInvoice) {
            $aInvoices[$sKey]['from'] = [];
            $aInvoices[$sKey]['to'] = [];
            foreach ($aInvoice as $sField => $sValue) {
                if (strpos($sField, 'from_') !== false) {
                    $aInvoices[$sKey]['from'][str_replace('from_', '', $sField)] = $sValue;
                    unset($aInvoices[$sKey][$sField]);
                }
                if (strpos($sField, 'to_') !== false) {
                    $aInvoices[$sKey]['to'][str_replace('to_', '', $sField)] = $sValue;
                    unset($aInvoices[$sKey][$sField]);
                }

            }
        }

        return $aInvoices;
    }

    public function getReceivedGifts($iUserId, $iLimit = 9)
    {
        $sSelect = db()->select('egift_id, MAX(time_stamp_paid) as time_stamp_paid')
            ->from(':egift_invoice')
            ->where('status = \'completed\' AND user_to = ' . $iUserId)
            ->group('egift_id')
            ->limit($iLimit)
            ->execute();

        $aRows = db()->select('*')
            ->from($this->_sTable, 'e')
            ->join("({$sSelect})", 'ei', 'ei.egift_id = e.egift_id')
            ->order('ei.time_stamp_paid DESC')
            ->executeRows();

        return $aRows;
    }

    /**************************************************************************************************************************/
    /*============================= OLD FUNCTIONS SECTION (SHOULD NOT USE AND SHOULD BE REMOVED) =============================*/
    /**************************************************************************************************************************/

    /**
     * Getter function that returns a list of categories.
     *
     * @param bool $bHideEmpty Should we remove empty categories?
     *
     * @return array
     */
    public function getCategories($bHideEmpty = false)
    {
        $aCategories = db()
            ->select('ec.*, eg.egift_id')
            ->leftJoin(Phpfox::getT('egift'), 'eg', 'eg.category_id=ec.category_id')
            ->group('ec.category_id', true)
            ->order('ec.ordering ASC')
            ->from($this->_sTable, 'ec')
            ->execute('getSlaveRows');

        if (!is_array($aCategories)) {
            $aCategories = array();
        }
        foreach ($aCategories as $iKey => $aCat) {
            $aPhraseIds = db()->select('phrase_id, language_id, text')
                ->from(Phpfox::getT('language_phrase'))
                ->where('var_name = "' . str_replace('egift.', '', $aCat['phrase']) . '"')
                ->execute('getSlaveRows');
            foreach ($aPhraseIds as $aPhrase) {
                $aCategories[$iKey]['phrase_ids'][$aPhrase['language_id']] = array(
                    'phrase_id' => $aPhrase['phrase_id'],
                    'text' => $aPhrase['text']
                );
                /* Not needed anymore but lets leave it for safety */
                if (preg_match('/\{phrase var=/', $aPhrase['text'])) {
                    $aCategories[$iKey]['phrase_ids'][$aPhrase['language_id']]['text'] = str_replace(array(
                        '{phrase var=',
                        '\'',
                        '"',
                        '}'
                    ), '', $aPhrase['text']);
                }
            }
        }
        if (!is_array($aCategories)) {
            $aCategories = array();
        }
        if ($bHideEmpty) {
            foreach ($aCategories as $iKey => $aCat) {
                if (!isset($aCat['egift_id']) || empty($aCat['egift_id'])) {
                    unset($aCategories[$iKey]);
                }
            }
        }
        /* Now we make sure there is at least an empty string for the missing languages */
        return $aCategories;
    }

    /**
     * Gets one single category
     *
     * @param int $iId
     *
     * @return array
     */
    public function getCategoryById($iId)
    {
        return Phpfox::getService('egift.category')->getCategoryById($iId);
    }

    /**
     * This function returns the cost of an egift in the currency specified by the user
     *
     * @param int $iEgift egift_id
     *
     * @return int|float price
     */
    public function getCost($iEgift)
    {
        $aGift = $this->getForEdit($iEgift);
        if (isset($aGift['price'][Phpfox::getService('user')->getCurrency()])) {
            return $aGift['price'][Phpfox::getService('user')->getCurrency()];
        }
        return 0;
    }

    /**
     * Gets a gift for editing. It accepts an optional second param to get the
     * gift from there.
     * Careful, this function serves very specific purposes.
     *
     * @param int $iEdit
     * @return array|bool
     */
    public function getForEdit($iEdit)
    {
        return $this->getEgift($iEdit);
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
        if ($sPlugin = Phpfox_Plugin::get('egift.service_egift__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

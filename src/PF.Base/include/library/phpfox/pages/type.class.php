<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox_Service
 */
abstract class Phpfox_Pages_Type extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('pages_type');
    }

    /**
     * @return Phpfox_Pages_Facade
     */
    abstract public function getFacade();

    public function getById($iId)
    {
        static $aRows = array();

        if (isset($aRows[$iId])) {
            return $aRows[$iId];
        }

        $aRows[$iId] = $this->database()->select('*')
            ->from(Phpfox::getT('pages_type'))
            ->where('type_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aRows[$iId]['type_id'])) {
            return false;
        }

        return $aRows[$iId];
    }

    /**
     * Get page types
     * @param int $iCacheTime , default 5
     * @return array
     */
    public function get($iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set($this->getFacade()->getItemType() . '_types');

        if (!($aRows = $this->cache()->get($sCacheId, $iCacheTime))) {
            $aRows = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('is_active = 1 AND item_type = ' . $this->getFacade()->getItemTypeId())
                ->order('ordering ASC')
                ->execute('getSlaveRows');

            foreach ($aRows as $iKey => $aRow) {
                $aRows[$iKey]['pages_count'] = db()->select('count(*)')
                    ->from(':pages')
                    ->where(['type_id' => $aRow['type_id']])
                    ->executeField();
                $aRows[$iKey]['category_id'] = $aRow['type_id'];
                $aRows[$iKey]['url'] = Phpfox::permalink($this->getFacade()->getItemType() . '.category',
                    $aRow['type_id'], $aRow['name']);
                $aRows[$iKey]['categories'] = $aRows[$iKey]['sub'] = $this->getFacade()->getCategory()->getByTypeId($aRow['type_id']);

                if (isset($aRow['image_path']) && strpos($aRow['image_path'], 'default-category') === false) {
                    $aRows[$iKey]['image_path'] = sprintf($aRow['image_path'], '_200');
                }
            }

            $this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group($this->getFacade()->getItemType(), $sCacheId);
        }

        return $aRows;
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('*')
            ->from(Phpfox::getT('pages_type'))
            ->where('type_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        //Support legacy phrases
        if (substr($aRow['name'], 0, 7) == '{phrase' && substr($aRow['name'], -1) == '}') {
            $aRow['name'] = preg_replace('/\s+/', ' ', $aRow['name']);
            $aRow['name'] = str_replace([
                "{phrase var='",
                "{phrase var=\"",
                "'}",
                "\"}"
            ], "", $aRow['name']);
        }//End support legacy
        $aLanguages = Phpfox::getService('language')->getAll();
        foreach ($aLanguages as $aLanguage) {
            $sPhraseValue = (Core\Lib::phrase()->isPhrase($aRow['name'])) ? _p($aRow['name'], [],
                $aLanguage['language_id']) : $aRow['name'];
            $aRow['name_' . $aLanguage['language_id']] = $sPhraseValue;
        }
        if (!isset($aRow['type_id'])) {
            return false;
        }

        return $aRow;
    }

    public function getForAdmin($bGetSub = true)
    {
        $aRows = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('item_type = ' . $this->getFacade()->getItemTypeId())
            ->order('ordering ASC')
            ->execute('getSlaveRows');
        foreach ($aRows as $iKey => $aRow) {
            if ($bGetSub) {
                $aRows[$iKey]['categories'] = $this->getFacade()->getCategory()->getForAdmin($aRow['type_id']);
            }
            if (isset($aRow['image_path'])) {
                $aRows[$iKey]['image_path'] = sprintf($aRow['image_path'], '_200');
            }
        }

        return $aRows;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_type_type__call')) {
            eval($sPlugin);

            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * Delete first level category image
     * @param $iTypeId
     */
    public function deleteImage($iTypeId)
    {
        $aSizes = ['', '_50', '_120', '_200'];
        $aImage = db()->select('image_server_id, image_path')->from($this->_sTable)->where(['type_id' => $iTypeId])->executeRow();

        if (!isset($aImage['image_path']) || !$aImage['image_path']) {
            return;
        }

        // delete image using cdn
        $iFileSizes = 0;
        foreach ($aSizes as $sSize) {
            $sFilePath = Phpfox::getParam('pages.dir_image') . sprintf($aImage['image_path'], $sSize);
            file_exists($sFilePath) && $iFileSizes += filesize($sFilePath);
            if ($aImage['image_server_id']) {
                !file_exists($sFilePath) && $iFileSizes += $this->_getRemoteFileSize(Phpfox::getLib('cdn')->getUrl($sFilePath,
                    $aImage['image_server_id']));
                Phpfox::getLib('cdn')->remove($sFilePath);
            }
            @unlink($sFilePath);
        }

        // update user space
        $iFileSizes && Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'pages', $iFileSizes, '-');

        // update database
        db()->update($this->_sTable, [
            'image_server_id' => 0,
            'image_path' => null
        ], ['type_id' => $iTypeId]);
    }

    /**
     * Get remote file size
     * @param $sUrl string remote file url
     * @return integer
     */
    private function _getRemoteFileSize($sUrl)
    {
        $ch = curl_init($sUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);

        return $size;
    }
}

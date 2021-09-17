<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 */
class Core_Service_Temp_File extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('temp_file');
    }

    /**
     * @param array $aParams
     * @return int
     */
    public function add($aParams = []) {
        $aParams = array_merge([
            'user_id' => Phpfox::getUserId(),
            'time_stamp' => PHPFOX_TIME
        ], $aParams);

        return db()->insert($this->_sTable, $aParams);
    }

    /**
     * @param int $iId
     * @return array|int|string
     */
    public function get($iId = 0) {
        if (empty($iId)) {
            return [];
        }

        return db()->select('*')->from($this->_sTable)->where('file_id = ' . $iId)->execute('getSlaveRow');
    }

    /**
     * @param int $iId
     * @param bool $bDeleteFile
     * @return bool
     */
    public function delete($iId = 0, $bDeleteFile = false) {
        if (empty($iId)) {
            return false;
        }

        if ($bDeleteFile) {
            $aFile  = $this->get($iId);
            if (empty($aFile)) {
                return false;
            }

            if (!Phpfox::hasCallback($aFile['type'], 'getUploadParams')) {
                return false;
            }

            $aParams = [
                'upload_dir' => Phpfox::getParam('core.dir_pic'),
                'upload_url' => Phpfox::getParam('core.url_pic'),
                'thumbnail_sizes' => []

            ];

            $aParams = array_merge($aParams, Phpfox::callback($aFile['type'] . '.getUploadParams'), $aFile);
            $aParams['update_space'] = false;
            Phpfox::getService('user.file')->remove($aParams);

        }

        return db()->delete($this->_sTable, 'file_id = ' . $iId);
    }

    /**
     * @description: clean unused temp files
     * @return bool
     */
    public function clean() {
        $iTime = PHPFOX_TIME - 60*60;
        $aRows = db()->select('file_id')->from($this->_sTable)->where('time_stamp < ' . $iTime)->order('file_id ASC')->limit(100)->execute('getSlaveRows');
        foreach ($aRows as $aRow) {
            $this->delete($aRow['file_id'], true);
        }
        return true;
    }
}

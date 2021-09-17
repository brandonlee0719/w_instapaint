<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Service_Store_Verify extends Phpfox_Service
{
    
    /**
     * @param $aVals
     */
    public function updateSetting($aVals)
    {
        $aSettingList = [
            'method'    => 'file_upload_method',
            'host_name' => 'ftp_host_name',
            'port'      => 'ftp_port',
            'user_name' => 'ftp_user_name',
            'password'  => 'ftp_password',
        ];
        foreach ($aSettingList as $sKey => $sValue) {
            if (isset($aVals[$sKey])) {
                $aParam = [
                    'value' => [
                        $sValue => $aVals[$sKey],
                    ]
                ];
                Phpfox::getService('admincp.setting.process')->update($aParam);
            }
        }
    }
    
    /**
     * @param string $app_id
     *
     * @return array
     */
    public function verifyApp($app_id)
    {
        $aTempFiles = $this->scanFiles(PHPFOX_DIR_FILE . 'temp' . PHPFOX_DS . $app_id . PHPFOX_DS);
        $aAppFiles = $this->scanFiles(PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $app_id);
        $newFile = array_diff($aTempFiles, $aAppFiles);
        $removeFile = array_diff($aAppFiles, $aTempFiles);
        $override = array_intersect($aAppFiles, $aTempFiles);
        return [
            $newFile,
            $removeFile,
            $override
        ];
    }
    
    /**
     * @param string      $path
     * @param null|string $originalPath
     *
     * @return array
     */
    public function scanFiles($path, $originalPath = null)
    {
        if (!is_dir($path)) {
            return [];
        }
        
        if (!isset($originalPath)) {
            $originalPath = $path;
        }
        $ffs = scandir($path);
        $listFiles = [];
        foreach ($ffs as $ff) {
            if ($ff != '.' && $ff != '..') {
                if (is_dir($path . PHPFOX_DS . $ff)) {
                    $sub = $this->scanFiles($path . PHPFOX_DS . $ff, $originalPath);
                    $listFiles = array_merge($listFiles, $sub);
                } else {
                    $listFiles[] = trim((str_replace($originalPath, '', $path)) . PHPFOX_DS . $ff, PHPFOX_DS);
                }
            }
        }
        return $listFiles;
    }
}
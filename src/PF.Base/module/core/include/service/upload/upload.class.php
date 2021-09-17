<?php

/**
 * Class Core_Service_Upload_Upload
 * @author Neil
 */
class Core_Service_Upload_Upload extends Phpfox_Service
{
    public function upload($aParams = [])
    {
        if (!isset($aParams['destination'])) {
            return false;
        }
        $iId = $this->database()->insert(':upload_temp',[
            'user_id' => Phpfox::getUserId(),
            'destination' => $aParams['destination'],
            'type' => $this->getFileType($aParams['destination']),
            'time_stamp' => PHPFOX_TIME
        ]);
        return $iId;
    }

    /**
     * @param $sDestination
     *
     * @return int 1:image|2:video|3:others
     */
    private function getFileType($sDestination)
    {
        //todo implement this method
        return 1;
    }

    //Remove old temp data if expired
    public function clean()
    {
        //todo implement this method
    }

    /**
     * Remove a temporary file
     *
     * @param int $iUploadId
     */
    public function remove($iUploadId)
    {
        //todo implement this method
    }

    public function ordering()
    {
        //todo order position of file
    }

    public function cleanSession($sSessionName)
    {
        //todo clean temp file from a session
    }
}
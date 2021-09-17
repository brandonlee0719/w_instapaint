<?php
namespace Apps\Core_Forums\Service\Subscribe;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');


class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('forum_subscribe');
    }

    /**
     * @param int $iThreadId
     * @param int $iUserId
     *
     * @return bool
     */
    public function add($iThreadId, $iUserId)
    {
        Phpfox::isUser(true);

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('thread_id = ' . (int)$iThreadId . ' AND user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');

        if ($iCnt) {
            return false;
        }

        $this->database()->insert($this->_sTable, [
            'thread_id' => (int)$iThreadId,
            'user_id' => (int)$iUserId
        ]);
        return true;
    }

    /**
     * @param int $iThreadId
     * @param int $iUserId
     *
     * @return void
     */
    public function delete($iThreadId, $iUserId)
    {
        $this->database()->delete($this->_sTable, 'thread_id = ' . (int)$iThreadId . ' AND user_id = ' . (int)$iUserId);
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
        if ($sPlugin = Phpfox_Plugin::get('forum.service_subscribe_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
<?php

namespace Apps\Core_Poke\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Phpfox_Service
 * @version        $Id: service.class.php 67 2009-01-20 11:32:45Z phpFox $
 */
class Process extends Phpfox_Service
{
    protected $_sTable;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('poke_data');
    }

    /**
     * This function sends a poke to $iUser from Phpfox::getUserId()
     * `status_id` tells if the poke has been seen:
     *            1: not seen
     *            2: seen
     * We have 2 cache files for this because they may have hundreds of pokes.
     *  The _ajax stores a smaller data set.
     * @param int $iUserId
     * @return boolean true if we added a poke.
     */
    public function sendPoke($iUserId)
    {
        if ($iUserId == Phpfox::getUserId()) {
            return false;
        }
        /* if the other user has not seen a poke then we do not add it */
        $iExists = db()->select('poke_id')
            ->from($this->_sTable)
            ->where('user_id = ' . Phpfox::getUserId() . ' AND to_user_id = ' . (int)$iUserId . ' AND status_id = ' . CORE_POKE_STATUS_POKING)
            ->execute('getSlaveField');
        if ($iExists > 0) {
            return false;
        }
        $iPokeId = db()->insert($this->_sTable, array(
            'user_id' => Phpfox::getUserId(),
            'to_user_id' => (int)$iUserId,
            'status_id' => CORE_POKE_STATUS_POKING
        ));

        /* Ignore all pokes from $iUserId to us */
        $this->ignore($iUserId);

        $this->cache()->remove('pokes_total_' . $iUserId);

        if (Phpfox::getParam('poke.add_to_feed') && Phpfox::isModule('feed')) {
            Phpfox::getService('feed.process')->add('poke', $iPokeId, 0, 0, 0, $iUserId);
        }
        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('poke', $iPokeId, (int)$iUserId, Phpfox::getUserId());
        }
        return true;
    }

    /**
     * Ignores (changes status => 2) a poke sent by $iUserId to Phpfox::getUserId()
     * @param int $iUserId
     */
    public function ignore($iUserId)
    {
        db()->update($this->_sTable,
            array('status_id' => CORE_POKE_STATUS_POKED),
            'user_id = ' . (int)$iUserId . ' AND to_user_id = ' . Phpfox::getUserId() . ' AND status_id = ' . CORE_POKE_STATUS_POKING);

        $this->cache()->remove('pokes_total_' . Phpfox::getUserId());
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
        if ($sPlugin = Phpfox_Plugin::get('poke.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);

        return false;
    }
}

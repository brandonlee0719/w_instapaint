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
class Poke extends Phpfox_Service
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
     * This function does the normal permissions checks and also checks the database
     * if the user has a pending poke it should not allow the current user to send
     * a new poke
     * @param integer $iUser
     * @return bool
     */
    public function canSendPoke($iUser)
    {
        /* If user cannot send pokes or can only send pokes to friends but $iUser is not a friend */
        $bDoNotHasSetting = !Phpfox::getUserParam('poke.can_poke');
        $bIsNotFriend = Phpfox::getUserParam('poke.can_only_poke_friends')
            && Phpfox::isModule('friend')
            && !Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $iUser);
        $bIsSelf = user()->id == $iUser;

        if ($bDoNotHasSetting || $bIsNotFriend || $bIsSelf) {
            return false;
        }

        /* if $iUser has a pending poke */
        $iExists = db()->select('poke_id')
            ->from($this->_sTable)
            ->where('user_id = ' . Phpfox::getUserId() . ' AND to_user_id = ' . (int)$iUser . ' AND status_id = ' . CORE_POKE_STATUS_POKING)
            ->execute('getSlaveField');

        return empty($iExists);
    }

    public function getTotalPokesForUser($iUserId)
    {
        $aConds = array(
            'to_user_id = ' . (int)$iUserId,
            'AND p.status_id = ' . CORE_POKE_STATUS_POKING
        );

        $sCacheId = $this->cache()->set('pokes_total_' . $iUserId);

        if (false === ($iCount = $this->cache()->get($sCacheId, 60))) {
            $iCount = db()->select('COUNT(u.user_id)')
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id AND u.profile_page_id = 0')
                ->where($aConds)
                ->executeField();

            $this->cache()->save($sCacheId, $iCount);
        }

        return $iCount;
    }

    public function getPokesForUser($iUserId, $iPage = 1, $iLimit = 5)
    {
        $aConds = array(
            'to_user_id = ' . (int)$iUserId,
            'AND p.status_id = ' . CORE_POKE_STATUS_POKING
        );

        $iCount = $this->getTotalPokesForUser($iUserId);

        $aPokes = db()->select('p.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id AND u.profile_page_id = 0')
            ->where($aConds)
            ->group('p.user_id', true)
            ->limit($iPage, $iLimit, $iCount)
            ->order('p.poke_id DESC')
            ->executeRows(true);

        return array($iCount, $aPokes);
    }

    public function getPokeData($iPokeId)
    {
        return  db()->select('uc.poke_id, uc.user_id')
            ->from(Phpfox::getT('poke_data'), 'uc')
            ->where('uc.poke_id = ' . (int)$iPokeId)
            ->execute('getSlaveRow');
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
        if ($sPlugin = Phpfox_Plugin::get('poke.service_poke__call')) {
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

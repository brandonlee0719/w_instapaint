<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Service_Process
 */
class Ban_Service_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ban');
    }

    /**
     * This function adds a ban filter, it was not renamed after 2.1 for compatibility
     * @param array $aVals
     * @param array $aBanFilter
     * @return bool
     * @throws Exception
     */
    public function add($aVals, &$aBanFilter = null)
    {
        Phpfox::isAdmin(true);

        $aForm = [
            'type_id' => [
                'type' => 'string:required'
            ],
            'find_value' => [
                'type' => 'string:required',
                'message' => _p('filter_value_is_required')
            ],
            'reason' => [
                'type' => 'string'
            ],
            'days_banned' => [
                'type' => 'int'
            ],
            'return_user_group' => [
                'type' => 'int'
            ],
            'bShow' => [
                'type' => 'string'
            ],
            // just to allow the input
            'user_groups_affected' => [
                'type' => 'array'
            ]
        ];

        if (isset($aVals['type_id']) && $aVals['type_id'] === 'email') {
            $aForm['find_value'] = [
                'type' => 'regex:email',
                'message' => _p('invalid_email')
            ];
        } else {
            $aForm['find_value'] = [
                'type' => 'string:required',
                'message' => _p('filter_value_is_required')
            ];
        }

        if ($aBanFilter !== null && isset($aBanFilter['replace'])) {
            $aForm['replacement'] = array(
                'type' => 'string:required',
                'message' => _p('filter_replacement_is_required')
            );
        }

        $aVals = $this->validator()->process($aForm, $aVals);

        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        if ($aVals['find_value'] == Phpfox::getIp()) {
            return Phpfox_Error::set(_p('you_cannot_ban_yourself_dot'));
        }

        $aVals['user_id'] = Phpfox::getUserId();
        $aVals['time_stamp'] = PHPFOX_TIME;
        $aVals['find_value'] = $this->preParse()->convert($aVals['find_value']);
        if ((isset($aVals['bShow']) && $aVals['bShow'] == '0') || !isset($aVals['bShow'])) {
            unset($aVals['reason']);
            unset($aVals['days_banned']);
            unset($aVals['return_user_group']);
        } else {
            $aVals['reason'] = !Phpfox_Locale::instance()->isPhrase($aVals['reason']) ? Phpfox::getLib('parse.input')->clean($aVals['reason']) : $aVals['reason'];
            $aVals['days_banned'] = (int)$aVals['days_banned'];
            $aVals['return_user_group'] = (int)$aVals['return_user_group'];
            if (!isset($aVals['user_groups_affected'])) {
                $aVals['user_groups_affected'] = array();
            }
            $aVals['user_groups_affected'] = serialize($aVals['user_groups_affected']);
        }
        unset($aVals['bShow']);
        if (isset($aVals['replacement'])) {
            $aVals['replacement'] = $this->preParse()->convert($aVals['replacement']);
        }
        if (empty($aVals['user_groups_affected'])) {
            $aVals['user_groups_affected'] = '';
        }
        $this->database()->insert($this->_sTable, $aVals);

        $this->cache()->removeGroup('ban');

        return true;
    }


    /**
     * This function places a ban on a user, it is a new functionality introduced in v2.1
     * It takes into account the number of the days the user will be banned, which user group
     * will the user return to after the ban expires and provides a reason for it.
     * This function reuses Phpfox::getService('user.process')->ban() as it already implements safety checks
     *
     * @param int $iUser user_id
     * @param int $iBanId `phpfox_ban`.`ban_id`
     * @param int $iDays
     * @param int $iUserGroup User group to return after ban expires
     * @param string $sReason can be a language phrase or plain text. Stored as mediumtext in DB, parsed if language phrase after
     * @return boolean
     */
    public function banUser($iUser, $iDays = 0, $iUserGroup = null, $sReason = null, $iBanId = null)
    {
        // sanity checks
        $iUser = (int)$iUser;
        $iDays = (int)$iDays;
        $iUserGroup = (int)$iUserGroup;
        $sReason = Phpfox::getLib('parse.input')->clean($sReason);
        $iBanId = ($iBanId != null) ? (int)$iBanId : null;

        define('PHPFOX_SKIP_BAN_ADMIN_CHECK', true);

        if ($iUser > 0 && $iUserGroup > 0 && ((is_int($iBanId) && $iBanId > 0) || $iBanId == null)) {
            if ((Phpfox::getService('user.process')->ban($iUser, true) == true)) {
                // always add a record
                $this->database()->insert(Phpfox::getT('ban_data'), array(
                    'ban_id' => $iBanId,
                    'user_id' => $iUser,
                    'start_time_stamp' => PHPFOX_TIME,
                    'end_time_stamp' => $iDays > 0 ? ($iDays * 86400) + PHPFOX_TIME : 0,
                    'return_user_group' => $iUserGroup,
                    'reason' => $sReason
                ));
            }
            return true;
        }
        return false;
    }

    /**
     * Delete a ban item
     * @param int $iDeleteId
     *
     * @return true
     */
    public function delete($iDeleteId)
    {
        Phpfox::isAdmin(true);
        $this->database()->delete($this->_sTable, 'ban_id = ' . (int)$iDeleteId);
        $this->cache()->removeGroup('ban');
        return true;
    }

    /**
     * Delete ban items by type and value
     * @param string $sType
     * @param string $sValue
     *
     * @return true
     */
    public function deleteByValue($sType, $sValue)
    {
        Phpfox::isAdmin(true);
        $this->database()->delete($this->_sTable,
            'type_id = \'' . $this->database()->escape($sType) . '\' AND find_value = \'' . $this->database()->escape($sValue) . '\'');
        $this->cache()->removeGroup('ban');
        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return  null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('ban.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

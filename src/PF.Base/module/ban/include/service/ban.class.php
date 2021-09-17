<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Service_Ban
 */
class Ban_Service_Ban extends Phpfox_Service
{
    /**
     * @var string
     */
    protected  $_sTable = '';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ban');
    }

    /**
     * @param string $sType
     *
     * @return array
     */
    public function getFilters($sType)
    {
        $sCacheId = $this->cache()->set('ban_type_' . $sType);

        if (false === ($aFilters = $this->cache()->get($sCacheId))) {
            $aFilters = $this->database()
                ->select('b.*, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'b')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
                ->where('b.type_id = \'' . $this->database()->escape($sType) . '\'')
                ->execute('getSlaveRows');

            foreach ($aFilters as $iKey => $aFilter) {
                if (!empty($aFilter['user_groups_affected'])) {
                    $aUserGroups = unserialize($aFilter['user_groups_affected']);
                    $aFilters[$iKey]['user_groups_affected'] = [];

                    $sWhere = '';
                    foreach ($aUserGroups as $iUserGroup) {
                        $sWhere .= 'user_group_id = ' . $iUserGroup . ' OR ';
                    }
                    $sWhere = rtrim($sWhere, ' OR ');
                    $aFilters[$iKey]['user_groups_affected'] = Phpfox::getService('user.group')->get($sWhere);
                }
            }
            $this->cache()->save($sCacheId, $aFilters);
            Phpfox::getLib('cache')->group('ban', $sCacheId);
        }
        return $aFilters;
    }

    /**
     * @param string $sType
     * @param string $sValue
     * @param bool $bGetReason
     *
     * @return bool
     */
    public function check($sType, $sValue, $bGetReason = false)
    {
        $sCacheId = $this->cache()->set('ban_' . $sType);

        if (!($aFilters = $this->cache()->get($sCacheId))) {
            $aRows = $this->database()->select('find_value, reason')
                ->from($this->_sTable)
                ->where('type_id = \'' . $this->database()->escape($sType) . '\'')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $aFilters[trim($aRow['find_value'])] = trim($aRow['reason']);
            }

            $this->cache()->save($sCacheId, $aFilters);
            Phpfox::getLib('cache')->group('ban', $sCacheId);
        }

        if ($sType == 'display_name') {
            $sValue = $this->preParse()->convert($sValue);
        }

        if (is_array($aFilters) && count($aFilters)) {
            foreach ($aFilters as $sFilter => $mValue) {
                $sFilter = str_replace('&#42;', '*', $sFilter);
                if ($sType == 'ip') {
                    $sFilter = preg_replace('%[^0-9.*]%', '', $sFilter);
                    if ($sFilter == '*') {
                        continue;
                    }
                }

                if (preg_match('/\*/i', $sFilter)) {
                    $sFilter = str_replace(array('.', '*'), array('\.', '(.*?)'), $sFilter);
                    if (preg_match('/http(s?):\/\//i', $sFilter)) {
                        $sFilter = str_replace('/', '\\/\\/', $sFilter);
                    }

                    if (preg_match('/^' . $sFilter . '$/i', $sValue)) {
                        return $bGetReason ? $mValue : false;
                    }
                } else {
                    if (preg_match('/^' . $sFilter . '$/i', $sValue, $aMatches)) {
                        return $bGetReason ? $mValue : false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * This function resembles $this->check but it also handles banning and is a more direct approach
     * and handles redirection and db insertion
     * This function is called in every Service as opposed to a Library mainly because there may be
     * cases where it becomes too restrictive
     * If the user groups affected is an empty array, it assumes that it affects every user group.
     * This function has been implemented in the following services
     *        - Blog.process (Add, update, updateBlogText, updateBlogTitle)
     *        - Comment.process (Add, updateText)
     *        - Event.process (Add, massEmail, update)
     *        - Forum.post.process (Add, update, updateText)
     *        - Forum.thread.process (Add, update)
     *        - Group.process (Add, update)
     *        - Mail.process (Add)
     *        - Marketplace.process (Add, update)
     *        - Music.process (upload)
     *        - Music.album.process (add, update)
     *        - Music.genre.process (add, update)
     *        - Music.song.process (setName)
     *        - Newsletter.process (add)
     *        - Page.process (add)
     *        - Photo.process (add)
     *        - Photo.album.process (add, updateTitle)
     *        - Photo.category.process (add)
     *        - Photo.tag.process (add)
     *        - Poll.process (add, updateAnswer)
     *        - Quiz.process (add, update)
     *        - User.process (updateStatus:2.1.0 RC1)
     * @param string $sValue
     * @return bool false on fail. In some situations it does'nt help echo'ing here (comment)
     */
    public function checkAutomaticBan($sValue)
    {
        /* Extra protection for admins so they don't get banned automatically. */
        if (Phpfox::isAdmin() || empty($sValue)) {
            return true;
        }
        if (is_array($sValue)) {
            $sValue = $this->_flatten($sValue);
        }
        $sCacheBanWord = $this->cache()->set('ban_work_filter');

        if (!$aFilters = $this->cache()->get($sCacheBanWord)) {
            $aFilters = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('type_id = "word"')
                ->execute('getSlaveRows');
            $this->cache()->save($sCacheBanWord, $aFilters);
            Phpfox::getLib('cache')->group('ban', $sCacheBanWord);
        }

        foreach ($aFilters as $iKey => $aFilter) {
            $aUserGroupsAffected = unserialize($aFilter['user_groups_affected']);

            if (is_array($aUserGroupsAffected) && !empty($aUserGroupsAffected) && in_array(Phpfox::getUserBy('user_group_id'),
                    $aUserGroupsAffected) == false) {
                continue;
            }

            $sFilter = '' . str_replace('&#42;', '*', $aFilter['find_value']) . '';
            $sFilter = str_replace("/", "\/", $sFilter);
            $sFilter = str_replace('&#42;', '*', $sFilter);

            if (preg_match('/\*/i', $sFilter)) {
                $sFilter = str_replace(array('.', '*'), array('\.', '(.*?)'), $sFilter);
                $bBan = preg_match('/' . $sFilter . '/is', $sValue);
            } else {
                $bBan = preg_match("/(\W)" . $sFilter . "(\W)/i", $sValue);
                if (!$bBan) {
                    $bBan = preg_match("/^" . $sFilter . "(\W)/i", $sValue);
                }
                if (!$bBan) {
                    $bBan = preg_match("/(\W)" . $sFilter . "$/i", $sValue);
                }
                if (!$bBan) {
                    $bBan = preg_match("/^" . $sFilter . "$/i", $sValue);
                }

            }

            if ($bBan) {
                if ($aFilter['days_banned'] === null) {
                    return true;
                }

                if (empty($aFilter['reason'])) {
                    $aFilter['reason'] = _p('You_are_banned_because_you_used_banned_word', ['word' => $sFilter]);
                }

                $this->database()->insert(Phpfox::getT('ban_data'), array(
                    'ban_id' => $aFilter['ban_id'],
                    'user_id' => Phpfox::getUserId(),
                    'start_time_stamp' => PHPFOX_TIME,
                    'end_time_stamp' => $aFilter['days_banned'] > 0 ? PHPFOX_TIME + ($aFilter['days_banned'] * 86400) : 0,
                    'return_user_group' => $aFilter['return_user_group'],
                    'reason' => $aFilter['reason']
                ));

                define('PHPFOX_USER_IS_BANNED', true);
                $aFilter['reason'] = str_replace('&#039;', "'", $aFilter['reason']);
                $sReason = preg_replace_callback('/\{phrase var=\'(.*)\'\}/is', function ($m) {
                    return "'' . _p('{$m[1]}',array(), '" . Phpfox::getUserBy('language_id') . "') . ''";
                }, $aFilter['reason']);

                $iUserGroupId = Phpfox::getParam('core.banned_user_group_id');
                if ($iUserGroupId == 0) {
                    $iUserGroupId = 5;
                }

                $this->database()->update(Phpfox::getT('user'), [
                        'user_group_id' => $iUserGroupId
                    ]
                    , 'user_id = ' . (int)Phpfox::getUserId());

                Phpfox::getService('user.auth')->logout();
                if (defined('PHPFOX_IS_AJAX') && PHPFOX_IS_AJAX) {
                    Phpfox::addMessage($sReason, 'danger', false);
                    echo 'window.location.reload(true);';
                } else {
                    Phpfox_Url::instance()->send('', array(), $sReason);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Simple function to recursively array_values. Used only with checkAutomaticBan
     * @param array|string $aArr
     * @return string
     */
    private function _flatten($aArr)
    {
        if (!is_array($aArr)) {
            return $aArr;
        }
        $sStr = '';
        foreach ($aArr as $aA) {
            $sStr .= $this->_flatten($aA) . ' ';
        }
        return $sStr;
    }

    /**
     * This function checks if $iUser is banned taking into account the user_group_id index and the ban_data table
     * @param array $aUser
     * @return array is_banned => bool, undefined|reason:string
     */
    public function isUserBanned($aUser = array())
    {
        $sCacheBanned = $this->cache()->set('ban_user_banned_' . md5(serialize($aUser)));

        if (!$aBanned = $this->cache()->get($sCacheBanned)) {
            $aBanned = $this->database()->select('*')
                ->from(Phpfox::getT('ban_data'))
                ->where('user_id = ' . ((!isset($aUser['user_id']) || $aUser['user_id'] == null) ? Phpfox::getUserId() : (int)$aUser['user_id']) . ' AND is_expired = 0')
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheBanned, $aBanned);
            Phpfox::getLib('cache')->group('ban', $sCacheBanned);
        }

        /* Users banned in version 2.0 do not have a record in ban_data but belong to the banned user group */
        if (!isset($aBanned['user_id']) &&
            isset($aUser['user_group_id']) &&
            Phpfox::getService('user.group.setting')->getGroupParam($aUser['user_group_id'], 'core.user_is_banned')) {
            return array('is_banned' => true);
        }

        /* Users banned in version 2.1 do have a record in ban_data where is_expired == 0 and the time stamp is
           either 0 or in the future */
        if (isset($aBanned['is_expired']) && $aBanned['is_expired'] == 0 && isset($aBanned['end_time_stamp'])
            && ($aBanned['end_time_stamp'] == 0 || $aBanned['end_time_stamp'] > PHPFOX_TIME)) {
            return array_merge(array('is_banned' => true), $aBanned);
        }

        if (!is_array($aBanned)) {
            $aBanned = [];
        }
        return array_merge(array('is_banned' => false), $aBanned);
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('ban.service_ban__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

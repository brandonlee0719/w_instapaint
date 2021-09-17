<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Service_Profile
 */
class Profile_Service_Profile extends Phpfox_Service
{
    private $_iUserId = 0;

    public function getProfileTitle($aRow)
    {
        $sTitleReplace = Phpfox::getParam('profile.profile_seo_for_meta_title');
        if (!empty($sTitleReplace) && Phpfox::getService('user.privacy')->hasAccess($aRow['user_id'],
                'profile.basic_info')) {
            preg_match_all('/\{(.*?)\}/i', $sTitleReplace, $aMatches);
            if (isset($aMatches[1]) && is_array($aMatches[1])) {
                foreach ($aMatches[1] as $sFind) {
                    if ($sFind == 'gender_name' && !Phpfox::getUserGroupParam($aRow['user_group_id'],
                            'user.can_edit_gender_setting')) {
                        unset($aRow[$sFind]);
                    }

                    if (!empty($aRow[$sFind])) {
                        if ($sFind == 'location' && !empty($aRow[$sFind])) {
                            if (isset($aRow['location_child'])) {
                                $aRow[$sFind] = $aRow[$sFind] . ' - ' . $aRow['location_child'];
                            }
                        }

                        $sTitleReplace = str_replace('{' . $sFind . '}', $aRow[$sFind], $sTitleReplace);
                    } else {
                        $sTitleReplace = str_replace('{' . $sFind . '} -', '', $sTitleReplace);
                        $sTitleReplace = str_replace('{' . $sFind . '}', '', $sTitleReplace);
                    }
                }
            }

            $sPageTitle = rtrim(trim($sTitleReplace), '-');
        }

        if (empty($sPageTitle)) {
            $sPageTitle = $aRow['full_name'];
        }

        return $sPageTitle;
    }

    /**
     * @deprecated 4.4.0
     * @return bool
     */
    public function timeline()
    {
        return false;
    }

    public function getProfileMenu($aUser)
    {
        $aMenus = array();
        if (!Phpfox::getUserBy('profile_page_id') && !defined('PHPFOX_IN_DESIGN_MODE')) {
            $aModuleCalls = Phpfox::massCallback('getProfileMenu', $aUser);
            foreach ($aModuleCalls as $sModule => $aModuleCall) {
                if (!is_array($aModuleCall)) {
                    continue;
                }

                if ($sModule == 'friend') {
                    continue;
                }
                $aMenus = array_merge($aMenus, $aModuleCall);
            }
        }

        foreach ($aMenus as $iKey => $aMenu) {
            if (isset($aMenu['total']) && !$aMenu['total'] && !Phpfox::getParam('profile.show_empty_tabs')) {
                unset($aMenus[$iKey]);
                continue;
            }

            $bSubIsSelected = false;
            if (isset($aMenu['sub_menu'])) {
                foreach ((array)$aMenu['sub_menu'] as $iSubKey => $aSubMenu) {
                    if ($this->request()->get('view')) {
                        $sCurrent = 'profile.' . $this->request()->get('req2') . '.view_' . $this->request()->get('view');
                    } else {
                        $sCurrent = 'profile.' . $this->request()->get('req2') . '.' . $this->request()->get('req3');
                    }

                    if ($sCurrent == $aSubMenu['url']) {
                        $aMenus[$iKey]['sub_menu'][$iSubKey]['is_selected'] = true;
                        $bSubIsSelected = true;
                        break;
                    }
                }
            }

            if ($bSubIsSelected === false
                && (
                    ($aMenu['url'] == 'profile' . (Phpfox_Request::instance()->get('req2') ? '.' . Phpfox_Request::instance()->get('req2') : '') . (Phpfox_Request::instance()->get('req3') ? '.' . Phpfox_Request::instance()->get('req3') : ''))
                    || (Phpfox_Request::instance()->get('req2') == '' && $iKey === 0 && !Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'],
                            'feed.view_wall'))
                )
            ) {
                $aMenus[$iKey]['is_selected'] = true;
            }

            if ($aMenu['url'] == 'profile.photo' && Phpfox_Request::instance()->get('req2') == 'photo' && (Phpfox_Request::instance()->get('req3') == 'albums' || Phpfox_Request::instance()->get('req3') == 'photos')) {
                $aMenus[$iKey]['is_selected'] = true;
            }

            $aMenus[$iKey]['actual_url'] = str_replace('.', '_', $aMenu['url']);

            if ($aMenu['url'] == 'profile') {
                $aMenus[$iKey]['url'] = $aUser['user_name'];
            } else {
                $aMenus[$iKey]['url'] = $aUser['user_name'] . '.' . Phpfox_Url::instance()->doRewrite(preg_replace("/^profile\.(.*)$/i",
                        "\\1", $aMenu['url']));
            }
        }
        //Activity points info
        if (Phpfox::getParam('user.no_show_activity_points') && $aUser['user_id'] == Phpfox::getUserId()) {
            $aMenus[] = array(
                'phrase' => _p('activity_points'),
                'url' => $aUser['user_name'] . '.activity-points',
                'total' => $aUser['activity_points'],
                'actual_url' => 'profile_activity-points'
            );
        }
        /* Reminder for purefan add a hook here */
        if ($sPlugin = Phpfox_Plugin::get('profile.service_profile_get_profile_menu')) {
            eval($sPlugin);
        }
        return $aMenus;
    }

    public function setUserId($iUserId)
    {
        $this->_iUserId = (int)$iUserId;
    }

    public function getProfileUserId()
    {
        return (int)$this->_iUserId;
    }

    /**
     * @deprecated This function will be removed in 4.6.0
     * @param $aItem
     * @return array|int|null|string
     */
    public function getInfoForAction($aItem)
    {
        if (isset($aItem['item_type_id']) && $aItem['item_type_id'] == 'user-status') {
            return Phpfox::getService('user')->getInfoForAction($aItem);
        }
        return null;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('profile.service_profile__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

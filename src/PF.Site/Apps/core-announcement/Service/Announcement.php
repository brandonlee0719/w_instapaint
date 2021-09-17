<?php

namespace Apps\Core_Announcement\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Announcement extends Phpfox_Service
{
    public static $aSupportStyle = ['info', 'danger', 'warning', 'success'];

    /**
     * Class constructor
     */
    public function __construct()
    {
    }

    /**
     * This function needs to get all the available announcements (is_active = 1) from database/cache
     *    Given that array, filter out based on the criteria given
     * @param int $iId
     * @param bool $bShowInDashboard
     * @param int $iDate
     * @return bool|array
     */
    public function getLatest($iId = null, $bShowInDashboard = null, $iDate = null)
    {
        $sCacheId = $this->cache()->set('announcements');

        if (!($aAnnouncements = $this->cache()->get($sCacheId))) {
            // for the is seen we'll need to query the database so we can take the left join out of here.
            $aAnnouncements = db()->select('a.*')
                ->from(Phpfox::getT('announcement'), 'a')
                ->where('a.is_active = 1')
                ->order('a.time_stamp DESC')
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aAnnouncements);
        }

        // get the announcements this user has decided to close
        $sCacheIdHide = $this->cache()->set('announcement_' . Phpfox::getUserId());
        if (!($aHidden = $this->cache()->get($sCacheIdHide))) {
            $aHide = db()->select('announcement_id')
                ->from(Phpfox::getT('announcement_hide'))
                ->where('user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveRows');

            foreach ($aHide as $aH) {
                $aHidden[] = $aH['announcement_id'];
            }

            $this->cache()->save($sCacheIdHide, $aHidden);
        }
        if (!is_array($aHidden)) {
            $aHidden = [];
        }
        if (!is_array($aAnnouncements)) {
            return false;
        }

        foreach ($aAnnouncements as $iKey => &$aAnnounce) {
            // we filter out the ones that do not apply to this user
            // get users age
            $iUsersAge = Phpfox::getService('user')->age(Phpfox::getUserBy('birthday'));
            // get the allowed user groups
            $aAllowedUsergroups = (false === unserialize($aAnnounce['user_group'])) ? array() : unserialize($aAnnounce['user_group']);

            $aAnnounce['start_date'] = Phpfox::getLib('date')->convertFromGmt($aAnnounce['start_date'],
                $aAnnounce['gmt_offset']);

            $aAnnounce['posted_on'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aAnnounce['time_stamp']);

            $bCheck1 = ($aAnnounce['country_iso'] == '' || $aAnnounce['country_iso'] == Phpfox::getUserBy('country_iso'));
            $bCheck2 = ($aAnnounce['age_from'] == 0 || ($aAnnounce['age_from'] <= $iUsersAge));
            $bCheck3 = ($aAnnounce['age_to'] == 0 || ($aAnnounce['age_to'] >= $iUsersAge));
            $bCheck4 = ($aAnnounce['gender'] == 0 || ($aAnnounce['gender'] == Phpfox::getUserBy('gender')));
            $bCheck5 = (sizeof($aAllowedUsergroups) == 0 || in_array(Phpfox::getUserBy('user_group_id'),
                    $aAllowedUsergroups));
            $bCheck6 = ($iId === null || $aAnnounce['announcement_id'] == (int)$iId);
            $bCheck7 = ($bShowInDashboard === null || $aAnnounce['show_in_dashboard'] == 1);
            $bCheck8 = ($iDate === null || $aAnnounce['start_date'] <= $iDate);
            $bCheck9 = (empty($aHidden) || !in_array($aAnnounce['announcement_id'], $aHidden));

            if (!$bCheck1 || !$bCheck2 || !$bCheck3 || !$bCheck4 || !$bCheck5 || !$bCheck6 || !$bCheck7 || !$bCheck8 || !$bCheck9) {
                unset($aAnnouncements[$iKey]);
            }

            $aAnnounce['subject_var'] = _p($aAnnounce['subject_var']);
            $aAnnounce['intro_var'] = _p($aAnnounce['intro_var']);
            $aAnnounce['content_var'] = _p($aAnnounce['content_var']);
            $aAnnounce['icon_image'] = Phpfox::getParam('announcement.icon_' . $aAnnounce['style']);

            switch ($aAnnounce['style']) {
                case 'info':
                    $aAnnounce['icon_font'] = 'ico-newspaper-o';
                    break;
                case 'success':
                    $aAnnounce['icon_font'] = 'ico-check-circle-alt';
                    break;
                case 'warning':
                    $aAnnounce['icon_font'] = 'ico-warning-o';
                    break;
                case 'danger':
                    $aAnnounce['icon_font'] = 'ico-fire';
                    break;
                default:
                    break;
            }
        }
//        sort($aAnnouncements);
        return $aAnnouncements;
    }

    /**
     * Gets the latest $iLatest announcements
     * @param int $iId
     * @return array
     */
    public function getAnnouncementsByLanguage($iId = 0)
    {
        static $aAnnouncements = null;

        if ($aAnnouncements !== null) {
            return $aAnnouncements;
        }
        if ($iId > 0) {
            db()->where('a.announcement_id = ' . (int)$iId);
        }
        $aAnnouncements = db()->select('a.*')
            ->from(Phpfox::getT('announcement'), 'a')
            ->order('a.time_stamp DESC')
            ->execute('getSlaveRows');

        return $aAnnouncements;
    }

    /**
     * @param int $iId
     *
     * @return array|bool
     */
    public function getAnnouncementById($iId)
    {
        $sCacheId = $this->cache()->set('announcement_' . (int)$iId);
        if (!$aAnnouncement = $this->cache()->get($sCacheId)) {
            $aAnnouncement = db()
                ->select('*')
                ->from(Phpfox::getT('announcement'))
                ->where('announcement_id = ' . $iId)
                ->executeRow(true);

            if (!count($aAnnouncement) || !is_array($aAnnouncement)) {
                return false;
            }


            $aAnnouncement['start_date'] = Phpfox::getLib('date')
                ->convertFromGmt($aAnnouncement['start_date'], $aAnnouncement['gmt_offset']);

            $aAnnouncement = array_merge($aAnnouncement, [
                'announcement_id' => $iId,
                'announcement_start_month' => date('n', $aAnnouncement['start_date']),
                'announcement_start_day' => date('j', $aAnnouncement['start_date']),
                'announcement_start_hour' => date('H', $aAnnouncement['start_date']),
                'announcement_start_year' => date('Y', $aAnnouncement['start_date']),
                'announcement_start_minute' => date('i', $aAnnouncement['start_date'])
            ]);

            if (!empty($aAnnouncement['user_group'])) {
                $aAnnouncement['user_group'] = unserialize($aAnnouncement['user_group']);
                if (count($aAnnouncement['user_group'])) {
                    $aAnnouncement['is_user_group'] = 2;
                }
            }

            $aLanguages = Phpfox::getService('language')->getAll();
            foreach ($aLanguages as $aLanguage) {
                $aAnnouncement['content_' . $aLanguage['language_id']] = Phpfox::getSoftPhrase($aAnnouncement['content_var'],
                    [], false,
                    null, $aLanguage['language_id']);
            }
            $this->cache()->save($sCacheId, $aAnnouncement);
        }

        return $aAnnouncement;
    }

    /**
     * Get more announcements
     * @param $iId
     * @param int $iLimit
     * @return array
     */
    public function getMore($iId, $iLimit = 4)
    {
        $aAnnoucements = db()->select('*')->from(':announcement')->where('announcement_id != ' . $iId . ' AND is_active=1')->limit($iLimit)->order('rand()')->executeRows();

        foreach ($aAnnoucements as &$aAnnoucement) {
            $aAnnoucement['subject_var'] = _p($aAnnoucement['subject_var']);
            $aAnnoucement['intro_var'] = _p($aAnnoucement['intro_var']);
        }

        return $aAnnoucements;
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
        if ($sPlugin = Phpfox_Plugin::get('announcement.service_announcement__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

<?php

namespace Apps\Core_Announcement\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Process extends Phpfox_Service
{
    const FIELD_SUBJECT = 'subject';
    const FIELD_INTRO = 'intro';
    const FIELD_CONTENT = 'content';
    private $_aLanguages;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('announcement');
        $this->_aLanguages = Phpfox::getService('language')->getAll();
    }

    /**
     * Add a new phrase for category
     *
     * @param array $aVals
     * @param string $sName
     * @param bool $bVerify
     *
     * @return null|string
     */
    protected function addPhrase($aVals, $sName = 'name', $bVerify = true)
    {
        $langId =  current($this->_aLanguages)['language_id'];
        $aFirstLang = end($this->_aLanguages);

        //Add phrases
        $aText = [];
        //Verify name

        foreach ($this->_aLanguages as $aLanguage) {
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']]) && !empty($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
            }elseif(isset($aVals[$sName . '_' . $langId]) && !empty($aVals[$sName . '_' . $langId])){
                $aText[$aLanguage['language_id']] = $aVals[$sName . '_' . $langId];
            } elseif ($bVerify) {
                return Phpfox_Error::set(_p('provide_a_language_name_label',
                    ['language_name' => $aLanguage['title'],'label' => $sName]));
            } else {
                $bReturnNull = true;
            }
        }
        if (isset($bReturnNull) && $bReturnNull) {
            //If we don't verify value, phrase can't be empty. Return null for this case.
            return null;
        }
        $name = $aVals[$sName . '_' . $aFirstLang['language_id']];
        $phrase_var_name = 'announcement_' . $sName . '_' . md5($name . PHPFOX_TIME);

        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];

        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        return $finalPhrase;
    }

    /**
     * Update phrase
     *
     * @param array $aVals
     * @param string $sName
     */
    protected function updatePhrase($aVals, $sName = 'name')
    {
        foreach ($this->_aLanguages as $aLanguage) {
            if (isset($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $name = $aVals[$sName . '_' . $aLanguage['language_id']];
                Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals[$sName],
                    $name);
            }
        }
    }

    /**
     * Deletes an announcement
     * @param int $iId
     * @return bool
     */
    public function delete($iId)
    {
        // how to check if user is an admin ?
        $iId = (int)$iId;
        if ($iId < 1) {
            return false;
        }

        db()->delete($this->_sTable, 'announcement_id = ' . $iId);
        db()->delete(Phpfox::getT('language_phrase'), 'var_name = \'announcement_subject_' . $iId . '\'');
        db()->delete(Phpfox::getT('language_phrase'), 'var_name = \'announcement_content_' . $iId . '\'');
        db()->delete(Phpfox::getT('language_phrase'), 'var_name = \'announcement_intro_' . $iId . '\'');
        $this->cache()->remove('announcements');

        return true;
    }

    /**
     *    Adds an announcement. The process is to add a dummy entry in the announcements table, then add the values in the
     *        phrase manager for each of the languages available (and passed in the array) according to the announcement_id
     *        that we got from the first entry.
     * @param array $aVals
     * @return string|bool
     */
    public function add($aVals)
    {
        // Add subject
        $sSubject = $this->addPhrase($aVals, self::FIELD_SUBJECT);
        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        // Add content
        $sContent = $this->addPhrase($aVals, self::FIELD_CONTENT, false);
        if (!Phpfox_Error::isPassed()) {
            Phpfox::getService('language.phrase.process')->delete($sSubject, true);
            return false;
        }

        $sIntro = $this->addPhrase($aVals, self::FIELD_INTRO);
        if (!Phpfox_Error::isPassed()) {
            Phpfox::getService('language.phrase.process')->delete($sSubject, true);
            Phpfox::getService('language.phrase.process')->delete($sContent, true);
            return false;
        }

        // convert input start_date to database start_date
        $parsed_time = Phpfox::getLib('date')->mktime($aVals['announcement_start_hour'],
            $aVals['announcement_start_minute'], 0, $aVals['announcement_start_month'],
            $aVals['announcement_start_day'], $aVals['announcement_start_year']);
        $iStartDate = Phpfox::getLib('date')->convertToGmt((int)$parsed_time);

        $aInsertAnnouncement = array(
            'subject_var' => $sSubject,
            'intro_var' => $sIntro,
            'content_var' => $sContent,
            'time_stamp' => PHPFOX_TIME,
            'is_active' => (int)$aVals['is_active'],
            'can_be_closed' => (int)$aVals['can_be_closed'],
            'start_date' => $iStartDate,
            'age_from' => (int)$aVals['age_from'],
            'age_to' => (int)$aVals['age_to'],
            'country_iso' => $aVals['country_iso'],
            'gender' => (int)$aVals['gender'],
            'user_id' => (int)$aVals['user_id'],
            'user_group' => serialize(array()),
            'show_in_dashboard' => (int)$aVals['show_in_dashboard'],
            'style' => $aVals['style'],
            'gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartDate)
        );

        if (isset($aVals['is_user_group']) && $aVals['is_user_group'] == 2) {
            $aGroups = array();
            $aUserGroups = Phpfox::getService('user.group')->get();
            if (isset($aVals['user_group'])) {
                foreach ($aUserGroups as $aUserGroup) {
                    if (in_array($aUserGroup['user_group_id'], $aVals['user_group'])) {
                        $aGroups[] = $aUserGroup['user_group_id'];
                    }
                }
            }
            $aInsertAnnouncement['user_group'] = (count($aGroups) ? serialize($aGroups) : null);
        }

        $iId = db()->insert($this->_sTable, $aInsertAnnouncement);

        $this->cache()->remove('announcements');
        return $iId;
    }

    public function update($aVals, $iId)
    {
        //Update subject phrase
        $this->updatePhrase($aVals, self::FIELD_SUBJECT);
        //Update info phrase
        $this->updatePhrase($aVals, self::FIELD_INTRO);
        //Update content phrase
        if ($aVals['content'] == '') {
            $aVals['content'] = $this->addPhrase($aVals, self::FIELD_CONTENT, false);
        } else {
            $this->updatePhrase($aVals, self::FIELD_CONTENT);
        }

        // convert input start_date to database start_date
        $parsed_time = Phpfox::getLib('date')->mktime($aVals['announcement_start_hour'],
            $aVals['announcement_start_minute'], 0, $aVals['announcement_start_month'],
            $aVals['announcement_start_day'], $aVals['announcement_start_year']);
        $iStartDate = Phpfox::getLib('date')->convertToGmt((int)$parsed_time);

        $aUpdate = array(
            'is_active' => (int)$aVals['is_active'],
            'can_be_closed' => (int)$aVals['can_be_closed'],
            'show_in_dashboard' => (int)$aVals['show_in_dashboard'],
            'start_date' => $iStartDate,
            'age_from' => (int)$aVals['age_from'],
            'age_to' => (int)$aVals['age_to'],
            'country_iso' => $aVals['country_iso'],
            'gender' => (int)$aVals['gender'],
            'user_id' => (int)$aVals['user_id'],
            'style' => $aVals['style'],
            'user_group' => serialize(array()),
            'gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartDate),
            'content_var' => $aVals['content']
        );

        if (isset($aVals['is_user_group']) && $aVals['is_user_group'] == 2) {
            $aGroups = array();
            $aUserGroups = Phpfox::getService('user.group')->get();
            if (isset($aVals['user_group'])) {
                foreach ($aUserGroups as $aUserGroup) {
                    if (in_array($aUserGroup['user_group_id'], $aVals['user_group'])) {
                        $aGroups[] = $aUserGroup['user_group_id'];
                    }
                }
            }
            $aUpdate['user_group'] = serialize($aGroups);
        }

        $iOldCanBeClosed = db()
            ->select('can_be_closed')
            ->from($this->_sTable)
            ->where('announcement_id = ' . $iId)
            ->execute('getSlaveField');
        if ($iOldCanBeClosed != $aVals['can_be_closed']) {
            db()->delete(Phpfox::getT('announcement_hide'), 'announcement_id = ' . (int)$iId);
        }

        $bSuccess = db()->update($this->_sTable, $aUpdate, 'announcement_id = ' . $iId);

        if ($bSuccess) {
            $this->cache()->remove('announcements');
            return true;
        } else {
            return false;
        }
    }

    /**
     * @deprecated Will remove on version 4.6
     * @param int $iId
     * @param array $aVal
     *
     * @return bool
     */
    public function editAnnouncement($iId, $aVal)
    {
        if (!is_int($iId) || $iId < 1) {
            return false;
        }

        foreach ($aVal['subject'] as $sLanguage => $aSubject) {
            if ($aSubject['is_default'] == 1 && empty($aSubject['text'])) {
                return Phpfox_Error::set(_p('subject_cannot_be_empty'));
            }
            Phpfox::getService('language.phrase.process')->updateVarName($sLanguage,
                'announcement.announcement_subject_' . $iId, $aSubject['text']);
        }
        foreach ($aVal['intro'] as $sLanguage => $aIntro) {
            Phpfox::getService('language.phrase.process')->updateVarName($sLanguage,
                'announcement.announcement_intro_' . $iId, $aIntro['text']);
        }
        foreach ($aVal['content'] as $sLanguage => $aContent) {
            if ($aContent['is_default'] == 1 && empty($aContent['text'])) {
                return Phpfox_Error::set(_p('content_cannot_be_empty'));
            }
            Phpfox::getService('language.phrase.process')->updateVarName($sLanguage,
                'announcement.announcement_content_' . $iId, $aContent['text']);
        }
        // update the active/inactive state
        $iStartDate = Phpfox::getLib('date')->convertToGmt((int)Phpfox::getLib('date')->mktime($aVal['announcement_start_hour'],
            $aVal['announcement_start_minute'], 0, $aVal['announcement_start_month'], $aVal['announcement_start_day'],
            $aVal['announcement_start_year']));
        $aUpdate = array(
            'is_active' => (int)$aVal['is_active'],
            'can_be_closed' => (int)$aVal['can_be_closed'],
            'show_in_dashboard' => (int)$aVal['show_in_dashboard'],
            'start_date' => $iStartDate,
            'age_from' => (int)$aVal['age_from'],
            'age_to' => (int)$aVal['age_to'],
            'country_iso' => $aVal['country_iso'],
            'gender' => (int)$aVal['gender'],
            'user_id' => (int)$aVal['user_id'],
            'user_group' => serialize(array()),
            'gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartDate)
        );
        if (isset($aVal['is_user_group']) && $aVal['is_user_group'] == 2) {
            $aGroups = array();
            $aUserGroups = Phpfox::getService('user.group')->get();
            if (isset($aVal['user_group'])) {
                foreach ($aUserGroups as $aUserGroup) {
                    if (in_array($aUserGroup['user_group_id'], $aVal['user_group'])) {
                        $aGroups[] = $aUserGroup['user_group_id'];
                    }
                }
            }
            $aUpdate['user_group'] = (count($aGroups) ? serialize($aGroups) : serialize(array()));
        }
        $iOldCanBeClosed = db()
            ->select('can_be_closed')
            ->from($this->_sTable)
            ->where('announcement_id = ' . $iId)
            ->execute('getSlaveField');
        if ($iOldCanBeClosed != $aVal['can_be_closed']) {
            db()->delete(Phpfox::getT('announcement_hide'), 'announcement_id = ' . (int)$iId);
        }
        db()->update($this->_sTable, $aUpdate, 'announcement_id = ' . $iId);
        $this->cache()->remove('announcements');
        $this->cache()->remove('locale', 'substr');
        return true;
    }

    /**
     * @deprecated Will remove on version 4.6
     * Changes status of an announcement (active/inactive)
     * @param int $iId
     * @param int $iNewState
     * @return bool|string
     */
    public function setStatus($iId, $iNewState)
    {
        $this->cache()->remove('announcements');
        if (intval($iNewState) == 0) {
            return db()->update($this->_sTable, array(
                'is_active' => 0
            ),
                'announcement_id = ' . (int)$iId);
        } elseif (intval($iNewState) == 1) {
            return db()->update($this->_sTable, array(
                'is_active' => 1
            ),
                'announcement_id = ' . (int)$iId);
        }
        return 'Problem: iId = ' . $iId . ' and iNewState: ' . $iNewState;
    }

    /**
     * Hides an announcement for the current user
     * @param int $iId
     * @return bool
     * @throws \Exception
     */
    public function hide($iId)
    {
        Phpfox::isUser(true);
        $aAnnouncement = db()->select('a.announcement_id, a.can_be_closed, ah.announcement_id AS is_seen')
            ->from($this->_sTable, 'a')
            ->leftJoin(Phpfox::getT('announcement_hide'), 'ah',
                'ah.announcement_id = a.announcement_id AND ah.user_id = ' . Phpfox::getUserId())
            ->where('a.announcement_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if ($aAnnouncement['can_be_closed'] == 0) {
            return false;
        }
        if (!isset($aAnnouncement['announcement_id'])) {
            return Phpfox_Error::set(_p('announcement_not_found'));
        }

        if ($aAnnouncement['is_seen']) {
            return Phpfox_Error::set(_p('announcement_is_already_hidden'));
        }

        db()->insert(Phpfox::getT('announcement_hide'),
            array('announcement_id' => $aAnnouncement['announcement_id'], 'user_id' => Phpfox::getUserId()));
        $this->cache()->remove('announcement_' . Phpfox::getUserId());
        return true;
    }

    public function toggleActiveAnnouncement($iId, $iActive)
    {
        Phpfox::isAdmin(true);

        $iActive = (int)$iActive;
        db()->update($this->_sTable, [
            'is_active' => ($iActive == 1 ? 1 : 0)
        ], 'announcement_id= ' . (int)$iId);

        $this->cache()->remove('announcements');
        $this->cache()->remove('announcement_' . $iId, 'substr');
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
        if ($sPlugin = Phpfox_Plugin::get('announcement.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
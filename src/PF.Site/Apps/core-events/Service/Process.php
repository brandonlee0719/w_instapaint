<?php
namespace Apps\Core_Events\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Plugin;
use Phpfox_Request;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');


class Process extends \Phpfox_Service
{
    /**
     * @var bool
     */
    private $_aPhotoSizes = array(50, 400, 600);

    /**
     * @var array
     */
    private $_aInvited = array();

    /**
     * @var array
     */
    private $_aCategories = array();

    /**
     * @var bool
     */
    private $_bIsEndingInThePast = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('event');
    }

    /**
     * @param array $aVals
     * @param string $sModule
     * @param int $iItem
     *
     * @return int
     */
    public function add($aVals, $sModule = 'event', $iItem = 0)
    {
        if (!$this->_verify($aVals)) {
            return false;
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        $oParseInput = Phpfox::getLib('parse.input');
        Phpfox::getService('ban')->checkAutomaticBan($aVals);
        $bHasAttachments = (!empty($aVals['attachment']));

        $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0,
            $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
        if ($this->_bIsEndingInThePast === true) {
            $aVals['end_hour'] = ($aVals['start_hour'] + 1);
            $aVals['end_minute'] = $aVals['start_minute'];
            $aVals['end_day'] = $aVals['start_day'];
            $aVals['end_year'] = $aVals['start_year'];
        }

        $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'],
            $aVals['end_day'], $aVals['end_year']);

        if ($iStartTime > $iEndTime) {
            $iEndTime = $iStartTime;
        }

        $aSql = array(
            'view_id' => (int)Phpfox::getUserParam('event.event_must_be_approved'),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'module_id' => $sModule,
            'item_id' => $iItem,
            'user_id' => Phpfox::getUserId(),
            'title' => $oParseInput->clean($aVals['title'], 255),
            'location' => $oParseInput->clean($aVals['location'], 255),
            'country_iso' => (empty($aVals['country_iso']) ? Phpfox::getUserBy('country_iso') : $aVals['country_iso']),
            'country_child_id' => (isset($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'],
                20)),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'time_stamp' => PHPFOX_TIME,
            'start_time' => Phpfox::getLib('date')->convertToGmt($iStartTime),
            'end_time' => Phpfox::getLib('date')->convertToGmt($iEndTime),
            'start_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartTime),
            'end_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iEndTime),
            'address' => (empty($aVals['address']) ? null : Phpfox::getLib('parse.input')->clean($aVals['address'])),
        );
        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                if (!Phpfox::getService('user.space')->isAllowedToUpload(Phpfox::getUserId(), $aFile['size'])) {
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                    return false;
                }
                $aSql['image_path'] = $aFile['path'];
                $aSql['server_id'] = $aFile['server_id'];
                Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'event', $aFile['size']);
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }
        if ($sPlugin = Phpfox_Plugin::get('event.service_process_add__start')) {
            return eval($sPlugin);
        }

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        $iId = $this->database()->insert($this->_sTable, $aSql);

        if (!$iId) {
            return false;
        }
        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        $this->database()->insert(Phpfox::getT('event_text'), array(
                'event_id' => $iId,
                'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
                'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
            )
        );

        foreach ($this->_aCategories as $iCategoryId) {
            $this->database()->insert(Phpfox::getT('event_category_data'),
                array('event_id' => $iId, 'category_id' => $iCategoryId));
        }

        $bAddFeed = (Phpfox::getUserParam('event.event_must_be_approved') ? false : true);

        if ($bAddFeed === true) {
            if ($sModule == 'event' && Phpfox::isModule('feed')) {
                Phpfox::getService('feed.process')->add('event', $iId, $aVals['privacy'],
                    (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0));
            } else {
                if (Phpfox::isModule('feed')) {
                    Phpfox::getService('feed.process')
                        ->callback(Phpfox::callback($sModule . '.getFeedDetails', $iItem))
                        ->add('event', $iId, $aVals['privacy'],
                            (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), $iItem);

                }

                //support add notification for parent module
                if (Phpfox::isModule('notification') && $sModule != 'event' && Phpfox::isModule($sModule) && Phpfox::hasCallback($sModule,
                        'addItemNotification')
                ) {
                    Phpfox::callback($sModule . '.addItemNotification', [
                        'page_id' => $iItem,
                        'item_perm' => 'event.who_can_view_browse_events',
                        'item_type' => 'event',
                        'item_id' => $iId,
                        'owner_id' => Phpfox::getUserId(),
                        'items_phrase' => _p('events__l')
                    ]);
                }
            }

            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'event');
        }

        $this->addRsvp($iId, 1, Phpfox::getUserId());


        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('event', $iId, Phpfox::getUserId(), $aVals['description'], true);
        }

        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('event.service_process_add__end')) {
            eval($sPlugin);
        }

        return $iId;
    }

    /**
     * @param array $aVals
     * @param bool $bIsUpdate, deprecated, remove in 4.7.0
     *
     * @return bool
     */
    private function _verify(&$aVals, $bIsUpdate = false)
    {
        if (isset($aVals['category'])) {
            if (!is_array($aVals['category'])) {
                $aVals['category'] = [$aVals['category']];
            }
            $aCategories = array_filter($aVals['category']);
            $iCategoryId = end($aCategories);
            if ($iCategoryId) {
                $sCategories = Phpfox::getService('event.category')->getParentCategories($iCategoryId);
                if (empty($sCategories)) {
                    return Phpfox_Error::set(_p('The {{ item }} cannot be found.', ['item' => _p('category__l')]));
                }
                $this->_aCategories = explode(',', trim($sCategories, ','));
            }
        }

        $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0,
            $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
        $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'],
            $aVals['end_day'], $aVals['end_year']);

        if ($iEndTime < $iStartTime) {
            $this->_bIsEndingInThePast = true;
        }
        return true;
    }

    /**
     * @param int $iEvent
     * @param int $iRsvp
     * @param int $iUserId
     *
     * @return bool
     */
    public function addRsvp($iEvent, $iRsvp, $iUserId)
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        if (($iInviteId = $this->database()->select('invite_id')
            ->from(Phpfox::getT('event_invite'))
            ->where('event_id = ' . (int)$iEvent . ' AND invited_user_id = ' . (int)$iUserId)
            ->execute('getSlaveField'))
        ) {
            $this->database()->update(Phpfox::getT('event_invite'), array(
                'rsvp_id' => $iRsvp,
                'invited_user_id' => $iUserId,
                'time_stamp' => PHPFOX_TIME
            ), 'invite_id = ' . $iInviteId
            );

            (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('event_invite', $iEvent,
                $iUserId) : false);
        } else {
            $this->database()->insert(Phpfox::getT('event_invite'), array(
                    'event_id' => $iEvent,
                    'rsvp_id' => $iRsvp,
                    'user_id' => $iUserId,
                    'invited_user_id' => $iUserId,
                    'time_stamp' => PHPFOX_TIME
                )
            );
        }

        return true;
    }

    /**
     * @param int $iId
     * @param array $aVals
     * @param null|array $aEventPost, deprecated, remove in 4.7.0
     *
     * @return bool
     */
    public function update($iId, $aVals, $aEventPost = null)
    {
        if (!$this->_verify($aVals, true)) {
            return false;
        }
        $aEvent = $this->database()->select('event_id, user_id, title, module_id, image_path, server_id')
            ->from($this->_sTable)
            ->where('event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $oParseInput = Phpfox::getLib('parse.input');

        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['description']);

        $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0,
            $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
        $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'],
            $aVals['end_day'], $aVals['end_year']);

        if ($iStartTime > $iEndTime) {
            $iEndTime = $iStartTime;
        }

        $aSql = array(
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'title' => $oParseInput->clean($aVals['title'], 255),
            'location' => $oParseInput->clean($aVals['location'], 255),
            'country_iso' => $aVals['country_iso'],
            'country_child_id' => (isset($aVals['country_child_id']) ? Phpfox::getService('core.country')->getValidChildId($aVals['country_iso'],
                (int)$aVals['country_child_id']) : 0),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'],
                20)),
            'start_time' => Phpfox::getLib('date')->convertToGmt($iStartTime),
            'end_time' => Phpfox::getLib('date')->convertToGmt($iEndTime),
            'start_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartTime),
            'end_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iEndTime),
            'address' => (empty($aVals['address']) ? null : Phpfox::getLib('parse.input')->clean($aVals['address']))
        );

        if (!empty($aEvent['image_path']) && (!empty($aVals['temp_file']) || !empty($aVals['remove_photo']))) {
            if ($this->deleteImage($iId)) {
                $aSql['image_path'] = null;
                $aSql['server_id'] = 0;
            }
            else {
                return false;
            }
        }

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                if (!Phpfox::getService('user.space')->isAllowedToUpload($aEvent['user_id'], $aFile['size'])) {
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                    return false;
                }
                $aSql['image_path'] = $aFile['path'];
                $aSql['server_id'] = $aFile['server_id'];
                Phpfox::getService('user.space')->update($aEvent['user_id'], 'event', $aFile['size']);
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }
        if ($sPlugin = Phpfox_Plugin::get('event.service_process_update__start')) {
            return eval($sPlugin);
        }
        $this->database()->update($this->_sTable, $aSql, 'event_id = ' . (int)$iId);

        $this->database()->update(Phpfox::getT('event_text'), array(
            'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
            'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
        ), 'event_id = ' . (int)$iId
        );
        $bHasAttachments = (!empty($aVals['attachment']));
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        if (isset($aVals['emails']) || isset($aVals['invite'])) {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('event_invite'))
                ->where('event_id = ' . (int)$iId)
                ->execute('getSlaveRows');
            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }
        }

        if (isset($aVals['emails'])) {
            // if (strpos($aVals['emails'], ','))
            {
                $aEmails = explode(',', $aVals['emails']);
                $aCachedEmails = array();
                foreach ($aEmails as $sEmail) {
                    $sEmail = trim($sEmail);
                    if (!Phpfox::getLib('mail')->checkEmail($sEmail)) {
                        continue;
                    }

                    if (isset($aInvited['email'][$sEmail])) {
                        continue;
                    }

                    $sLink = Phpfox_Url::instance()->permalink('event', $aEvent['event_id'], $aEvent['title']);

                    $sMessage = _p('full_name_invited_you_to_the_title', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => $oParseInput->clean($aVals['title'], 255),
                            'link' => $sLink
                        )
                    );
                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= _p('full_name_added_the_following_personal_message', array(
                                    'full_name' => Phpfox::getUserBy('full_name')
                                )
                            ) . "\n";
                        $sMessage .= $aVals['personal_message'];
                    }
                    $oMail = Phpfox::getLib('mail');
                    if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1) {
                        $oMail->fromEmail(Phpfox::getUserBy('email'))
                            ->fromName(Phpfox::getUserBy('full_name'));
                    }
                    $bSent = $oMail->to($sEmail)
                        ->subject(array(
                            'event.full_name_invited_you_to_the_event_title',
                            array(
                                'full_name' => Phpfox::getUserBy('full_name'),
                                'title' => $oParseInput->clean($aVals['title'], 255)
                            )
                        ))
                        ->message($sMessage)
                        ->send();

                    if ($bSent) {
                        $this->_aInvited[] = array('email' => $sEmail);

                        $aCachedEmails[$sEmail] = true;

                        $this->database()->insert(Phpfox::getT('event_invite'), array(
                                'event_id' => $iId,
                                'type_id' => 1,
                                'user_id' => Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                    }
                }
            }
        }

        if (isset($aVals['invite']) && is_array($aVals['invite'])) {
            $sUserIds = '';
            foreach ($aVals['invite'] as $iUserId) {
                if (!is_numeric($iUserId)) {
                    continue;
                }
                $sUserIds .= $iUserId . ',';
            }
            $sUserIds = rtrim($sUserIds, ',');

            $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                ->from(Phpfox::getT('user'))
                ->where('user_id IN(' . $sUserIds . ')')
                ->execute('getSlaveRows');

            foreach ($aUsers as $aUser) {
                if (isset($aCachedEmails[$aUser['email']])) {
                    continue;
                }

                if (isset($aInvited['user'][$aUser['user_id']])) {
                    continue;
                }

                if (Phpfox::isModule('friend') && !Phpfox::getService('friend')->isFriend(Phpfox::getUserId(),
                        $aUser['user_id'])
                ) {
                    continue;
                }

                $sLink = Phpfox_Url::instance()->permalink('event', $aEvent['event_id'], $aEvent['title']);

                $sMessage = _p('full_name_invited_you_to_the_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aVals['title'], 255),
                    'link' => $sLink
                ), $aUser['language_id']);
                if (!empty($aVals['personal_message'])) {
                    $sMessage .= _p('full_name_added_the_following_personal_message', array(
                            'full_name' => Phpfox::getUserBy('full_name')
                        ), $aUser['language_id']
                        ) . ":\n" . $aVals['personal_message'];
                }
                $bSent = Phpfox::getLib('mail')->to($aUser['user_id'])
                    ->subject(array(
                        'event.full_name_invited_you_to_the_event_title',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => $oParseInput->clean($aVals['title'], 255)
                        )
                    ))
                    ->message($sMessage)
                    ->notification('event.invite_to_event')
                    ->send();

                if ($bSent) {
                    $this->_aInvited[] = array('user' => $aUser['full_name']);

                    $this->database()->insert(Phpfox::getT('event_invite'), array(
                            'event_id' => $iId,
                            'user_id' => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        )
                    );

                    (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('event_invite', $iId,
                        $aUser['user_id']) : null);
                }
            }
        }

        $this->database()->delete(Phpfox::getT('event_category_data'), 'event_id = ' . (int)$iId);
        foreach ($this->_aCategories as $iCategoryId) {
            $this->database()->insert(Phpfox::getT('event_category_data'),
                array('event_id' => $iId, 'category_id' => $iCategoryId));
        }

        if (empty($aEvent['module_id']) || $aEvent['module_id'] == 'event') {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('event', $iId, $aVals['privacy'],
                $aVals['privacy_comment']) : null);
        }

        Phpfox::getService('feed.process')->clearCache('event', $iId);

        (($sPlugin = Phpfox_Plugin::get('event.service_process_update__end')) ? eval($sPlugin) : false);


        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->update('event', $aEvent['event_id'], $aEvent['user_id'],
                $aVals['description'], true);
        }

        return true;
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function deleteImage($iId)
    {
        $aEvent = $this->database()->select('user_id, image_path, server_id')
            ->from($this->_sTable)
            ->where('event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aEvent['user_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_event'));
        }

        if (!Phpfox::getService('user.auth')
            ->hasAccess('event', 'event_id', $iId, 'event.can_edit_own_event', 'event.can_edit_other_event',
                $aEvent['user_id'])
        ) {
            return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_event'));
        }

        if (!empty($aEvent['image_path'])) {
            $aParams = Phpfox::getService('event')->getUploadParams();
            $aParams['type'] = 'event';
            $aParams['path'] = $aEvent['image_path'];
            $aParams['user_id'] = $aEvent['user_id'];
            $aParams['update_space'] = ($aEvent['user_id'] ? true : false);
            $aParams['server_id'] = $aEvent['server_id'];

            if (!Phpfox::getService('user.file')->remove($aParams)) {
                return false;
            }
        }

        $this->database()->update($this->_sTable, array('image_path' => null, 'server_id' => 0), 'event_id = ' . (int)$iId);

        (($sPlugin = Phpfox_Plugin::get('event.service_process_deleteimage__end')) ? eval($sPlugin) : false);
        return true;
    }

    /**
     * @param int $iInviteId
     *
     * @return bool
     */
    public function deleteGuest($iInviteId)
    {
        $aEvent = $this->database()->select('e.event_id, e.user_id')
            ->from(Phpfox::getT('event_invite'), 'ei')
            ->join($this->_sTable, 'e', 'e.event_id = ei.event_id')
            ->where('ei.invite_id = ' . (int)$iInviteId)
            ->execute('getSlaveRow');

        if (!isset($aEvent['user_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_event'));
        }

        if (!Phpfox::getService('user.auth')->hasAccess('event', 'event_id', $aEvent['event_id'],
            'event.can_edit_own_event', 'event.can_edit_other_event', $aEvent['user_id'])
        ) {
            return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_event'));
        }

        $this->database()->delete(Phpfox::getT('event_invite'), 'invite_id = ' . (int)$iInviteId);

        return true;
    }

    /**
     * @param int $iId
     * @param null|array $aEvent
     *
     * @param bool $bForce
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function delete($iId, &$aEvent = null, $bForce = false)
    {
        if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__start')) {
            return eval($sPlugin);
        }

        $mReturn = true;
        if ($aEvent === null) {
            $aEvent = $this->database()->select('user_id, module_id, item_id, image_path, is_sponsor, is_featured, server_id')
                ->from($this->_sTable)
                ->where('event_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            if (empty($aEvent)) {
                return $bForce ? false : Phpfox_Error::set(_p('unable_to_find_the_event_you_want_to_delete'));
            }

            if (in_array($aEvent['module_id'],
                    ['pages', 'groups']) && Phpfox::getService($aEvent['module_id'])->isAdmin($aEvent['item_id'])
            ) {
                $mReturn = Phpfox::getService($aEvent['module_id'])->getUrl($aEvent['item_id']) . 'event/';
            } else {
                if (!isset($aEvent['user_id'])) {
                    return $bForce ? false : Phpfox_Error::set(_p('unable_to_find_the_event_you_want_to_delete'));
                }

                if (!$bForce && !Phpfox::getService('user.auth')->hasAccess('event', 'event_id', $iId, 'event.can_delete_own_event',
                    'event.can_delete_other_event', $aEvent['user_id'])
                ) {
                    return Phpfox_Error::set(_p('You don\'t have permission to {{ action }} this {{ item }}.',
                        ['action' => _p('delete__l'), 'item' => _p('event__l')]));
                }
            }
        }

        if (!empty($aEvent['image_path'])) {
            $sPath = Phpfox::getParam('event.dir_image');
            $aSizes = Phpfox::getParam('event.thumbnail_sizes');
            $aImages = [$sPath . sprintf($aEvent['image_path'], '')];
            foreach ($aSizes as $iSize) {
                $aImages[] = $sPath . sprintf($aEvent['image_path'], '_'.$iSize);
                $aImages[] = $sPath . sprintf($aEvent['image_path'], '_'.$iSize.'_square');
            }

            $iFileSizes = 0;
            foreach ($aImages as $sImage) {
                if (file_exists($sImage)) {
                    $iFileSizes += filesize($sImage);
                    if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_unlink')) {
                        return eval($sPlugin);
                    }
                    Phpfox_File::instance()->unlink($sImage);
                }

                if (Phpfox::getParam('core.allow_cdn') && $aEvent['server_id'] > 0) {
                    // Get the file size stored when the photo was uploaded
                    $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('event.dir_image'),
                        Phpfox::getParam('event.url_image'), $sImage));

                    $aHeaders = get_headers($sTempUrl, true);
                    if (preg_match('/200 OK/i', $aHeaders[0])) {
                        $iFileSizes += (int)$aHeaders["Content-Length"];
                    }
                    if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_unlink')) {
                        return eval($sPlugin);
                    }
                    Phpfox::getLib('cdn')->remove($sImage);
                }
            }

            if ($iFileSizes > 0) {
                if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_space_update')) {
                    return eval($sPlugin);
                }
                Phpfox::getService('user.space')->update($aEvent['user_id'], 'event', $iFileSizes, '-');
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__pre_deletes')) {
            return eval($sPlugin);
        }

        if (Phpfox::isModule('feed')) {
            $aFeeds = db()->select('feed_id')->from(':event_feed')->where(['parent_user_id' => $iId])->executeRows();
            foreach (array_column($aFeeds, 'feed_id') as $iFeedId) {
                Phpfox::getService('feed.process')->deleteFeed($iFeedId, 'event');
            }
        }

        (Phpfox::isModule('attachment') ? Phpfox::getService('attachment.process')->deleteForItem($aEvent['user_id'],
            $iId, 'event') : null);
        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null, $iId,
            'event') : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('event', $iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_event', $iId) : null);
        (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('event', (int)$iId, 0, true) : null);
        (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem([
            'event_like',
            'event_comment',
            'event_invite'
        ], (int)$iId) : null);

        //close all sponsorships
        (Phpfox::isModule('ad') ? Phpfox::getService('ad.process')->closeSponsorItem('event', (int)$iId) : null);

        $aInvites = $this->database()->select('invite_id, invited_user_id')
            ->from(Phpfox::getT('event_invite'))
            ->where('event_id = ' . (int)$iId)
            ->execute('getSlaveRows');
        foreach ($aInvites as $aInvite) {
            (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('event_invite',
                $aInvite['invite_id'], $aInvite['invited_user_id']) : false);
        }
        Phpfox::getService('user.activity')->update($aEvent['user_id'], 'event', '-');

        $this->database()->delete($this->_sTable, 'event_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('event_text'), 'event_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('event_category_data'), 'event_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('event_invite'), 'event_id = ' . (int)$iId);
        $iTotalEvent = $this->database()
            ->select('total_event')
            ->from(Phpfox::getT('user_field'))
            ->where('user_id =' . (int)$aEvent['user_id'])->execute('getSlaveField');
        $iTotalEvent = $iTotalEvent - 1;

        if ($iTotalEvent > 0) {
            $this->database()->update(Phpfox::getT('user_field'),
                array('total_event' => $iTotalEvent),
                'user_id = ' . (int)$aEvent['user_id']);
        }

        if ($sPlugin = Phpfox_Plugin::get('event.service_process_delete__end')) {
            return eval($sPlugin);
        }


        $sCacheId = $this->cache()->set(array('events', Phpfox::getUserId()));
        $this->cache()->remove($sCacheId);


        return $mReturn;
    }

    /**
     * @param int $iId
     * @param int $iType
     *
     * @return bool
     */
    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('event.can_feature_events', true);

        $this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')),
            'event_id = ' . (int)$iId);


        return true;
    }

    /**
     * @param int $iId
     * @param int $iType
     *
     * @return bool|mixed
     */
    public function sponsor($iId, $iType)
    {
        if (!Phpfox::getUserParam('event.can_sponsor_event') && !Phpfox::getUserParam('event.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set(_p('hack_attempt'));
        }

        $iType = (int)$iType;
        if ($iType != 1 && $iType != 0) {
            return false;
        }

        $this->database()->update($this->_sTable, array('is_sponsor' => $iType), 'event_id = ' . (int)$iId);


        if ($sPlugin = Phpfox_Plugin::get('event.service_process_sponsor__end')) {
            return eval($sPlugin);
        }

        return true;
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function approve($iId)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('event.can_approve_events', true);

        $aEvent = $this->database()->select('v.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.event_id = ' . (int)$iId)
            ->executeRow();

        if (!isset($aEvent['event_id'])) {
            return false;
        }

        $this->database()->update($this->_sTable, array('view_id' => '0'), 'event_id = ' . $aEvent['event_id']);

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('event_approved', $aEvent['event_id'], $aEvent['user_id']);
        }

        // Send the user an email
        $sLink = Phpfox_Url::instance()->permalink('event', $aEvent['event_id'], $aEvent['title']);

        Phpfox::getLib('mail')->to($aEvent['user_id'])
            ->subject(array(
                'event.your_event_has_been_approved_on_site_title',
                array('site_title' => Phpfox::getParam('core.site_title'))
            ))
            ->message(array(
                'event.your_event_has_been_approved_on_site_title_link',
                array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)
            ))
            ->notification('event.event_is_approved')
            ->send();

        Phpfox::getService('user.activity')->update($aEvent['user_id'], 'event');

        $this->addRsvp($aEvent['event_id'], 1, $aEvent['user_id']);

        (($sPlugin = Phpfox_Plugin::get('event.service_process_approve__1')) ? eval($sPlugin) : false);

        if ($aEvent['module_id'] == 'event' && Phpfox::isModule('feed')) {
            Phpfox::getService('feed.process')->add('event', $aEvent['event_id'], $aEvent['privacy'], $aEvent['privacy_comment'], 0,
                $aEvent['user_id']);
        } else {
            if (Phpfox::isModule('feed')) {
                Phpfox::getService('feed.process')
                    ->callback(Phpfox::callback($aEvent['module_id'] . '.getFeedDetails', $aEvent['item_id']))
                    ->add('event', $aEvent['event_id'], $aEvent['privacy'], $aEvent['privacy_comment'], $aEvent['item_id'],
                        $aEvent['user_id']);
            }
        }
        return true;
    }

    /**
     * @param int $iId
     * @param int $iPage
     * @param string $sSubject
     * @param string $sText
     *
     * @return bool|mixed
     */
    public function massEmail($iId, $iPage, $sSubject, $sText)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('event.can_mass_mail_own_members', true);

        $aEvent = Phpfox::getService('event')->getEvent($iId, true);

        if (!isset($aEvent['event_id'])) {
            return false;
        }

        if ($aEvent['user_id'] != Phpfox::getUserId()) {
            return false;
        }
        if ($sPlugin = Phpfox_Plugin::get('event.service_process_massemail__start')) {
            return eval($sPlugin);
        }
        Phpfox::getService('ban')->checkAutomaticBan($sText);
        list($iCnt, $aGuests) = Phpfox::getService('event')->getInvites($iId, 1, $iPage, 20);

        $sLink = Phpfox_Url::instance()->permalink('event', $aEvent['event_id'], $aEvent['title']);

        $sText = '<br />
		' . _p('notice_this_is_a_newsletter_sent_from_the_event') . ': ' . $aEvent['title'] . '<br />
		<a href="' . $sLink . '">' . $sLink . '</a>
		<br /><br />
		' . $sText;

        foreach ($aGuests as $aGuest) {
            if ($aGuest['user_id'] == Phpfox::getUserId()) {
                continue;
            }

            Phpfox::getLib('mail')->to($aGuest['user_id'])
                ->subject($sSubject)
                ->message($sText)
                ->notification('event.mass_emails')
                ->send();
        }
        if ($sPlugin = Phpfox_Plugin::get('event.service_process_massemail__end')) {
            return eval($sPlugin);
        }
        $this->database()->update($this->_sTable, array('mass_email' => PHPFOX_TIME),
            'event_id = ' . $aEvent['event_id']);

        return $iCnt;
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function removeInvite($iId)
    {
        $this->database()->delete(Phpfox::getT('event_invite'),
            'event_id = ' . (int)$iId . ' AND invited_user_id = ' . Phpfox::getUserId());

        (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('event_invite', $iId,
            Phpfox::getUserId()) : false);

        return true;
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
        if ($sPlugin = Phpfox_Plugin::get('event.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * @param $iId
     * @param $sCounter
     * @param bool $bMinus
     */
    public function updateCounter($iId, $sCounter, $bMinus = false)
    {
        $this->database()->update($this->_sTable, array(
            $sCounter => array('= ' . $sCounter . ' ' . ($bMinus ? '-' : '+'), 1)
        ), 'event_id = ' . (int)$iId
        );
    }
}
<?php

namespace Apps\Core_Photos\Service\Tag;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('photo_tag');
    }

    public function add($aVals)
    {
        Phpfox::isUser(true);

        $aPhoto = db()->select('p.photo_id, p.user_id, p.title, u.full_name')
            ->from(Phpfox::getT('photo'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.photo_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aPhoto['photo_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_photo'));
        }

        if ((Phpfox::getUserParam('photo.can_tag_own_photo') && $aPhoto['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_tag_other_photos')) {
            $sReturn = '';

            $iIsTagged = db()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('photo_id = ' . (int)$aVals['item_id'] . ' AND position_x = ' . (int)$aVals['x1'] . ' AND position_y = ' . (int)$aVals['y1'] . ' AND width = ' . (int)$aVals['width'] . ' AND height = ' . (int)$aVals['height'] . '')
                ->execute('getSlaveField');

            if ($iIsTagged) {
                return Phpfox_Error::set(_p('this_photo_is_already_tagged_in_the_same_position'));
            }

            $iTotalTags = db()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('photo_id = ' . (int)$aVals['item_id'] . ' AND user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');

            if (($aPhoto['user_id'] == Phpfox::getUserId() && $iTotalTags >= Phpfox::getUserParam('photo.how_many_tags_on_own_photo')) ||
                ($aPhoto['user_id'] != Phpfox::getUserId() && $iTotalTags >= Phpfox::getUserParam('photo.how_many_tags_on_other_photo'))
            ) {
                return Phpfox_Error::set(_p('no_more_tags_for_this_photo_can_be_added'));
            }


            if (!empty($aVals['note'])) {
                if (Phpfox::getLib('parse.format')->isEmpty($aVals['note'])) {
                    return Phpfox_Error::set(_p('provide_a_photo_tag'));
                }

                $aVals['note'] = Phpfox::getLib('parse.input')->clean($aVals['note'], 255);

                $sReturn = Phpfox::getLib('parse.output')->clean($aVals['note']);
            }

            Phpfox::getService('ban')->checkAutomaticBan($aVals['note']);

            $iTagUserId = 0;
            if (!empty($aVals['tag_user_id'])) {
                $aUser = Phpfox::getService('user')->getUser($aVals['tag_user_id'],
                    'u.user_id, u.user_name, u.full_name');
                if (isset($aUser['user_id'])) {
                    $iTagUserId = $aUser['user_id'];

                    $iIsUserTagged = db()->select('COUNT(*)')
                        ->from($this->_sTable)
                        ->where('photo_id = ' . (int)$aVals['item_id'] . ' AND tag_user_id = ' . (int)$iTagUserId . '')
                        ->execute('getSlaveField');

                    if ($iIsUserTagged) {
                        return Phpfox_Error::set(_p('full_name_has_already_been_tagged_in_this_photo',
                            array('full_name' => $aUser['full_name'])));
                    }

                    $sReturn = '<a href="' . Phpfox::getLib('url')->makeUrl($aUser['user_name']) . '">' . $aUser['full_name'] . '</a>';

                    unset($aVals['note']);
                }
            }

            if (empty($aVals['note']) && $iTagUserId < 1) {
                return null;
            }
            db()->insert($this->_sTable, array(
                    'photo_id' => (int)$aVals['item_id'],
                    'user_id' => Phpfox::getUserId(),
                    'tag_user_id' => $iTagUserId,
                    'content' => (empty($aVals['note']) ? null : $aVals['note']),
                    'time_stamp' => PHPFOX_TIME,
                    'photo_width' => (int)$aVals['photo_width'],
                    'position_x' => (int)$aVals['x1'],
                    'position_y' => (int)$aVals['y1'],
                    'width' => (int)$aVals['width'],
                    'height' => (int)$aVals['height']
                )
            );
            $aFeed = Phpfox::getService('feed')->getParentFeedItem('photo',$aVals['item_id']);
            if ($aFeed) {
                $iFeedId = $aFeed['feed_id'];
            } else {
                $iFeedId = db()->select('feed_id')->from(':photo_feed')->where('photo_id = '.$aVals['item_id'])->execute('getField');
            }
            if (!empty($iFeedId)) {
                db()->update(':feed', ['time_update' => PHPFOX_TIME], 'feed_id = ' . (int)$iFeedId);
            }
            (($sPlugin = Phpfox_Plugin::get('photo.service_tag_process_add__1')) ? eval($sPlugin) : false);

            $sLink = Phpfox::getLib('url')->permalink('photo', $aPhoto['photo_id'], $aPhoto['title']);

            Phpfox::getLib('mail')->to($iTagUserId)
                ->subject(array(
                    'photo.full_name_tagged_you_in_a_photo',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'user_id' => Phpfox::getUserId()
                    )
                ))
                ->message((Phpfox::getUserId() == $aPhoto['user_id'] ? _p('full_name_tagged_you_on_gender_photo', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'gender' => Phpfox::getService('user')->gender(Phpfox::getUserBy('gender'), 1),
                    'link' => $sLink,
                    'title' => $aPhoto['title']
                )) : _p('full_name_tagged_you_on_user_photo', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'other_full_name' => $aPhoto['full_name'],
                    'link' => $sLink,
                    'title' => $aPhoto['title']
                ))))
                ->send();

            Phpfox::getService('notification.process')->add('photo_tag', $aPhoto['photo_id'], $iTagUserId);

            return $sReturn;
        }

        return Phpfox_Error::set(_p('not_allowed_to_tag_this_photo'));
    }

    public function delete($iId)
    {
        Phpfox::isUser(true);

        $aTag = db()->select('pt.tag_user_id, p.photo_id, p.user_id AS photo_owner_id, pt.tag_id, pt.user_id AS post_user_id')
            ->from($this->_sTable, 'pt')
            ->join(Phpfox::getT('photo'), 'p', 'p.photo_id = pt.photo_id')
            ->where('pt.tag_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aTag['tag_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_photo'));
        }

        if (($aTag['post_user_id'] == Phpfox::getUserId() || $aTag['photo_owner_id'] == Phpfox::getUserId() || $aTag['tag_user_id'] == Phpfox::getUserId())) {
            db()->delete($this->_sTable, 'tag_id = ' . (int)$aTag['tag_id']);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('photo_tag', $aTag['tag_id']) : null);

            (($sPlugin = Phpfox_Plugin::get('photo.service_tag_process_delete__1')) ? eval($sPlugin) : false);

            return $aTag['photo_id'];
        }

        return Phpfox_Error::set(_p('unable_to_delete_this_tag'));
    }

    public function deleteAll($iPhotoId)
    {
        Phpfox::isUser(true);

        db()->delete(Phpfox::getT('photo_tag'),
            'photo_id = ' . (int)$iPhotoId . ' AND user_id = ' . Phpfox::getUserId());
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
        if ($sPlugin = Phpfox_Plugin::get('photo.service_tag_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
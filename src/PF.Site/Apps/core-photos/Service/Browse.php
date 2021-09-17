<?php

namespace Apps\Core_Photos\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Browse extends Phpfox_Service
{
    private $_sCategory = null;

    /**
     * Class constructor
     */
    public function __construct()
    {

    }

    public function category($sCategory)
    {
        $this->_sCategory = $sCategory;

        return $this;
    }

    public function query()
    {
        db()->select('pa.name AS album_name, pa.profile_id AS album_profile_id, pa.cover_id AS album_cover_id, pa.timeline_id AS album_timeline_id, ppc.name as category_name, ppc.category_id, ')
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = photo.album_id')
            ->leftJoin(Phpfox::getT('photo_category_data'), 'ppcd', 'ppcd.photo_id = photo.photo_id')
            ->leftJoin(Phpfox::getT('photo_category'), 'ppc', 'ppc.category_id = ppcd.category_id')
            ->group('photo.photo_id', true);

        if (Phpfox::isModule('like')) {
            db()->select('l.like_id as is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'photo\' AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId() . '');
        }

        if (Phpfox::getLib('request')->get('mode') == 'edit') {
            db()->select('pi.description, ')->leftJoin(Phpfox::getT('photo_info'), 'pi',
                'pi.photo_id = photo.photo_id');
        }
    }

    public function processRows(&$aRows)
    {
        $bShowTopic = Phpfox::isModule('tag') && \Phpfox_Request::instance()->get('mode') == 'edit';
        foreach ($aRows as $iKey => $aRow) {
            $oReq = Phpfox::getLib('request');
            $aRow['link'] = Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']);
            if ((Phpfox::getUserId() && defined('PHPFOX_IS_USER_PROFILE'))
                || $oReq->get('req1') == 'photo' && $oReq->get('view') == 'my'
            ) {
                $aRow['link'] .= 'userid_' . $aRow['user_id'] . '/myphotos_1/';
            }

            $aRow['destination'] = Phpfox::getService('photo')->getPhotoUrl($aRow);
            if ($oReq->get('mode') == 'edit') {
                $sCategoryList = '';
                $aCategories = (array)db()->select('category_id')
                    ->from(Phpfox::getT('photo_category_data'))
                    ->where('photo_id = ' . (int)$aRow['photo_id'])
                    ->execute('getSlaveRows');

                foreach ($aCategories as $aCategory) {
                    $sCategoryList .= $aCategory['category_id'] . ',';
                }
                $aRow['category_list'] = rtrim($sCategoryList, ',');
            }

            if ($aRow['album_id'] > 0) {
                if ($aRow['album_profile_id'] > 0) {
                    $aRow['album_name'] = _p('user_profile_pictures', ['full_name' => $aRow['full_name']]);;
                    $aRow['album_url'] = Phpfox::permalink('photo.album.profile', $aRow['user_id'], $aRow['user_name']);
                }
                if ($aRow['album_cover_id'] > 0) {
                    $aRow['album_name'] = _p('user_cover_photo', ['full_name' => $aRow['full_name']]);
                    $aRow['album_url'] = Phpfox::permalink('photo.album.cover', $aRow['user_id'], $aRow['user_name']);
                }
                if ($aRow['album_timeline_id'] > 0) {
                    $aRow['album_name'] = _p('user_timeline_photos', ['full_name' => $aRow['full_name']]);
                    $aRow['album_url'] = Phpfox::permalink('photo.album', $aRow['album_id'], $aRow['album_name']);
                } else {
                    $aRow['album_url'] = Phpfox::permalink('photo.album', $aRow['album_id'], $aRow['album_name']);
                }
            }
            if ($bShowTopic) {
                $aTags = Phpfox::getService('tag')->getTagsById('photo', $aRow['photo_id']);
                if (isset($aTags[$aRow['photo_id']])) {
                    $aRow['tag_list'] = '';
                    foreach ($aTags[$aRow['photo_id']] as $aTag) {
                        $aRow['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aRow['tag_list'] = trim(trim($aRow['tag_list'], ','));
                }
            }
            Phpfox::getService('photo')->getPermissions($aRow);
            $aRows[$iKey] = $aRow;
        }
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            db()->join(Phpfox::getT('friend'), 'friends',
                'friends.user_id = photo.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if ($this->request()->get('req2') == 'tag') {
            db()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = photo.photo_id AND tag.category_id = \'photo\'');
        }

        if ($this->_sCategory !== null || (isset($_SESSION['photo_category']) && $_SESSION['photo_category'] != '')) {
            db()->innerJoin(Phpfox::getT('photo_category_data'), 'pcd', 'pcd.photo_id = photo.photo_id');
            if (!$bIsCount) {
                db()->group('photo.photo_id');
            }
        }
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
        if ($sPlugin = Phpfox_Plugin::get('photo.service_browse__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
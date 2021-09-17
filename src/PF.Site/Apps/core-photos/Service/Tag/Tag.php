<?php

namespace Apps\Core_Photos\Service\Tag;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Tag extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('photo_tag');
    }

    public function getJs($iPhotoId)
    {
        $sJs = 'id: \'#js_photo_view_image\',tagged_class: \'.js_tagged_section\', active_class:\'.photos_tag\', tag_link_id: \'#js_tag_photo\', name: \'val[tag]\', item_id: ' . $iPhotoId . ', in_photo: \'#js_photo_in_this_photo\'';
        if (($sNotes = $this->getJavascript($iPhotoId))) {
            $sJs .= ', notes: ' . $sNotes . '';
            $sNotes = preg_replace('/\(<a(.*?)>(.*?)<\/a>\)/i', '', $sNotes);
            $sNotes = preg_replace('/<a(.*?)>(.*?)<\/a>/i', '\\2', $sNotes);
            $sJs .= ', js_notes: ' . $sNotes . '';
        }
        if (Phpfox::isUser()) {
            $sJs .= ', user_id: ' . Phpfox::getUserId();
        }

        return $sJs;
    }

    public function getJavascript($iPhotoId)
    {
        $aTags = db()->select('p.user_id AS photo_owner_id, pt.tag_id, pt.user_id AS post_user_id, pt.content, pt.position_x, pt.position_y, pt.width, pt.height, pt.photo_width, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'pt')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = pt.tag_user_id')
            ->join(Phpfox::getT('photo'), 'p', 'p.photo_id = pt.photo_id')
            ->where('pt.photo_id = ' . (int)$iPhotoId)
            ->execute('getSlaveRows');

        if (!count($aTags)) {
            return false;
        }

        $sNotes = '[';
        foreach ($aTags as $aTag) {
            $sNotes .= '{';
            $sNotes .= 'note_id: ' . $aTag['tag_id'] . ', ';
            $sNotes .= 'x1: ' . $aTag['position_x'] . ', ';
            $sNotes .= 'y1: ' . $aTag['position_y'] . ', ';
            $sNotes .= 'width: ' . $aTag['width'] . ', ';
            $sNotes .= 'height: ' . $aTag['height'] . ', ';
            $sNotes .= 'name: \'' . $aTag['full_name'] . '\', ';
            $sNotes .= 'photo_width: ' . $aTag['photo_width'] . ', ';

            $sRemove = (($aTag['post_user_id'] == Phpfox::getUserId() || $aTag['photo_owner_id'] == Phpfox::getUserId() || $aTag['user_id'] == Phpfox::getUserId()) ? ' <a href="#" onclick="var obj = this;$Core.jsConfirm({message:\\\'' . _p('are_you_sure') . '\\\'}, function() { $(\\\'#noteform\\\').hide(); $(\\\'#js_photo_view_image\\\').imgAreaSelect({ hide: true }); $(obj).parent(\\\'span:first\\\').remove();$(\\\'.notep#notep_' . $aTag['tag_id'] . '\\\').remove();$.ajaxCall(\\\'photo.removePhotoTag\\\', \\\'tag_id=' . $aTag['tag_id'] . '\\\'); }, function(){}); return false;"><i class="ico ico-close"></i></a>' : '');

            if (!empty($aTag['user_id'])) {
                $sNotes .= 'user_href: \'' . Phpfox::getLib('url')->makeUrl($aTag['user_name']) . '\', ';
                $sNotes .= 'note: \'<a class="user_link" onclick="$(\\\'.note , .notep\\\').remove();" href="' . Phpfox::getLib('url')->makeUrl($aTag['user_name']) . '" id="js_photo_tag_user_id_' . $aTag['user_id'] . '">' . $aTag['full_name'] . '</a>' . $sRemove . '\'';
            } else {
                $sNotes .= 'user_href:\'\', ';
                $sNotes .= 'note: \'' . str_replace("'", "\'",
                        Phpfox::getLib('parse.output')->clean($aTag['content'])) . $sRemove . '\'';
            }
            $sNotes .= '},';
        }
        $sNotes = rtrim($sNotes, ',');
        $sNotes .= ']';
        return $sNotes;
    }

    /**
     * @param $aPhotoIds
     * @param null $iUserId
     * @return array|int|string
     */
    public function getTagByIds($aPhotoIds, $iUserId = null)
    {
        if (is_array($aPhotoIds)) {
            $sPhotoIds = implode(',',$aPhotoIds);
        } else {
            $sPhotoIds = $aPhotoIds;
        }
        $aTags = db()->select('pt.tag_user_id, '. Phpfox::getUserField())
                    ->from($this->_sTable,'pt')
                    ->join(':user','u','u.user_id = pt.tag_user_id')
                    ->where('pt.photo_id IN ('.$sPhotoIds.')'.(($iUserId != null) ? ' AND pt.tag_user_id != '. (int)$iUserId : ''))
                    ->group('pt.tag_user_id')
                    ->execute('getSlaveRows');
        return $aTags;
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
        if ($sPlugin = Phpfox_Plugin::get('photo.service_tag_tag__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
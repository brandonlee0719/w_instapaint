<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Controller;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class UploadController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        $aCallback = false;
        $aEditSong = array();
        $bIsEdit = false;
        $iAlbumId = $this->request()->getInt('album', 0);
        $bCanSelectAlbum = true;

        $aValidation = array(
            'title' => _p('provide_a_name_for_this_song')
        );

        $oValidator = \Phpfox_Validator::instance()->set([
            'sFormName' => 'js_music_form',
            'aParams' => $aValidation
        ]);

        if (($iId = $this->request()->getInt('id')) && ($aEditSong = \Phpfox::getService('music')->getForEdit($iId))) {
            if ($aEditSong['module_id'] == 'pages' || $aEditSong['module_id'] == 'groups') {
                Phpfox::getService($aEditSong['module_id'])->setIsInPage();
            }
            if($aEditSong['user_id'] != Phpfox::getUserId())
            {
                $bCanSelectAlbum = false;
            }
            $sModule = !empty($aEditSong['module_id']) ? $aEditSong['module_id'] : false;
            $iItem = $aEditSong['item_id'];
            $bIsEdit = true;
            $this->template()->assign(array(
                    'aForms' => $aEditSong,
                    'iAlbumId' => $iAlbumId
                )
            );
        }
        if (!$bIsEdit) {
            Phpfox::getUserParam('music.can_upload_music_public', true);
        }

        if ($sModule !== false && $iItem !== false && Phpfox::hasCallback($sModule, 'getMusicDetails')) {
            if (($aCallback = Phpfox::callback($sModule . '.getMusicDetails', array('item_id' => $iItem)))) {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
                if (!$bIsEdit && Phpfox::hasCallback($sModule,
                        'checkPermission') && !Phpfox::callback($sModule . '.checkPermission', $iItem,
                        'music.share_music')
                ) {
                    return \Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                }
            } else {
                return \Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
        } else {
            if ($sModule && $iItem && $aCallback === false) {
                return \Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
        }
        $aVals = $this->request()->getArray('val');
        if ($bIsEdit && !empty($aVals) && $this->request()->get('upload_via_song')) {
            if ($oValidator->isValid($aVals)) {
                if (\Phpfox::getService('music.process')->update($aEditSong['song_id'], $aVals)) {
                    if ($iAlbumId) {
                        $this->url()->send('music.album.manage', ['id' => $iAlbumId], 'Song successfully updated.');
                    } else {
                        $this->url()->permalink('music', $aEditSong['song_id'], $aEditSong['title'], true,
                            'Song successfully updated.');
                    }
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('editing_song') . ': ' . $aEditSong['title'] : _p('share_songs')))
            ->setBreadCrumb(_p('music'),
                ($aCallback === false ? $this->url()->makeUrl('music') : $aCallback['url_home_photo']))
            ->setBreadCrumb(($bIsEdit ? _p('editing_song') . ': ' . $aEditSong['title'] : _p('share_songs')),
                ($bIsEdit) ? $this->url()->makeUrl('music.upload',
                    array('id' => $iId)) : $this->url()->makeUrl('music.upload'), true)
            ->setPhrase(array(
                    'select_an_mp3',
                    'please_select_a_valid_mp3_file_to_upload',
                    'provide_name_for_each_song',
                    'invalid_file_extension',
                    'maximum_number_of_songs_you_can_upload_each_time_is',
                    'provide_a_name_for_this_song',
                    'browse_three_dot',
                    'notice',
                    'add_more_files',
                    'uploading_three_dot',
                    'please_do_not_refresh_the_current_page_or_close_the_browser_window'
                )
            )
            ->setHeader('cache', array(
                    'progress.css' => 'style_css',
                    'progress.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.onLoadFormComplete = function(){ $Core.music.sModule = \''.$sModule.'\';$Core.music.iItemId = '.$iItem.';}</script>'
                )
            )
            ->assign(array(
                    'iItem' => $iItem,
                    'sModule' => $sModule,
                    'bIsEdit' => $bIsEdit,
                    'aUploadAlbums' => Phpfox::getService('music.album')->getForUpload($aCallback),
                    'sCreateJs' => $oValidator->createJS(),
                    'sGetJsForm' => $oValidator->getJsForm(false),
                    'iUploadLimit' => (Phpfox::getUserParam('music.music_max_file_size') > 0) ? Phpfox::getLib('file')->getLimit(Phpfox::getUserParam('music.music_max_file_size')) : 0,
                    'iMaxFileSize' => (Phpfox::getUserParam('music.music_max_file_size') > 0) ? Phpfox::getUserParam('music.music_max_file_size') : 0,
                    'iPhotoMaxFileSize' => Phpfox::isModule('photo') ? Phpfox::getUserParam('photo.photo_max_upload_size') : 0,
                    'sJavaScriptEditLink' => ($bIsEdit ? "\$Core.jsConfirm({message: '" . _p('are_you_sure',
                            array('phpfox_squote' => true)) . "'}, function(){ $('#js_submit_upload_image').show(); $('#js_music_upload_image').show(); $('#js_music_current_image').remove(); $.ajaxCall('music.deleteSongImage', 'id={$aEditSong['song_id']}'); },function(){}); return false;" : ''),
                    'iMaxFileUpload' => Phpfox::getUserParam('music.max_songs_per_upload'),
                    'aGenres' => Phpfox::getService('music.genre')->getList(1),
                    'bCanSelectAlbum' => $bCanSelectAlbum,
                    'aAlbums' => $bCanSelectAlbum ? Phpfox::getService('music.album')->getAll(Phpfox::getUserId(), $sModule, $iItem) : ($aEditSong['album_id'] > 0 ? Phpfox::getService('music.album')->getAlbums(false,1,'ma.album_id = '.(int)$aEditSong['album_id'],true) : []),
                    'iTimestamp' => PHPFOX_TIME
                )
            );

        if (!empty($aEditSong)) {
            $this->template()->buildPageMenu('js_music_song_block', [], [
                'link' => Phpfox::permalink('music', $aEditSong['song_id'], $aEditSong['title']),
                'phrase' => _p('view_song')
            ]);
        }

        if ($bIsEdit) {
            if (Phpfox::isModule('attachment')) {
                $this->setParam('attachment_share', array(
                        'type' => 'music_song',
                        'id' => 'js_file_holder_' . $iId,
                        'edit_id' => $iId,

                    )
                );
            }
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_upload_clean')) ? eval($sPlugin) : false);
    }
}
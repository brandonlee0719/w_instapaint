<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Music\Controller;

use Phpfox;
use Phpfox_File;
use Phpfox_Validator;

defined('PHPFOX') or exit('NO DICE!');

class AlbumController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($this->request()->getInt('req3') > 0) {
            return \Phpfox_Module::instance()->setController('music.view-album');
        }

        Phpfox::getUserParam('music.can_access_music', true);
        Phpfox::isUser(true);


        $bIsEdit = false;
        $aAlbum = array();
        $sAction = $this->request()->get('req3');
        $aVals = $this->request()->getArray('val');
        $sModule = $this->request()->get('module', false);
        $iItem = $this->request()->getInt('item', false);

        $aCallback = false;

        if (($iEditId = $this->request()->getInt('id')) || ($iEditId = $this->request()->getInt('album_edit_id'))) {
            if (($aAlbum = Phpfox::getService('music.album')->getForEdit($iEditId))) {
                if ($aAlbum['module_id'] == 'pages') {
                    Phpfox::getService('pages')->setIsInPage();
                }
                $iPage = $this->request()->getInt('page', 1);
                $iPageSize = 10;
                list($iCount, $aSongs) = Phpfox::getService('music')->getForManage($aAlbum['user_id'], $iEditId, $iPageSize, $iPage);

                Phpfox::getLib('pager')->set([
                    'page' => $this->request()->get('page', 1),
                    'size' => $iPageSize,
                    'count' => $iCount,
                    'paging_mode' => 'pagination'
                ]);
                $bIsEdit = true;
                $this->template()->assign(array(
                        'aForms' => $aAlbum,
                        'aSongs' => $aSongs,
                        'bNoTitle' => true,
                        'iPage' => $this->request()->getInt('page', 0)
                    )
                );
            }
        } else {
            Phpfox::getUserParam('music.can_add_music_album', true);
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

        $aValidation = array(
            'name' => _p('provide_a_name_for_this_album'),
            'year' => array(
                'def' => 'year'
            )
        );

        $oValidator = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_album_form',
                'aParams' => $aValidation
            )
        );

        if ($aVals) {
            if ($oValidator->isValid($aVals)) {
                if ($bIsEdit) {
                    if (\Phpfox::getService('music.album.process')->update($aAlbum['album_id'], $aVals)) {
                        switch ($sAction) {
                            case 'track':
                                $this->url()->permalink('music.album', $aAlbum['album_id'], $aAlbum['name'], true,
                                    _p('tracks_successfully_uploaded') . (Phpfox::getUserParam('music.music_song_approval') ? ' ' . _p('note_that_it_will_have_to_be_approved_first_before_it_is_displayed_publicly') : ''));
                                break;
                            default:
                                $this->url()->permalink('music.album', $aAlbum['album_id'], $aAlbum['name'], true,
                                    _p('album_successfully_updated'));
                                break;
                        }
                    }
                } else {
                    if ($iId = \Phpfox::getService('music.album.process')->add($aVals)) {
                        if (Phpfox::getUserParam('music.can_upload_music_public')) {
                            $this->url()->send('music.album.track.setup', array('id' => $iId),
                                _p('album_successfully_added'));

                        } else {
                            $this->url()->send('music.album.manage', array('id' => $iId),
                                _p('album_successfully_added'));
                        }
                    }
                }
            }
        }

        if ($bIsEdit) {
            $aPageMenu = array(
                'detail' => _p('album_details'),
                'manage' => _p('manage_songs')
            );
            if (Phpfox::getUserParam('music.can_upload_music_public') && $aAlbum['user_id'] == Phpfox::getUserId()) {
                $aPageMenu['track'] = _p('upload_songs');

            }
            $this->template()->buildPageMenu('js_upload_music',
                $aPageMenu,
                array(
                    'link' => $this->url()->permalink('music.album', $aAlbum['album_id'], $aAlbum['name']),
                    'phrase' => _p('view_this_album')
                )
            );

            $this->setParam(array(
                    'album_user_id' => $aAlbum['user_id'],
                    'album_id' => $aAlbum['album_id'],
                    'album_view_all' => true
                )
            );
        }

        $this->template()->setTitle(($bIsEdit ? _p('editing_album') . ': ' . $aAlbum['name'] : _p('create_album')))
            ->setBreadCrumb(_p('music'), $this->url()->makeUrl('music'))
            ->setBreadCrumb(($bIsEdit ? _p('update_album') . ': ' . $aAlbum['name'] : _p('create_album')),
                $this->url()->makeUrl('current'), true)
            ->setPhrase(array(
                    'select_an_mp3',
                    'select_a_file_to_upload',
                    'please_select_a_valid_mp3_file_to_upload',
                    'provide_name_for_each_song',
                    'invalid_file_extension',
                    'maximum_number_of_songs_you_can_upload_each_time_is',
                    'are_you_sure_you_want_to_delete_this_song',
                    'browse_three_dot',
                    'browse_three_dot',
                    'notice',
                    'add_more_files',
                    'uploading_three_dot',
                    'please_do_not_refresh_the_current_page_or_close_the_browser_window'
                )
            )
            ->setHeader(array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'progress.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.musicAlbumCreate = function(){ if ($(\'#js_upload_music_'.$sAction.'\').length > 0) { $Core.pageSectionMenuShow(\'#js_upload_music_'.$sAction.'\'); }}</script>'
                )
            )
            ->assign(array(
                    'sModule' => $sModule,
                    'iItem' => $iItem,
                    'bIsEdit' => $bIsEdit,
                    'sCreateJs' => $oValidator->createJS(),
                    'sGetJsForm' => $oValidator->getJsForm(false),
                    'iUploadLimit' => Phpfox_File::instance()->getLimit(Phpfox::getUserParam('music.music_max_file_size')),
                    'sJavaScriptEditLink' => ($bIsEdit ? "\$Core.jsConfirm({message: '" . _p('are_you_sure',
                            array('phpfox_squote' => true)) . "'}, function(){ $('#js_submit_upload_image').show(); $('#music-dropzone').show(); $('#js_music_current_image').remove();$('#image_path').val('');$.ajaxCall('music.deleteImage', 'id={$aAlbum['album_id']}'); },function(){}); return false;" : ''),
                    'sMethodUrl' => ($bIsEdit ? $this->url()->makeUrl('music.album.track',
                        array('id' => $aAlbum['album_id'], 'method' => 'simple')) : ''),
                    'sActionMethod' => $sAction,
                    'bIsEditAlbum' => true,
                    'iMaxFileUpload' => Phpfox::getUserParam('music.max_songs_per_upload'),
                    'iMaxFileSize' => (Phpfox::getUserParam('music.music_max_file_size') > 0) ? Phpfox::getUserParam('music.music_max_file_size') : 0,
                    'aGenres' => Phpfox::getService('music.genre')->getList(1),
                    'iPhotoMaxFileSize' => Phpfox::isModule('photo') ? Phpfox::getUserParam('photo.photo_max_upload_size') : 0,
                    'iTimestamp' => PHPFOX_TIME
                )
            );
        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                    'type' => 'music_album',
                    'id' => 'js_actual_upload_form',
                    'edit_id' => ($bIsEdit ? $iEditId : 0),

                )
            );
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_album_clean')) ? eval($sPlugin) : false);
    }
}
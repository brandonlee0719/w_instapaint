<?php

namespace Apps\PHPfox_Videos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Mail;
use Phpfox_Plugin;
use Phpfox_Validator;

defined('PHPFOX') or exit('NO DICE!');

class ShareController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        user('pf_video_share', '1', null, true);
        $bIsAjaxBrowsing = ($this->request()->get('is_ajax_browsing') ? true : false);

        //Support sharing video on feed in case login as page/group
        $iProfilePageId = Phpfox::getUserBy('profile_page_id');
        if ($iProfilePageId && $bIsAjaxBrowsing) {
            if (($sModuleId = Phpfox::getLib('pages.facade')->getPageItemType($iProfilePageId)) == 'groups') {
                Phpfox::getService('groups')->setIsInPage();
            } elseif ($sModuleId == 'pages') {
                Phpfox::getService('pages')->setIsInPage();
            }
        }

        $bUploadSuccess = false;
        if ($sPlugin = Phpfox_Plugin::get('video.component_controller_add_1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }

        $sModule = $this->request()->get('module', false);
        $iItemId = $this->request()->getInt('item', false);
        $aCallback = false;
        if ($sModule !== false && $iItemId !== false && Phpfox::hasCallback($sModule, 'getItem')) {
            if ($sPlugin = Phpfox_Plugin::get('video.component_controller_add_2')) {
                eval($sPlugin);
                if (isset($mReturnFromPlugin)) {
                    return $mReturnFromPlugin;
                }
            }
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
            if ($aCallback === false) {
                return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
            }
            $bCheckParentPrivacy = true;
            if (Phpfox::hasCallback($sModule, 'checkPermission')) {
                $bCheckParentPrivacy = Phpfox::callback($sModule . '.checkPermission', $iItemId,
                    'pf_video.share_videos');
            }
            if (!$bCheckParentPrivacy) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
            $this->template()
                ->setBreadCrumb(isset($aCallback['module_title']) ? $aCallback['module_title'] : _p($sModule),
                    $this->url()->makeUrl($sModule))
                ->setBreadCrumb($aCallback['title'], Phpfox::permalink($sModule, $iItemId))
                ->setBreadCrumb(_p('videos'), $this->url()->makeUrl($sModule, array($iItemId, 'video')))
                ->assign([
                    'sModule' => $sModule,
                    'iItemId' => $iItemId
                ]);
        } else {
            if (!empty($sModule) && !empty($iItemId) && $sModule != 'video' && $aCallback === null) {
                return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
            }

            $this->template()
                ->setBreadCrumb(_p('videos'), $this->url()->makeUrl('video'));

        }
        $this->template()->setBreadCrumb(_p('share_a_video'), $this->url()->current(), true);

        $aValidation = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('provide_a_title_for_this_video')
            )
        );

        $iMethodUpload = setting('pf_video_method_upload');
        $bAllowVideoUploading = false;
        if (setting('pf_video_support_upload_video') && (($iMethodUpload == 1 && setting('pf_video_key')) || ($iMethodUpload == 0 && setting('pf_video_ffmpeg_path')))) {
            $bAllowVideoUploading = true;
        }

        (($sPlugin = Phpfox_Plugin::get('video.component_controller_share_process_validation')) ? eval($sPlugin) : false);
        if (!$bIsAjaxBrowsing) {
            $oValid = Phpfox_Validator::instance()->set(array(
                    'sFormName' => 'core_js_video_form',
                    'aParams' => $aValidation
                )
            );

            if (($aVals = $this->request()->get('val'))) {
                if ($sPlugin = Phpfox_Plugin::get('video.component_controller_add_3')) {
                    eval($sPlugin);
                    if (isset($mReturnFromPlugin)) {
                        return $mReturnFromPlugin;
                    }
                }

                if ($oValid->isValid($aVals)) {
                    if ($sPlugin = Phpfox_Plugin::get('video.component_controller_add_4')) {
                        eval($sPlugin);
                        if (isset($mReturnFromPlugin)) {
                            return $mReturnFromPlugin;
                        }
                    }
                    if (isset($aVals['pf_video_id'])) {
                        if (empty($aVals['pf_video_id'])) {
                            return Phpfox_Error::display(_p('we_could_not_find_a_video_there_please_try_again'));
                        }
                        $encoding = storage()->get('pf_video_' . $aVals['pf_video_id']);
                        if (!empty($encoding->value->encoded)) {
                            $aVals = array_merge($aVals, array(
                                'is_stream' => 0,
                                'user_id' => $encoding->value->user_id,
                                'server_id' => $encoding->value->server_id,
                                'path' => $encoding->value->video_path,
                                'ext' => $encoding->value->ext,
                                'default_image' => isset($encoding->value->default_image) ? $encoding->value->default_image : '',
                                'image_path' => isset($encoding->value->image_path) ? $encoding->value->image_path : '',
                                'image_server_id' => $encoding->value->image_server_id,
                                'duration' => $encoding->value->duration,
                                'video_size' => $encoding->value->video_size,
                                'photo_size' => $encoding->value->photo_size
                            ));
                            $iId = Phpfox::getService('v.process')->addVideo($aVals);

                            if (Phpfox::isModule('notification')) {
                                Phpfox::getService('notification.process')->add('v_ready', $iId,
                                    $encoding->value->user_id, $encoding->value->user_id, true);
                            }

                            Phpfox_Mail::instance()->to($encoding->value->user_id)
                                ->subject(_p('video_is_ready'))
                                ->message(_p('your_video_is_ready') . '.<br />' . url('/video/play/' . $iId))
                                ->send();

                            $file = PHPFOX_DIR_FILE . 'static/' . $encoding->value->id . '.' . $encoding->value->ext;
                            if (file_exists($file)) {
                                @unlink($file);
                            }

                            storage()->del('pf_video_' . $aVals['pf_video_id']);
                        } else {
                            if ($iMethodUpload == 0 && setting('pf_video_ffmpeg_path')) {
                                $iJobId = \Phpfox_Queue::instance()->addJob('videos_ffmpeg_encode', []);
                                storage()->set('pf_video_' . $iJobId, [
                                    'encoding_id' => $iJobId,
                                    'id' => $encoding->value->id,
                                    'user_id' => $encoding->value->user_id,
                                    'path' => $encoding->value->path,
                                    'ext' => $encoding->value->ext,
                                    'privacy' => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
                                    'callback_module' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : ''),
                                    'callback_item_id' => (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : 0),
                                    'parent_user_id' => (isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0),
                                    'title' => $aVals['title'],
                                    'category' => json_encode(isset($aVals['category']) ? $aVals['category'] : []),
                                    'text' => $aVals['text'],
                                    'status_info' => ''
                                ]);
                                storage()->del('pf_video_' . $aVals['pf_video_id']);
                            } else {
                                storage()->update('pf_video_' . $aVals['pf_video_id'], [
                                    'privacy' => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
                                    'callback_module' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : ''),
                                    'callback_item_id' => (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : 0),
                                    'parent_user_id' => (isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0),
                                    'title' => $aVals['title'],
                                    'category' => json_encode(isset($aVals['category']) ? $aVals['category'] : []),
                                    'text' => $aVals['text'],
                                    'status_info' => '',
                                    'updated_info' => 1
                                ]);
                            }
                        }

                        $bUploadSuccess = true;
                    } elseif (!empty($aVals['url']) && $parsed = Phpfox::getService('link')->getLink($aVals['url'])) {
                        if (empty($parsed['embed_code'])) {
                            return Phpfox_Error::display(_p('unable_to_load_a_video_to_embed'));
                        }
                        if (isset($parsed['duration'])) {
                            $aVals['duration'] = $parsed['duration'];
                        }
                        if ($iId = Phpfox::getService('v.process')->addVideo($aVals)) {
                            $this->url()->permalink('video.play', $iId, $aVals['title'], true,
                                _p('video_successfully_added'));
                        }
                    } else {
                        $this->template()
                            ->assign(array(
                                    'bAddFalse' => 1
                                )
                            );
                    }
                }
            }

            $this->template()
                ->assign(array(
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm()
                ));
        }

        define('PHPFOX_APP_DETAIL_PAGE', true);
        $this->template()->setTitle(_p('share_a_video'))
            ->assign(array(
                    'bIsAjaxBrowsing' => $bIsAjaxBrowsing,
                    'sModule' => $sModule,
                    'iItemId' => $iItemId,
                    'bAllowVideoUploading' => $bAllowVideoUploading,
                    'ivideoFileSize' => user('pf_video_file_size'),
                    'bUploadSuccess' => $bUploadSuccess
                )
            );

        (($sPlugin = Phpfox_Plugin::get('video.component_controller_share_process')) ? eval($sPlugin) : false);

        return 'controller';
    }
}

<?php

namespace Apps\PHPfox_Videos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Validator;

defined('PHPFOX') or exit('NO DICE!');

class EditController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $bIsEdit = false;
        $sModule = $iItemId = '';
        $aVideo = [];
        if (($iId = $this->request()->getInt('id'))) {
            if (($aVideo = Phpfox::getService('v.video')->getForEdit($iId))) {
                if (empty($aVideo) || empty($aVideo['video_id'])) {
                    return Phpfox_Error::display(_p('video_not_found'));
                }

                if (!(($aVideo['user_id'] == Phpfox::getUserId() && user('pf_video_edit_own_video')) || user('pf_video_edit_all_video'))) {
                    return Phpfox_Error::display(_p('unable_to_edit_this_video'));
                }
                $aCategories = Phpfox::getService('v.category')->getCategoriesByVideoId($aVideo['video_id']);
                $this->setParam('aSelectedCategories', $aCategories);
                if (!empty($aVideo['module_id'])) {
                    $sModule = $aVideo['module_id'];
                    $iItemId = $aVideo['item_id'];
                }

                $bIsEdit = true;
            }
        }

        if ($bIsEdit === false) {
            return Phpfox_Error::display(_p('unable_to_edit_this_video'));
        }

        $aValidation = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('provide_a_title_for_this_video')
            )
        );

        (($sPlugin = Phpfox_Plugin::get('video.component_controller_edit_process_validation')) ? eval($sPlugin) : false);

        $oValid = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'core_js_video_form',
                'aParams' => $aValidation
            )
        );

        $aCallback = null;

        if (!empty($sModule) && Phpfox::hasCallback($sModule, 'getItem')) {
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
            if ($aCallback === false) {
                return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
            }
            $sUrl = $this->url()->makeUrl('video', array('edit', 'id' => $iId));
            $sCrumb = _p('editing_video') . ': ' . Phpfox::getLib('parse.output')->shorten($aVideo['title'],
                    Phpfox::getService('core')->getEditTitleSize(), '...');

            $this->template()
                ->setBreadCrumb(isset($aCallback['module_title']) ? $aCallback['module_title'] : _p($sModule),
                    $this->url()->makeUrl($sModule))
                ->setBreadCrumb($aCallback['title'], Phpfox::permalink($sModule, $iItemId))
                ->setBreadCrumb(_p('videos'), $this->url()->makeUrl($sModule, array($iItemId, 'video')))
                ->setBreadCrumb($sCrumb, $sUrl, true);
        } else {
            if (!empty($sModule) && ($sModule != 'user') && !empty($iItemId) && $sModule != 'video' && $aCallback === null) {
                return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
            }

            $this->template()
                ->setBreadCrumb(_p('videos'), $this->url()->makeUrl('video'))
                ->setBreadCrumb(_p('editing_video') . ': ' . Phpfox::getLib('parse.output')->shorten($aVideo['title'],
                        Phpfox::getService('core')->getEditTitleSize(), '...'),
                    $this->url()->makeUrl('video', array('edit', 'id' => $iId)), true);

        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($oValid->isValid($aVals)) {
                if (Phpfox::getService('v.process')->update($aVideo['video_id'], $aVals)) {
                    $this->url()->permalink('video.play', $aVideo['video_id'], $aVideo['title'], true,
                        _p('video_successfully_updated'));
                }
            }
        }
        if ($aVideo['image_path']) {
            Phpfox::getService('v.video')->convertImagePath($aVideo);
        }

        $this->template()->setTitle(_p('editing_video') . ': ' . $aVideo['title'])
            ->assign(array(
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'aForms' => $aVideo,
                    'iMaxFileSize' => Phpfox::getLib('phpfox.file')->filesize((user('pf_video_max_file_size_photo_upload',
                                500) / 1024) * 1048576)
                )
            );

        (($sPlugin = Phpfox_Plugin::get('video.component_controller_edit_process')) ? eval($sPlugin) : false);

        return 'controller';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_controller_edit_clean')) ? eval($sPlugin) : false);
    }
}

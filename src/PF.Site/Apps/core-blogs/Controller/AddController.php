<?php

namespace Apps\Core_Blogs\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class AddController
 * @package Apps\Core_Blogs\Controller
 */
class AddController extends Phpfox_Component
{
    const PHOTO_FIELD = 'image';
    const IMG_SUFFIX = '_240';

    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);
        $bIsEdit = false;
        $bIsPublish = $this->request()->getInt('publish', false);
        $iMaxFileSize = (user('blog_photo_max_upload_size') === 0 ? null : ((user('blog_photo_max_upload_size') / 1024) * 1048576));

        $sModule = $this->request()->get('module');
        $iItemId = $this->request()->getInt('item');
        if (($aVals = $this->request()->getArray('val')) && !empty($aVals['module_id']) && !empty($aVals['item_id'])) {
            $sModule = $aVals['module_id'];
            $iItemId = $aVals['item_id'];
        }

        if (($iEditId = $this->request()->getInt('id'))) {
            $oBlog = Phpfox::getService('blog');
            $aRow = $oBlog->getBlogForEdit($iEditId);

            // Check permission before edit
            if (!empty($aRow['module_id']) && !empty($aRow['item_id'])) {
                if (isset($aRow['module_id']) && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'],
                        'checkPermission')) {
                    if (!Phpfox::callback($aRow['module_id'] . '.checkPermission', $aRow['item_id'],
                        'blog.view_browse_blogs')) {
                        return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                    }
                }
                $sModule = $aRow['module_id'];
                $iItemId = $aRow['item_id'];
            }

            if (empty($aRow) || empty($aRow['blog_id'])) {
                return Phpfox_Error::display(_p('blog_not_found'));
            }

            if (!Phpfox::getService('blog.permission')->canEdit($aRow)) {
                return Phpfox_Error::display(_p('unable_to_edit_this_blog'));
            }

            if (Phpfox::isModule('tag')) {
                $aTags = Phpfox::getService('tag')->getTagsById('blog', $aRow['blog_id']);
                if (isset($aTags[$aRow['blog_id']])) {
                    $aRow['tag_list'] = '';
                    foreach ($aTags[$aRow['blog_id']] as $aTag) {
                        $aRow['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aRow['tag_list'] = trim(trim($aRow['tag_list'], ','));
                }
            }

            $aCategories = Phpfox::getService('blog.category')->getCategoriesByBlogId($aRow['blog_id']);
            $aSelectedCategories = array();

            if (!empty($aCategories)) {
                foreach ($aCategories as $aCategory) {
                    $aSelectedCategories[] = $aCategory['category_id'];
                }
            }
            $aRow['selected_categories'] = $aSelectedCategories;

            $bIsEdit = true;
            $this->setParam('aSelectedCategories', $aSelectedCategories);

            // Get Current Image
            if (!empty($aRow['image_path'])) {
                $aRow['current_image'] = Phpfox::getService('blog')->getImageUrl($aRow['image_path'],
                    $aRow['server_id'], self::IMG_SUFFIX);
            }

            $this->template()->assign(array(
                    'aForms' => $aRow
                )
            );
            (($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_process_edit')) ? eval($sPlugin) : false);
        } else {
            Phpfox::getUserParam('blog.add_new_blog', true);

            http_cache()->set();
        }

        if (!empty($sModule) && !empty($iItemId)) {
            $this->template()->assign([
                'sModule' => $sModule,
                'iItem' => $iItemId
            ]);
        }

        $aValidation = array(
            'title' => array(
                'def' => 'string:required',
                'title' => _p('fill_title_for_blog')
            ),
            'text' => array(
                'def' => 'string:required',
                'title' => _p('add_content_to_blog')
            )
        );

        if (Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_blog_add') && !$bIsPublish) {
            $aValidation['image_verification'] = _p('complete_captcha_challenge');
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_process_validation')) ? eval($sPlugin) : false);

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'core_js_blog_form',
                'aParams' => $aValidation
            )
        );

        $aCallback = null;
        if (!empty($sModule) && Phpfox::hasCallback($sModule, 'getItem')) {
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
            if ($aCallback === false) {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }
            $bCheckParentPrivacy = true;
            if (!$bIsEdit && Phpfox::hasCallback($sModule, 'checkPermission')) {
                $bCheckParentPrivacy = Phpfox::callback($sModule . '.checkPermission', $iItemId, 'blog.share_blogs');
            }

            if (!$bCheckParentPrivacy) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }

            if ($bIsEdit && !empty($aRow)) {
                $sUrl = $this->url()->makeUrl('blog', array('add', 'id' => $iEditId));
                $sCrumb = _p('editing_blog') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'],
                        Phpfox::getService('core')->getEditTitleSize(), '...');
            } else {
                $sUrl = $this->url()->makeUrl('blog',
                    array('add', 'module' => $aCallback['module'], 'item' => $iItemId));
                $sCrumb = _p('adding_a_new_blog');
            }

            $this->template()
                ->setBreadCrumb(isset($aCallback['module_title']) ? $aCallback['module_title'] : _p($sModule),
                    $this->url()->makeUrl($sModule))
                ->setBreadCrumb($aCallback['title'], Phpfox::permalink($sModule, $iItemId))
                ->setBreadCrumb(_p('blogs'), $this->url()->makeUrl($sModule, array($iItemId, 'blog')))
                ->setBreadCrumb($sCrumb, $sUrl, true);
        } else {
            if (!empty($sModule) && !empty($iItemId) && $sModule != 'blog' && $aCallback === null) {
                return Phpfox_Error::display(_p('Cannot find the parent item.'));
            }

            $this->template()
                ->setBreadCrumb(_p('blogs'), $this->url()->makeUrl('blog'))
                ->setBreadCrumb(((!empty($iEditId) && !empty($aRow)) ? _p('editing_blog') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'],
                        Phpfox::getService('core')->getEditTitleSize(), '...') : _p('adding_a_new_blog')),
                    ($iEditId > 0 ? $this->url()->makeUrl('blog',
                        array('add', 'id' => $iEditId)) : $this->url()->makeUrl('blog', array('add'))), true);

        }

        // Process Add or Update Blog
        if (($aVals = $this->request()->getArray('val')) || $bIsPublish) {
            // Check if we're publishing a blog entry
            if ($bIsPublish) {
                $aVals = !empty($aRow) ? $aRow : [];
                $aVals['draft_publish'] = true;
                $aVals['text'] = htmlspecialchars_decode($aVals['text']);
            }

            if ($oValid->isValid($aVals)) {
                $this->setParam('aSelectedCategories', (!empty($aVals['selected_categories']) ? $aVals['selected_categories'] : []));

                // Add the new blog
                if (isset($aVals['publish']) || isset($aVals['draft'])) {
                    if (isset($aVals['draft'])) {
                        $aVals['post_status'] = BLOG_STATUS_DRAFT;
                        $sMessage = _p('blog_successfully_saved');
                    } else {
                        $aVals['post_status'] = BLOG_STATUS_PUBLIC;
                        $sMessage = _p('your_blog_has_been_added');
                    }

                    if (($iFlood = Phpfox::getUserParam('blog.flood_control_blog')) !== 0) {
                        $aFlood = array(
                            'action' => 'last_post', // The SPAM action
                            'params' => array(
                                'field' => 'time_stamp', // The time stamp field
                                'table' => Phpfox::getT('blog'), // Database table we plan to check
                                'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                                'time_stamp' => $iFlood * 60 // Seconds);
                            )
                        );

                        // actually check if flooding
                        if (Phpfox::getLib('spam')->check($aFlood)) {
                            Phpfox_Error::set(_p('your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                        }
                    }

                    if (Phpfox_Error::isPassed()) {
                        $iId = Phpfox::getService('blog.process')->add($aVals);
                    }
                }

                // Update a blog
                if ((isset($aVals['update']) || isset($aVals['draft_update']) || isset($aVals['draft_publish'])) && isset($aRow['blog_id']) && $bIsEdit) {
                    if (isset($aVals['draft_publish'])) {
                        $aVals['post_status'] = BLOG_STATUS_PUBLIC;
                    } else {
                        $aVals['post_status'] = $aRow['post_status'];
                    }

                    // Update the blog
                    if (Phpfox_Error::isPassed()) {
                        $iId = Phpfox::getService('blog.process')->update($aRow['blog_id'], $aRow['user_id'], $aVals,
                            $aRow);
                        $sMessage = _p('the_blog_has_been_successfully_updated');
                    }
                }

                if (isset($iId) && $iId) {
                    Phpfox::permalink('blog', $iId, $aVals['title'], true, (empty($sMessage) ? '' : $sMessage));
                }
            }
        }

        $this->template()
            ->setTitle((!empty($iEditId) && !empty($aRow)) ? _p('editing_blog') . ': ' . $aRow['title'] : _p('adding_a_new_blog'))
            ->assign(array(
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'bIsEdit' => $bIsEdit,
                    'iMaxFileSize' => $iMaxFileSize,
                    'sPhotoField' => self::PHOTO_FIELD,
                    'bCanCustomPrivacy' => (empty($sModule) ? true : !Phpfox::hasCallback($sModule, 'inheritPrivacy'))
                )
            )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                )
            );

        if (!empty($iEditId) && !empty($aRow)) {
            $this->template()->buildPageMenu('js_blogs_block', [], [
                'link' => Phpfox::permalink('blog', $iEditId, $aRow['title']),
                'phrase' => _p('view_blog')
            ]);
        }

        if (Phpfox::isModule('attachment') && Phpfox::getUserParam('attachment.can_attach_on_blog')) {
            $this->setParam('attachment_share', array(
                    'type' => 'blog',
                    'id' => 'core_js_blog_form',
                    'edit_id' => ($bIsEdit ? $iEditId : 0)
                )
            );
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_process')) ? eval($sPlugin) : false);
        return true;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}

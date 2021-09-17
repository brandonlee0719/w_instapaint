<?php

namespace Apps\Core_Photos\Ajax;

use Core_Service_Process;
use Phpfox;
use Phpfox_Ajax;
use Phpfox_File;
use Phpfox_Image;
use Phpfox_Module;
use Phpfox_Plugin;
use Phpfox_Template;

class Ajax extends Phpfox_Ajax
{
    /**
     * Displays the form that adds a new photo album.
     *
     */
    public function newAlbum()
    {
        $this->setTitle(_p('create_a_new_photo_album'));
        // Only users can view this form.
        Phpfox::isUser(true);
        // Only users with this specific user group perm. can view this form.
        Phpfox::getUserParam('photo.can_create_photo_album', true);
        // Display the block form
        Phpfox::getBlock('photo.album');

        $this->call('<script type="text/javascript">$Core.loadInit();</script>');
    }

    /**
     * Add a new album into the database
     *
     * @return boolean Return false only to exit the call earlier.
     */
    public function addAlbum()
    {
        // Only users can view this form.
        Phpfox::isUser(true);
        // Only users with this specific user group perm. can view this form.
        Phpfox::getUserParam('photo.can_create_photo_album', true);
        // Get the total number of albums this user has
        $iTotalAlbums = Phpfox::getService('photo.album')->getAlbumCount(Phpfox::getUserId());
        // Check if they are allowed to create new albums
        $bAllowedAlbums = (Phpfox::getUserParam('photo.max_number_of_albums') == '' ? true : (!Phpfox::getUserParam('photo.max_number_of_albums') ? false : (Phpfox::getUserParam('photo.max_number_of_albums') <= $iTotalAlbums ? false : true)));

        // Are they allowed to create new albums?
        if (!$bAllowedAlbums) {
            // They have reached their limit
            $this->alert(_p('you_have_reached_your_limit_you_are_currently_unable_to_create_new_photo_albums'));

            return false;
        }

        // Assigned the post vals
        $aVals = $this->get('val');

        // Add the photo album
        if ($iId = Phpfox::getService('photo.album.process')->add($aVals)) {
            // All went well, add the new album to our form and close the AJAX popup.
            $this->show('#js_photo_albums')
                ->show('#js_photo_album_select_label')
                ->remove('#js_photo_albums_span')
                ->slideUp('#js_photo_privacy_holder')
                ->call('tb_remove();')
                ->append('#js_photo_album_select',
                    '<option value="' . $iId . '" selected="selected">' . Phpfox::getLib('parse.output')->clean(Phpfox::getLib('parse.input')->clean($aVals['name'])) . '</option>');
            //Update new album value
            $this->call('$Core.Photo.updateAddNewAlbum(' . $iId . ')');
        }
    }

    /**
     * Displays the photo index page using the pagination.
     * @deprecated from 4.6.0
     *
     */
    public function browse()
    {
        if (!defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            define('PHPFOX_IS_AJAX_CONTROLLER', true);
        }
        Phpfox_Module::instance()->getComponent('photo.index', $this->getAll(), 'controller');
        $this->call('$(".pager_container, .moderation_holder").remove();');
        $this->call('$(\'#js_ajax_browse_content\').append(\'' . $this->getContent() . '\'); ');
        $this->call('$Core.loadInit();');
    }

    /**
     * Browse a users album
     * @deprecated from 4.6.0
     */
    public function browseUserAlbum()
    {
        Phpfox_Module::instance()->getComponent('photo.profile', $this->getAll(), 'controller');

        $this->call('$(\'#js_user_photo_albums\').html(\'' . $this->getContent() . '\'); $.scrollTo(\'#js_user_photo_albums_outer\', 340);');
    }

    /**
     * Browse a users album
     * @deprecated from 4.6.0
     */
    public function browseAlbum()
    {
        Phpfox_Module::instance()->getComponent('photo.album', $this->getAll(), 'controller');

        $this->call('$(\'#js_album_content\').html(\'' . $this->getContent() . '\'); $.scrollTo(\'#js_album_content\', 340);');
    }

    /**
     * Browser a set of photos by a user
     * @deprecated from 4.6.0
     */
    public function browseUserPhotos()
    {
        Phpfox_Module::instance()->getComponent('photo.profile', $this->getAll(), 'controller');

        $this->call('$(\'#js_user_photos\').html(\'' . $this->getContent() . '\'); $.scrollTo(\'#js_user_photos_outer\', 340); $Behavior.hoverAction(); $Behavior.imageHoverHolder();');
    }

    /**
     * Refresh the featured image and reset the refresh time.
     *
     */
    public function refreshFeaturedImage()
    {
        Phpfox::getBlock('photo.featured');
        $this->html('#js_block_content_featured_photo', $this->getContent(false));
    }

    /**
     *
     */
    public function updateAlbum()
    {
        Phpfox::isUser(true);

        $aVals = $this->get('val');

        if (Phpfox::getService('user.auth')->hasAccess('photo_album', 'album_id', $aVals['album_id'],
                'photo.can_edit_own_photo_album',
                'photo.can_edit_other_photo_albums') && Phpfox::getService('photo.album.process')->update($aVals['album_id'],
                $aVals)
        ) {
            $oParseInput = Phpfox::getLib('parse.input');
            $oParseOutput = Phpfox::getLib('parse.output');

            if (isset($aVals['inline'])) {
                $sTitle = $oParseOutput->clean($oParseInput->clean($aVals['name']));

                $this->hide('#js_album_edit_form')
                    ->call('$(\'#js_album_inner_title_link_' . $aVals['album_id'] . '\').attr(\'title\', \'' . $sTitle . '\');')
                    ->html('#js_album_inner_title_' . $aVals['album_id'], $sTitle)
                    ->show('#js_user_photo_albums')
                    ->html('#js_updating_album',
                        ' - <a href="#" onclick="$(\'#js_album_edit_form\').hide(); $(\'#js_user_photo_albums\').show(); return false;">' . _p('cancel') . '</a>');
            } else {
                $this->html('#js_ge_edit_inner_title' . $aVals['album_id'],
                    $oParseOutput->clean($oParseInput->clean($aVals['name'])))
                    ->html('#js_album_description', $oParseOutput->clean($oParseInput->clean($aVals['description'])))
                    ->html('#js_updating_album', ' - <a href="#" id="js_album_cancel_edit">' . _p('cancel') . '</a>');
            }
        }
    }

    /**
     *
     */
    public function updatePhoto()
    {
        $aPostVals = $this->get('val');
        $aVals = $aPostVals[$this->get('photo_id')];
        $aVals['set_album_cover'] = (isset($aPostVals['set_album_cover']) ? $aPostVals['set_album_cover'] : null);
        if (!isset($aVals['privacy']) && isset($aPostVals['privacy'])) {
            $aVals['privacy'] = $aPostVals['privacy'];
        } else {
            $aVals['privacy'] = (isset($aVals['privacy']) ? $aVals['privacy'] : 0);
        }
        $aVals['privacy_comment'] = 0;
        if (($iUserId = Phpfox::getService('user.auth')->hasAccess('photo', 'photo_id', $aVals['photo_id'],
                'photo.can_edit_own_photo',
                'photo.can_edit_other_photo')) && Phpfox::getService('photo.process')->update($iUserId,
                $aVals['photo_id'], $aVals)
        ) {
            $aPhoto = Phpfox::getService('photo')->getForEdit($aVals['photo_id']);

            if ($this->get('inline')) {
                $this->html('#js_photo_title_' . $this->get('photo_id'),
                    Phpfox::getLib('parse.output')->clean(Phpfox::getLib('parse.input')->clean($aVals['title'])));
                $this->call('tb_remove();');
            } else {
                Phpfox::addMessage(_p('photo_successfully_updated'));
                $this->call('window.location.href = "' . Phpfox::getLib('url')->permalink('photo', $aPhoto['photo_id'],
                        Phpfox::getLib('parse.input')->clean($aVals['title'])) . '";');
            }
        }
    }

    /**
     * Set an album cover
     *
     */
    public function setAlbumCover()
    {
        if (Phpfox::getService('user.auth')->hasAccess('photo_album', 'album_id', $this->get('album_id'),
                'photo.can_edit_own_photo_album',
                'photo.can_edit_other_photo_albums') && Phpfox::getService('photo.album.process')->setCover($this->get('album_id'),
                $this->get('photo_id'))
        ) {

        }
    }

    /**
     * After uploading a photo we give an option that allows users the ability
     * to delete their photos on the spot. This method does that job for us.
     *
     */
    public function deleteNewPhoto()
    {
        // Only users can view this form.
        Phpfox::isUser(true);

        // Delete the photo.
        if (Phpfox::getService('photo.process')->delete($this->get('id'))) {

        }
    }

    public function deleteTheaterPhoto()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('photo.process')->delete($this->get('photo_id'))) {
            $this->call("js_box_remove($('.js_box_image_holder_full').find('.js_box_content:first'));");
            $this->call("$('.js_photo_item_" . $this->get('photo_id') . "').parents('.js_parent_feed_entry:first').remove();");
            $this->call("$('#js_photo_id_" . $this->get('photo_id') . "').remove();");
        }
    }
    public function deletePhoto()
    {
        $iId = $this->get('id');
        $bIsDetail = $this->get('is_detail', 0);
        $aPhoto = Phpfox::getService('photo')->getPhotoItem($iId);

        if (!$aPhoto) {
            $this->alert(_p('sorry_the_photo_you_are_looking_for_no_longer_exists'));
            return false;
        }
        else {
            if (Phpfox::getService('photo.process')->delete($iId)) {
                Phpfox::addMessage(_p('photo_successfully_deleted'));
            }
            else {
                $this->alert(_p('you_do_not_have_sufficient_permission_to_delete_this_photo'));
                return false;
            }
        }
        if (!$bIsDetail) {
            $this->call('window.location.reload();');
        } else {
            $sUrl = Phpfox::getLib('url')->makeUrl('photo');
            if ($aPhoto['module_id'] != '' && $aPhoto['group_id'] != 0) {
                if (Phpfox::hasCallback($aPhoto['module_id'],
                        'getPhotoDetails') && $aCallback = Phpfox::callback($aPhoto['module_id'] . '.getPhotoDetails',
                        $aPhoto)
                ) {
                    $sUrl = $aCallback['url_home_photo'];
                }
            }
            $this->call('window.location.href = "' . $sUrl . '";');
        }
    }

    public function deleteAlbumPhoto()
    {
        $iId = $this->get('id');
        $bIsDetail = $this->get('is_detail', 0);
        if ($sParentReturn = Phpfox::getService('photo.album.process')->delete($iId)) {
            Phpfox::addMessage(_p('photo_album_successfully_deleted'));
        }
        else {
            $this->alert(_p('you_do_not_have_sufficient_permission_to_delete_this_photo_album'));
            return false;
        }

        if (!$bIsDetail) {
            $this->call('window.location.reload();');
        } else {
            if (is_bool($sParentReturn)) {
                $sUrl = Phpfox::getLib('url')->makeUrl('photo.albums');
            } else {
                $sUrl = $sParentReturn;
            }
            $this->call('window.location.href = "' . $sUrl . '";');
        }
    }

    /**
     *
     */
    public function editPhoto()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('user.auth')->hasAccess('photo', 'photo_id', $this->get('photo_id'),
            'photo.can_edit_own_photo', 'photo.can_edit_other_photo')
        ) {
            Phpfox::getBlock('photo.edit-photo', array('ajax_photo_id' => $this->get('photo_id')));
            $this->setTitle(_p('editing_photo'));
            $this->call('<script type="text/javascript">$Core.loadInit();</script>');
        }
    }

    public function warning()
    {
        Phpfox::getBlock('photo.warning');
    }

    public function getCategoryForEdit()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('photo.can_edit_photo_categories', true);

        $aCategory = Phpfox::getService('photo.category')->getCategory($this->get('id'));

        $this->call('$(\'#js_photo_category_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);');

        $this->html('#js_photo_table_header', _p('editing_category') . ': ' . $aCategory['name'])
            ->html('#js_photo_hidden',
                '<input type="hidden" name="val[edit_id]" value="' . $aCategory['category_id'] . '" />')
            ->html('#js_photo_extra_button',
                ' <input type="button" name="" value="' . _p('cancel') . '" class="button" onclick="$(\'#js_photo_category_' . $aCategory['parent_id'] . '\').attr(\'selected\', false); $(\'#js_category_holder\').show(); $(\'#js_photo_table_header\').html(\'' . _p('add_a_photo_category') . '\'); $(\'#js_photo_extra_button\').html(\'\'); $(\'#js_photo_hidden\').html(\'\'); $(\'#name\').val(\'\');" /> <input type="submit" value="' . _p('delete') . '" onclick="return confirm(\'' . _p('are_you_sure') . '\');" class="button" name="val[delete]" />')
            ->val('#name', $aCategory['name']);

        if (strpos($aCategory['name'], '&#') !== false) {
            $this->call("$('#name').val($('<div />').html($('#name').val()).text());");
        }
    }

    /**
     *
     */
    public function approve()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('photo.can_approve_photos', true);

        if (Phpfox::getService('photo.process')->approve($this->get('id'))) {
            $this->alert(_p('photo_has_been_approved'), _p('photo_approved'), 300, 100, true);
            $this->hide('#js_item_bar_approve_image');
            $this->hide('.js_moderation_off');
            $this->show('.js_moderation_on');
            if ($this->get('inline')) {
                $sUrl = Phpfox::getLib('url')->makeUrl('photo');
                $this->call('if(!$(\'#js_approve_photo_message\').length) {$("#js_photo_id_' . $this->get('id') . '").remove(); var total_pending = parseInt($("#photo_pending").html()) - 1; if(total_pending > 0) $("#photo_pending").html(total_pending); else window.location.href = "' . $sUrl . '";}');
            }
            else{
                $this->call('window.location.reload();');
            }
        }
    }

    public function getNew()
    {
        Phpfox::getBlock('photo.new');
        $this->html('#' . $this->get('id'), $this->getContent(false));
        $this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox::getLib('url')->makeUrl('photo') . '\');');
    }

    /**
     *
     */
    public function feature()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('photo.can_feature_photo', true);

        if (Phpfox::getService('photo.process')->feature($this->get('photo_id'), $this->get('type'))) {
            if ($this->get('type') == '1') {
                $sHtml = '<a href="#" title="' . _p('un_feature_this_photo') . '" onclick="$.ajaxCall(\'photo.feature\', \'photo_id=' . $this->get('photo_id') . '&amp;type=0\'); return false;"><i class="ico ico-diamond-o mr-1"></i>' . _p('un_feature') . '</a>';
            } else {
                $sHtml = '<a href="#" title="' . _p('feature_this_photo') . '" onclick="$.ajaxCall(\'photo.feature\', \'photo_id=' . $this->get('photo_id') . '&amp;type=1\'); return false;"><i class="ico ico-diamond mr-1"></i>' . _p('feature') . '</a>';
            }

            $this->html('#js_photo_feature_' . $this->get('photo_id'), $sHtml);
            $this->alert(($this->get('type') == '1' ? _p('photo_successfully_featured') : _p('photo_successfully_un_featured')),
                null, 300, 150, true);
            if ($this->get('type') == '1') {
                $this->addClass('#js_photo_id_' . $this->get('photo_id'), 'row_featured_image');
                $this->call('$(\'#js_photo_id_' . $this->get('photo_id') . '\').find(\'.js_featured_photo:first\').show();');
            } else {
                $this->removeClass('#js_photo_id_' . $this->get('photo_id'), 'row_featured_image');
                $this->call('$(\'#js_photo_id_' . $this->get('photo_id') . '\').find(\'.js_featured_photo:first\').hide();');
            }
        }
    }

    public function sponsor()
    {
        if (!Phpfox::isModule('ad')) {
            return $this->alert('your_request_is_invalid');
        }
        $iPhotoId = $this->get('photo_id');
        // 0 = remove sponsor; 1 = add sponsor
        if (Phpfox::getService('photo.process')->sponsor($iPhotoId, $this->get('type'))) {
            $aPhoto = Phpfox::getService('photo')->getForEdit($iPhotoId);
            if ($this->get('type') == '1') {
                $sModule = _p('photo');
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'photo',
                    'item_id' => $this->get('photo_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => $sModule, 'name' => $aPhoto['title']])
                ));
                // image was sponsored
                $sHtml = '<a href="#" title="' . _p('unsponsor_this_photo') . '" onclick="$.ajaxCall(\'photo.sponsor\', \'photo_id=' . $this->get('photo_id') . '&amp;type=0\'); return false;">' . _p('unsponsor_this_photo') . '</a>';
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('photo', $this->get('photo_id'));
                $sHtml = '<a href="#" title="' . _p('unsponsor_this_photo') . '" onclick="$.ajaxCall(\'photo.sponsor\', \'photo_id=' . $this->get('photo_id') . '&amp;type=1\'); return false;">' . _p('sponsor_this_photo') . '</a>';
            }
            $this->html('#js_photo_sponsor_' . $this->get('photo_id'), $sHtml);
            $this->alert($this->get('type') == '1' ? _p('photo_successfully_sponsored') : _p('photo_successfully_un_sponsored'),
                null, 300, 150, true);
            if ($this->get('type') == '1') {
                $this->addClass('#js_photo_id_' . $this->get('photo_id'), 'row_sponsored_image');
                $this->call('$(\'#js_photo_id_' . $this->get('photo_id') . '\').find(\'.js_sponsor_photo:first\').show();');
            } else {
                $this->removeClass('#js_photo_id_' . $this->get('photo_id'), 'row_sponsored_image');
                $this->call('$(\'#js_photo_id_' . $this->get('photo_id') . '\').find(\'.js_sponsor_photo:first\').hide();');
            }
        }
    }

    /**
     *
     */
    public function rotate()
    {
        Phpfox::isUser(true);
        if ($aPhoto = Phpfox::getService('photo.process')->rotate($this->get('photo_id'), $this->get('photo_cmd'))) {
            Phpfox::getService('photo.tag.process')->deleteAll($this->get('photo_id'));
            $this->call('window.location.href = \'' . Phpfox::getLib('url')->permalink('photo', $aPhoto['photo_id'],
                    $aPhoto['title']) . 'refresh_1/' . '\';');
        }
    }

    /**
     *
     */
    public function addPhotoTag()
    {
        $aVals = $this->get('val');

        $this->val('#js_tag_user_id', '0')->val('#NoteNote', '');
        if (($sReturn = Phpfox::getService('photo.tag.process')->add($aVals['tag']))) {
            $this->append('#js_photo_in_this_photo', ', ' . $sReturn)->call('$(\'#js_photo_in_this_photo\').parent().show();');
            $this->call('$(\'#js_photo_in_this_photo\').html(ltrim($(\'#js_photo_in_this_photo\').html(), \', \'));');
            $this->call(';window.oPhotoTagParams={' . Phpfox::getService('photo.tag')->getJs($aVals['tag']['item_id']) . '};');
            $this->call(';$Behavior.tagPhoto();');
        }
        if(!\Phpfox_Error::isPassed()){
            $error  =  \Phpfox_Error::get();
            \Phpfox_Error::reset();
            $this->call(';$Core.photo_tag.error("'.$error[0].'")');
            $this->call(';$Core.photo_tag.init({' . Phpfox::getService('photo.tag')->getJs($aVals['tag']['item_id']) . '});');
        }
    }

    /**
     *
     */
    public function removePhotoTag()
    {
        if ($iPhoto = Phpfox::getService('photo.tag.process')->delete($this->get('tag_id'))) {
            $this->call('$(\'.note\').remove(); $Core.photo_tag.init({' . Phpfox::getService('photo.tag')->getJs($iPhoto) . '});');
        }
        $error  =  \Phpfox_Error::get();
        \Phpfox_Error::reset();
    }

    public function process()
    {
        $aPostPhotos = $this->get('photos');
        $iTimeStamp = $this->get('timestamp', 0);
        $aVals = $this->get('val');

        if (is_array($aPostPhotos)) {
            $aImages = array();
            foreach ($aPostPhotos as $aPostPhoto) {
                $aPart = json_decode(urldecode($aPostPhoto), true);
                $aImages[] = $aPart[0];
            }
        } else {
            $aImages = json_decode(urldecode($aPostPhotos), true);
        }

        $oImage = Phpfox_Image::instance();
        $aPhoto = [];
        $aImage = [];

        foreach ($aImages as $iKey => $aImage) {
            $aImage['destination'] = urldecode($aImage['destination']);
            if ($aImage['completed'] == 'false') {
                $aPhoto = Phpfox::getService('photo')->getForProcess($aImage['photo_id'], $this->get('user_id', 0));
                if (isset($aPhoto['photo_id'])) {
                    if (Phpfox::getParam('core.allow_cdn')) {
                        Phpfox::getLib('cdn')->setServerId($aPhoto['server_id']);
                    }

                    $sFileName = $aPhoto['destination'];
                    $sFile = Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '');
                    if (!file_exists(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''))
                        && Phpfox::getParam('core.allow_cdn')
                        && !Phpfox::getParam('core.keep_files_in_server')
                    ) {
                        if (Phpfox::getParam('core.allow_cdn') && $aPhoto['server_id'] > 0) {
                            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                                    'server_id' => $aPhoto['server_id'],
                                    'path' => 'photo.url_photo',
                                    'file' => $aPhoto['destination'],
                                    'suffix' => '',
                                    'return_url' => true
                                )
                            );

                            $aExts = preg_split("/[\/\\.]/", $sActualFile);
                            $iCnt = count($aExts) - 1;
                            $sExt = strtolower($aExts[$iCnt]);

                            $aParts = explode('/', $aPhoto['destination']);
                            $sFile = Phpfox::getParam('photo.dir_photo') . $aParts[0] . '/' . $aParts[1] . '/' . md5($aPhoto['destination']) . '.' . $sExt;

                            // Create a temp copy of the original file in local server
                            if (filter_var($sActualFile, FILTER_VALIDATE_URL) !== false) {
                                file_put_contents($sFile, fox_get_contents($sActualFile));
                            } else {
                                copy($sActualFile, $sFile);
                            }
                            //Delete file in local server
                            register_shutdown_function(function () use ($sFile) {
                                @unlink($sFile);
                            });
                        }
                    }
                    list($width, $height, ,) = getimagesize($sFile);
                    foreach (Phpfox::getService('photo')->getPhotoPicSizes() as $iSize) {
                        // Create the thumbnail
                        if ($oImage->createThumbnail($sFile,
                                Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize,
                                $height, true,
                                ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false)) === false
                        ) {
                            continue;
                        }

                        if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                            $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                        }

                        if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                            unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                        }
                    }
                    //Crop original image
                    $iWidth = (int)Phpfox::getUserParam('photo.maximum_image_width_keeps_in_server');
                    if ($iWidth < $width) {
                        $bIsCropped = $oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                ''), Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), $iWidth, $height,
                            true,
                            ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false));
                        if ($bIsCropped !== false) {
                            //Rename file
                            if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                                $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                            }
                            if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                                unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                            }
                        }
                    }
                    //End Crop
                    if (Phpfox::getParam('photo.enabled_watermark_on_photos')) {
                        $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                    }

                    $aImages[$iKey]['completed'] = 'true';

                    (($sPlugin = Phpfox_Plugin::get('photo.component_ajax_ajax_process__1')) ? eval($sPlugin) : false);

                    break;
                }
            }
        }

        $iNotCompleted = 0;
        foreach ($aImages as $iKey => $aImage) {
            if ($aImage['completed'] == 'false') {
                $iNotCompleted++;
            } else {
                $aPhoto = Phpfox::getService('photo')->getForProcess($aImage['photo_id'], $this->get('user_id', 0));
            }
        }
        if ($iNotCompleted === 0) {
            $aCallback = ($this->get('callback_module') ? Phpfox::callback($this->get('callback_module') . '.addPhoto',
                $this->get('callback_item_id')) : null);
            $iFeedId = 0;
            $bNewFeed = false;
            if (!Phpfox::getUserParam('photo.photo_must_be_approved') && !$this->get('is_cover_photo') && !$this->get('no_feed')) {
                if (Phpfox::isModule('feed')) {
                    if ($iTimeStamp && !empty($_SESSION['upload_photo_' . $iTimeStamp . '_' . $aPhoto['album_id']])) {
                        $iFeedId = $_SESSION['upload_photo_' . $iTimeStamp . '_' . $aPhoto['album_id']];
                    } else {
                        $iFeedId = Phpfox::getService('feed.process')->callback($aCallback)->add('photo',
                            $aPhoto['photo_id'], $aPhoto['privacy'], $aPhoto['privacy_comment'],
                            (int)$this->get('parent_user_id', 0));
                        if ($aCallback && defined('PHPFOX_NEW_FEED_LOOP_ID') && PHPFOX_NEW_FEED_LOOP_ID) {
                            storage()->set('photo_parent_feed_' . PHPFOX_NEW_FEED_LOOP_ID, $iFeedId);
                        }

                        $bNewFeed = true;
                        if ($iTimeStamp) {
                            $_SESSION['upload_photo_' . $iTimeStamp . '_' . $aPhoto['album_id']] = $iFeedId;
                        }
                        if (isset($aVals['action']) && $aVals['action'] == 'upload_photo_via_share') {
                            Phpfox::getService('photo.process')->notifyTaggedInFeed($aVals['status_info'],
                                $aPhoto['photo_id'], $aPhoto['user_id']);
                        }
                        if ($aCallback && Phpfox::isModule('notification') && Phpfox::isModule($aCallback['module']) && Phpfox::hasCallback($aCallback['module'],
                                'addItemNotification')
                        ) {
                            Phpfox::callback($aCallback['module'] . '.addItemNotification', [
                                'page_id' => $aCallback['item_id'],
                                'item_perm' => 'photo.view_browse_photos',
                                'item_type' => 'photo',
                                'item_id' => $aPhoto['photo_id'],
                                'owner_id' => $aPhoto['user_id'],
                                'items_phrase' => _p('photos__l')
                            ]);
                        }
                    }

                }
                if (count($aImages)) {
                    foreach ($aImages as $aImage) {
                        if ($aImage['photo_id'] == $aPhoto['photo_id'] && $bNewFeed) {
                            continue;
                        }

                        db()->insert(Phpfox::getT('photo_feed'), array(
                                'feed_id' => $iFeedId,
                                'photo_id' => $aImage['photo_id'],
                                'feed_table' => (empty($aCallback['table_prefix']) ? 'feed' : $aCallback['table_prefix'] . 'feed')
                            )
                        );
                    }
                }
            }

            // this next if is the one you will have to bypass if they come from sharing a photo in the activity feed.
            if (($this->get('page_id') > 0)) {
                if ($this->get('is_cover_photo')) {
                    Phpfox::getService('pages.process')->updateCoverPhoto($aImage['photo_id'], $this->get('page_id'));
                }
                $this->call('window.location.href = "' . Phpfox::getLib('url')->permalink('pages',
                        $this->get('page_id'), '') . 'coverupdate_1";');
            } else {
                if (($this->get('groups_id') > 0)) {
                    if ($this->get('is_cover_photo')) {
                        Phpfox::getService('groups.process')->updateCoverPhoto($aImage['photo_id'], $this->get('groups_id'));
                    }
                    $this->call('window.location.href = "' . Phpfox::getLib('url')->permalink('groups',
                            $this->get('groups_id'), '') . 'coverupdate_1";');
                } else {
                    if ($this->get('action') == 'upload_photo_via_share') {
                        if ($this->get('is_cover_photo')) {
                            Phpfox::getService('user.process')->updateCoverPhoto($aImage['photo_id']);

                            $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('profile',
                                    array('coverupdate' => '1')) . '\';');
                        } else {
                            if ($aCallback && Phpfox::getLib('pages.facade')->getPageItemType($aCallback['item_id']) !== false && !defined('PHPFOX_IS_PAGES_VIEW')) {
                                define('PHPFOX_IS_PAGES_VIEW', true);
                            }
                            Phpfox::getService('feed')->callback($aCallback)->processAjax($iFeedId);

                            (($sPlugin = Phpfox_Plugin::get('photo.component_ajax_process_done')) ? eval($sPlugin) : false);
                        }
                    } else {
                        foreach ($aImages as $aImage) {
                            // use the JS var set at progress.js
                            $this->call('sImages += "&photos[]=' . $aImage['photo_id'] . '";');
                        }
                        if (Phpfox::getParam('photo.photo_upload_process', 0)) {
                            if ($aCallback !== null) {
                                $sModule = isset($aCallback['module']) ? $aCallback['module'] : 'pages';
                                $this->call('var sCurrentProgressLocation = \'' . Phpfox::getLib('url')->makeUrl($sModule . '.' . $aCallback['item_id'] . '.photo',
                                        ['view' => 'my', 'mode' => 'edit']) . '\';');
                            } else {
                                $this->call('var sCurrentProgressLocation = \'' . Phpfox::getLib('url')->makeUrl('photo',
                                        array('view' => 'my', 'mode' => 'edit')) . '\';');
                            }
                            $this->call('var edit_after_upload = true;');
                        } else {
                            $this->call('sImages = "";');
                            $this->call('var sCurrentProgressLocation = \'' . Phpfox::getLib('url')->permalink('photo',
                                    $aPhoto['photo_id'],
                                    $aPhoto['title']) . ($aPhoto['album_id'] ? 'albumid_' . $aPhoto['album_id'] : 'userid_' . Phpfox::getUserId()) . '/\';');
                        }
                        $this->call('hasUploaded++; if ((hasUploaded + hasErrors) == iTotalUploadedFiles) completeProgress();');
                    }
                }
            }
        } else {
            $this->call('$(\'#js_progress_cache_holder\').html(\'\' + $.ajaxProcess(\'' . _p('processing_image_current_total',
                    array(
                        'phpfox_squote' => true,
                        'current' => (count($aImages) - $iNotCompleted),
                        'total' => count($aImages)
                    )) . '\', \'large\') + \'\');');
            $this->html('#js_photo_upload_process_cnt', (count($aImages) - $iNotCompleted));

            $sExtra = '';
            if ($this->get('callback_module')) {
                $sExtra .= '&callback_module=' . $this->get('callback_module') . '&callback_item_id=' . $this->get('callback_item_id') . '';
            }
            if ($this->get('parent_user_id')) {
                $sExtra .= '&parent_user_id=' . $this->get('parent_user_id');
            }

            if ($this->get('start_year') && $this->get('start_month') && $this->get('start_day')) {
                $sExtra .= '&start_year= ' . $this->get('start_year') . '&start_month= ' . $this->get('start_month') . '&start_day= ' . $this->get('start_day') . '';
            }

            if ($this->get('custom_pages_post_as_page')) {
                $sExtra .= '&custom_pages_post_as_page= ' . $this->get('custom_pages_post_as_page');
            }
            if (isset($aVals['action']) && $aVals['action'] == 'upload_photo_via_share') {
                $sExtra .= '&val[action]=' . $aVals['action'] . '&val[status_info]=' . $aVals['status_info'];
            }
            $sExtra .= '&is_cover_photo=' . $this->get('is_cover_photo');
            $this->call('$.ajaxCall(\'photo.process\', \'&action=' . $this->get('action') . '&js_disable_ajax_restart=true&photos=' . json_encode($aImages) . $sExtra . '\');');
        }

        $aVals = $this->get('core');

        if (isset($aVals['profile_user_id']) && !empty($aVals['profile_user_id']) && $aVals['profile_user_id'] != Phpfox::getUserId() && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('photo_feed_profile', $aPhoto['photo_id'],
                $aVals['profile_user_id']);
        }
    }

    public function view()
    {
        Phpfox::getComponent('photo.view', array(), 'controller');
        $aHeaderFiles = Phpfox_Template::instance()->getHeader(true);

        $aPhrases = Phpfox_Template::instance()->getPhrases();

        $sLoadFiles = '';
        foreach ($aHeaderFiles as $sHeaderFile) {
            if (preg_match('/<style(.*)>(.*)<\/style>/i', $sHeaderFile)) {
                continue;
            }

            $sHeaderFile = strip_tags($sHeaderFile);

            $sNew = preg_replace('/\s+/', '', $sHeaderFile);
            if (empty($sNew)) {
                continue;
            }

            if (substr($sNew, 0, 13) == 'oTranslations') {
                continue;
            }

            if (strpos($sHeaderFile, 'custom.css') !== false) {
                continue;
            }

            $sLoadFiles .= '\'' . str_replace("'", "\'", $sHeaderFile) . '\',';
        }
        $sLoadFiles = rtrim($sLoadFiles, ',');

        $sContent = $this->getContent(false);

        if (count($aPhrases) && is_array($aPhrases)) {
            $sPhrases = '<script type="text/javascript">';
            foreach ($aPhrases as $sKey => $sValue) {
                $sPhrases .= 'oTranslations[\'' . $sKey . '\'] = \'' . str_replace("'", "\'", $sValue) . '\';';
            }
            $sPhrases .= '</script>';

            echo $sPhrases;
        }

        echo '<script type="text/javascript">$Core.loadStaticFiles([' . $sLoadFiles . ']);</script>';
        echo $sContent;
        echo '<script type="text/javascript">$Core.loadInit();</script>';
    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'edit':
                $this->call('var sImages = \'\';');
                foreach ((array)$this->get('item_moderate') as $iId) {
                    $this->call('sImages += "&photos[]=' . $iId . '";');
                }
                $sMessage = '';
                $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('photo',
                        array('view' => 'my', 'mode' => 'edit', 'massedit' => 1)) . '\' + sImages;');
                break;
            case 'approve':
                Phpfox::getUserParam('photo.can_approve_photos', true);
                $iTimeStamp = PHPFOX_TIME;
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('photo.process')->approve($iId, $iTimeStamp);
                }
                $sMessage = _p('photo_s_successfully_approved');
                break;
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    if (!Phpfox::getService('photo')->isAdminOfParentItem($iId)) {
                        Phpfox::getUserParam('photo.can_delete_other_photos', true);
                    }
                    Phpfox::getService('photo.process')->delete($iId);
                    $this->call('$("#js_photo_id_' . $iId . '").remove();');
                }
                $sMessage = _p('photo_s_successfully_deleted');
                break;
            case 'feature':
                Phpfox::getUserParam('photo.can_feature_photo', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('photo.process')->feature($iId, 1);
                }
                $sMessage = _p('photo_s_successfully_featured');
                break;
            case 'un-feature':
                Phpfox::getUserParam('photo.can_feature_photo', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('photo.process')->feature($iId, 0);
                }
                $sMessage = _p('photo_s_successfully_unfeatured');
                break;
            default:
                break;
        }
        if (!empty($sMessage)) {
            Phpfox::addMessage($sMessage);
            $this->call('window.location.reload();');
        }
        $this->updateCount();
        $this->hide('.moderation_process');
    }

    public function albumModeration()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    if (!Phpfox::getService('photo.album')->isAdminOfParentItem($iId)) {
                        Phpfox::getUserParam('photo.can_delete_other_photo_albums', true);
                    }
                    Phpfox::getService('photo.album.process')->delete($iId);
                    $this->remove('#js_album_id_' . $iId);
                }
                $sMessage = _p('albums_s_successfully_deleted');
                $this->alert($sMessage, _p('moderation'), 300, 150, true);
                break;
        }
        $this->updateCount();
        $this->call('window.location.reload();');
    }

    public function massUpdate()
    {
        $aVals = $this->get('val');
        $aPhoto = [];
        $aRedirectPhoto = [];
        foreach ($aVals as $iPhotoId => $aVal) {
            $aPhoto = db()->select('photo_id, album_id, title, user_id')
                ->from(Phpfox::getT('photo'))
                ->where('photo_id = ' . (int)$iPhotoId)
                ->execute('getSlaveRow');

            if (isset($aPhoto['photo_id'])) {
                if ($aPhoto['user_id'] != Phpfox::getUserId()) {
                    continue;
                }

                if (!empty($aPhoto['album_id'])) {
                    $aVal['album_id'] = $aPhoto['album_id'];
                }

                if (isset($aVal['delete_photo'])) {
                    Phpfox::getService('photo.process')->delete($aPhoto['photo_id']);
                    $this->slideUp('#photo_edit_item_id_' . $aPhoto['photo_id']);
                } else {
                    $aRedirectPhoto[] = $aPhoto;
                    Phpfox::getService('photo.process')->update($aPhoto['user_id'], $aPhoto['photo_id'], $aVal);
                }
            }
        }
        $aPhoto = end($aRedirectPhoto);

        if (!$aPhoto) {
            $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('photo',
                    ['view' => 'my']) . '\';');
        }
        if ($this->get('is_photo_upload')) {
            if ($this->get('mass_edit')) {
                $this->call('window.location.href = \'' . Phpfox::getLib('url')->permalink('photo', $aPhoto['photo_id'],
                        $aPhoto['title']) . ($aPhoto['album_id'] ? 'albumid_' . $aPhoto['album_id'] : 'userid_' . Phpfox::getUserId()) . '/myphotos_1/\';');
            } else {
                $this->call('window.location.href = \'' . Phpfox::getLib('url')->permalink('photo', $aPhoto['photo_id'],
                        $aPhoto['title']) . ($aPhoto['album_id'] ? 'albumid_' . $aPhoto['album_id'] : 'userid_' . Phpfox::getUserId()) . '/\';');
            }
        } else {
            $this->alert(_p('successfully_updated_photo_s'), _p('notice'), 300, 150, true);
            $this->hide('#js_photo_multi_edit_image');
            $this->show('#js_photo_multi_edit_submit');
        }
    }

    public function getForAttachment()
    {
        Phpfox::isUser(true);

        Phpfox::getBlock('photo.attachment');

        $this->hide('#' . $this->get('div-id') . ' .js_upload_form_holder_global:first');
        if ($this->get('page') > 1) {
            $this->remove('#' . $this->get('div-id') . ' .js_upload_form_holder_global_temp:first .js_pager_view_more_link');
            $this->append('#' . $this->get('div-id') . ' .js_upload_form_holder_global_temp:first',
                $this->getContent(false));
        } else {
            $this->html('#' . $this->get('div-id') . ' .js_upload_form_holder_global_temp:first',
                $this->getContent(false), '.show()');
            $this->call('$(\'#' . $this->get('div-id') . '\').parents(\'.js_upload_attachment_parent_holder:first .js_global_attachment_loader:first\').hide();');
        }
    }

    /**
     *
     */
    public function attachToItem()
    {
        Phpfox::isUser(true);

        $iFileSizes = 0;

        $oAttachment = Phpfox::getService('attachment.process');
        $oFile = Phpfox_File::instance();
        $oImage = Phpfox_Image::instance();

        $aPhoto = Phpfox::getService('photo')->getPhotoItem($this->get('photo-id'));

        if (!isset($aPhoto['photo_id'])) {
            $this->alert(_p('unable_to_find_the_photo_you_are_looking_for'));

            return;
        }

        if ($aPhoto['user_id'] != Phpfox::getUserId()) {
            $this->alert(_p('unable_to_import_this_photo'));

            return;
        }

        $iId = $oAttachment->add(array(
                'category' => $this->get('category'),
                'file_name' => $aPhoto['file_name'],
                'extension' => $aPhoto['extension'],
                'is_image' => true
            )
        );

        $sFileName = md5($iId . PHPFOX_TIME . uniqid()) . '%s.' . $aPhoto['extension'];
        $sFileToCopy = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['original_destination'], '');
        if (!file_exists($sFileToCopy)) {
            $sFileToCopy = Phpfox::getParam('photo.dir_photo') . sprintf($aPhoto['original_destination'], '_500');
        }
        $oFile->copy($sFileToCopy, Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''));

        $sFileSize = $aPhoto['file_size'];
        $iFileSizes += $sFileSize;

        $oAttachment->update(array(
            'file_size' => $sFileSize,
            'destination' => $sFileName,
            'server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ), $iId);

        $sThumbnail = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_thumb');
        $sViewImage = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_view');

        $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''), $sThumbnail,
            Phpfox::getParam('attachment.attachment_max_thumbnail'),
            Phpfox::getParam('attachment.attachment_max_thumbnail'));
        $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''), $sViewImage,
            Phpfox::getParam('attachment.attachment_max_medium'), Phpfox::getParam('attachment.attachment_max_medium'));

        $iFileSizes += (filesize($sThumbnail) + filesize($sThumbnail));

        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'attachment', $iFileSizes);

        $aAttachment = db()->select('*')
            ->from(Phpfox::getT('attachment'))
            ->where('attachment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $sImagePath = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aAttachment['server_id'],
            'path' => 'core.url_attachment',
            'file' => $aAttachment['destination'],
            'suffix' => '_view',
            'max_width' => 'attachment.attachment_max_medium',
            'max_height' => 'attachment.attachment_max_medium',
            'return_url' => true
        ));

        $this->call('Editor.insert({is_image: true, name: \'\', id: \'' . $iId . ':view\', type: \'image\', path: \'' . $sImagePath . '\'});');

        if ($this->get('attachment-inline')) {
            $this->call('$Core.clearInlineBox();');
        } else {
            $this->call('tb_remove();');
        }
    }

    /**
     * Sets a new picture as a Profile Picture adding it to the Profile Pictures Album
     */
    public function makeProfilePicture()
    {
        Phpfox::isUser(true);
        $iAvatarId = storage()->get('user/avatar/' . Phpfox::getUserId());
        if ($iAvatarId) {
            $iAvatarId = $iAvatarId->value;
        }
        $iPhotoId = $this->get('photo_id');
        if ($iAvatarId && ($iAvatarId == $iPhotoId)) {
            $this->alert(_p('The photo has already made as your profile picture.'));
            return false;
        }

        /* Just call the service it'll take care of everything */
        if (Phpfox::getService('photo.process')->makeProfilePicture($iPhotoId)) {
            Phpfox::addMessage(_p('profile_photo_successfully_updated'));
            $this->call('$(".photo_make_as_profile").attr("onclick", "return false;");');
            $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('profile') . '\';');
        } else {
            $this->alert(_p('Cannot find the photo.'));
            return false;
        }
    }

    /**
     * Sets a new picture as a Profile Picture adding it to the Profile Pictures Album
     */
    public function makeCoverPicture()
    {
        Phpfox::isUser(true);
        $iCover = storage()->get('user/cover/' . Phpfox::getUserId());
        if ($iCover) {
            $iCover = $iCover->value;
        }

        $iPhotoId = $this->get('photo_id');

        if ($iCover && ($iCover == $iPhotoId)) {
            $this->alert(_p('the_photo_has_already_made_as_your_cover_picture'));
            return false;
        }
        /* Just call the service it'll take care of everything */

        if (Phpfox::getService('photo.process')->makeCoverPicture($iPhotoId)) {
            Phpfox::addMessage(_p('cover_photo_successfully_updated'));
            $this->call('$(".photo_make_as_cover").attr("onclick", "return false;");');
            $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('profile') . '\';');
        } else {
            $this->alert(_p('Cannot find the photo.'));
            return false;
        }
    }

    /**
     * Show all user tags on albums
     */
    public function browseAlbumTags()
    {
        $this->error(false);
        $aAlbum = Phpfox::getService('photo.album')->getForView($this->get('album_id', 0));
        Phpfox::getBlock('photo.album-tag', ['aAlbum' => $aAlbum, 'view' => 'all']);

        if ($this->get('page')) {
            $content = $this->getContent(false);
            $this->call('$("#js_album_tag_content").find(".js_pager_popup_view_more_link").remove();');
            $this->append('#js_album_tag_content', $content);
            $this->call('$Core.loadInit();');
        } else {
            $sTitle = _p('People In This Album');
            $this->setTitle($sTitle);
            $this->call('<script>$Core.loadInit();</script>');
        }
    }

    /**
     * @deprecated from 4.6.0
     */
    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
                'table' => 'photo_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );
    }

    /**
     * This function use in AdminCP, manage category
     * This function for active/de-active a category
     */
    public function toggleActiveCategory()
    {
        $iCategoryId = $this->get('id');
        $iActive = $this->get('active');
        Phpfox::getService('photo.category.process')->toggleActiveCategory($iCategoryId, $iActive);
    }

    public function removePhoto()
    {
        Phpfox::isUser(true);
        $iPhotoId = $this->get('id', false);

        if ($iPhotoId && Phpfox::getService('photo.process')->delete($iPhotoId)) {
            $this->call('$Core.Photo.removeUploadedPhoto(' . $iPhotoId . ')');
        }
    }
}

<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Attachment_Component_Controller_Frame extends Phpfox_Component
{
    /**
     * Controller
     * @throws Exception
     */
    public function process()
    {
        header('Content-Type:text/javascript');
        if (!isset($_FILES['file']) && isset($_FILES['Filedata'])) {
            $_FILES['file'] = array();
            $_FILES['file']['error']['file'] = UPLOAD_ERR_OK;
            $_FILES['file']['name']['file'] = $_FILES['Filedata']['name'];
            $_FILES['file']['type']['file'] = $_FILES['Filedata']['type'];
            $_FILES['file']['tmp_name']['file'] = $_FILES['Filedata']['tmp_name'];
            $_FILES['file']['size']['file'] = $_FILES['Filedata']['size'];
        } elseif (!isset($_FILES['file'])) {
            exit;
        }

        $oFile = Phpfox_File::instance();
        $oImage = Phpfox_Image::instance();
        $oAttachment = Phpfox::getService('attachment.process');
        $sIds = '';
        $textareaId = $this->request()->get('textarea_id');
        $iUploaded = 0;
        $iFileSizes = 0;

        foreach ($_FILES['file']['error'] as $iKey => $sError) {
            if ($sError == UPLOAD_ERR_OK) {
                $aValid = array('gif', 'png', 'jpg');
                if ($this->request()->get('custom_attachment') == 'photo') {
                    $aValid = array('gif', 'png', 'jpg');
                }

                if ($this->request()->get('input') == '' && $this->request()->get('custom_attachment') == '') {
                    $aValid = Phpfox::getService('attachment.type')->getTypes();
                }

                if (empty($aValid)) {
                    Phpfox_Error::set(_p('attachment_does_not_support_any_extension'));
                } else {
                    $iMaxSize = null;

                    if (Phpfox::getUserParam('attachment.item_max_upload_size') !== 0) {
                        $iMaxSize = (Phpfox::getUserParam('attachment.item_max_upload_size') / 1024);
                    }

                    $aImage = $oFile->load('file[' . $iKey . ']', $aValid, $iMaxSize);
                }

                if (isset($aImage) && $aImage !== false) {
                    if (!Phpfox::getService('attachment')->isAllowed()) {
                        echo 'window.parent.$(\'#' . $this->request()->get('upload_id') . '\').parents(\'.js_upload_attachment_parent_holder\').html(\'<div class="error_message">' . _p('failed_limit_reached') . '</div>\');';

                        continue;
                    }

                    $iUploaded++;
                    $bIsImage = in_array($aImage['ext'], Phpfox::getParam('attachment.attachment_valid_images'));

                    $iId = $oAttachment->add(array(
                            'category' => $this->request()->get('category_name'),
                            'file_name' => $_FILES['file']['name'][$iKey],
                            'extension' => $aImage['ext'],
                            'is_image' => $bIsImage
                        )
                    );

                    $sIds .= $iId . ',';

                    $sFileName = $oFile->upload('file[' . $iKey . ']', Phpfox::getParam('core.dir_attachment'), $iId);
                    if (Phpfox::isModule('photo')) {
                        Phpfox::getService('photo')->cropMaxWidth(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName,
                                ''));
                    }
                    $sFileSize = filesize(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''));
                    $iFileSizes += $sFileSize;

                    $oAttachment->update(array(
                        'file_size' => $sFileSize,
                        'destination' => $sFileName,
                        'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
                    ), $iId);

                    if ($bIsImage) {
                        $sThumbnail = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_thumb');
                        $sViewImage = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_view');

                        $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''),
                            $sThumbnail, Phpfox::getParam('attachment.attachment_max_thumbnail'),
                            Phpfox::getParam('attachment.attachment_max_thumbnail'));
                        $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''),
                            $sViewImage, Phpfox::getParam('attachment.attachment_max_medium'),
                            Phpfox::getParam('attachment.attachment_max_medium'));

                        $iFileSizes += (filesize($sThumbnail) + filesize($sThumbnail));
                    }
                } else {
                    // error processing
                    header('Content-Type:application/json');
                    http_response_code(400);

                    return [
                        'error' => implode(', ', Phpfox_Error::get())
                    ];
                }
            }
        }

        if (!$iUploaded) {
            exit;
        }

        if ($this->request()->get('custom_attachment') == 'photo' || $this->request()->get('custom_attachment') == 'video') {
            $aAttachment = Phpfox_Database::instance()->select('*')
                ->from(Phpfox::getT('attachment'))
                ->where('attachment_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            if ($this->request()->get('custom_attachment') == 'photo') {
                $sImagePath = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aAttachment['server_id'],
                    'path' => 'core.url_attachment',
                    'file' => $aAttachment['destination'],
                    'suffix' => '_view',
                    'max_width' => 'attachment.attachment_max_medium',
                    'max_height' => 'attachment.attachment_max_medium',
                    'return_url' => true
                ));

                echo '
					window.parent.Editor.setId("' . $textareaId . '").insert({is_image: true, name: \'\', id: \'' . $iId . ':view\', type: \'image\', path: \'' . $sImagePath . '\'});
				';
            } else {
                echo '
					window.parent.Editor.setId("' . $textareaId . '").insert({is_image: true, name: \'\', id: \'' . $iId . '\', type: \'video\'});
				';
            }
        } else {
            ob_start();

            Phpfox::getBlock('attachment.list', array(
                'sIds' => $sIds,
                'bCanUseInline' => true,
                'attachment_no_header' => true,
                'attachment_edit' => true,
                'sAttachmentInput' => $this->request()->get('input'),
                'bGetAttachmentList' => true
            ));

            $sContent = ob_get_contents();

            ob_clean();

            $sAttachmentObject = $this->request()->get('attachment_obj_id');

            if (!empty($sAttachmentObject)) {
                echo '
					var $oParent = window.parent.$(\'#' . $this->request()->get('attachment_obj_id') . '\');
					$oParent.find(\'.js_attachment:first\').val($oParent.find(\'.js_attachment:first\').val() + \'' . $sIds . '\');
					$oParent.find(\'.js_attachment_list:first\').show();';
                echo "\$Core.Attachment.appendAttachmentList('{$this->request()->get('attachment_obj_id')}', '" . base64_encode($sContent) . "');";
                echo 'window.parent.$Core.loadInit();';
            }

            if ($this->request()->get('category_name') == 'theme') {
                echo '
					var $oParent = window.parent.$(\'#' . $this->request()->get('input') . '\');
					$oParent.val(\'' . Phpfox::getParam('core.url_attachment') . sprintf($sFileName, '') . '\');
					// window.parent.on_change_image($oParent);
					$oParent.focus();
					$oParent.blur();
					window.parent.tb_remove();
				';
            }
        }
        // increase counter
        echo "\$Core.Attachment.increaseCounter('{$this->request()->get('holder_id')}');";
        echo '$Core.Attachment.resetForm();';

        // Update user space usage
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'attachment', $iFileSizes);

        if ($this->request()->get('attachment_inline')) {
            echo 'window.parent.$Core.updateInlineBox();';
        }

        // use inline
        if (isset($iId) && $this->request()->get('custom_attachment') == 'photo') {
            echo '$("#js_attachment_id_' . $iId. '").find(".js_attachment_add_inline a").trigger("click");';
        }

        exit;
    }
}

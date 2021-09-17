<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Attachment_Service_Api extends \Core\Api\ApiServiceBase
{
    public function __construct()
    {
        $this->setPublicFields([
            'attachment_id',
            'is_image',
            'image_link'
        ]);
    }

    /**
     * @description: Add attachment
     * @return array|bool
     */
    public function post()
    {
        $this->isUser();

        if (empty($this->request()->get('category_name')) || !isset($_FILES['file'])) {
            return $this->error(_p('Please input category_name and attachment files.'));
        }

        $iUploaded = 0;
        $iFileSizes = 0;
        $aReturnParams = array();
        $sIds = '';
        $oFile = Phpfox_File::instance();
        $oAttachment = Phpfox::getService('attachment.process');
        $oImage = Phpfox_Image::instance();
        foreach ($_FILES['file']['error'] as $iKey => $sError) {
            if ($sError == UPLOAD_ERR_OK) {
                $aValid = Phpfox::getService('attachment.type')->getTypes();
                $iMaxSize = null;
                if (Phpfox::getUserParam('attachment.item_max_upload_size') !== 0) {
                    $iMaxSize = (Phpfox::getUserParam('attachment.item_max_upload_size') / 1024);
                }
                $aImage = $oFile->load('file[' . $iKey . ']', $aValid, $iMaxSize);
                if ($aImage !== false) {
                    if (!Phpfox::getService('attachment')->isAllowed()) {
                        return $this->error(_p('failed_limit_reached'));
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
                        Phpfox::getService('photo')->cropMaxWidth(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''));
                    }
                    $sFileSize = filesize(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''));
                    $iFileSizes += $sFileSize;

                    $oAttachment->update(array(
                        'file_size' => $sFileSize,
                        'destination' => $sFileName,
                        'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
                    ), $iId);
                    $sImageLink = '';
                    if ($bIsImage) {
                        $sThumbnail = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_thumb');
                        $sViewImage = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_view');

                        $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''), $sThumbnail, Phpfox::getParam('attachment.attachment_max_thumbnail'), Phpfox::getParam('attachment.attachment_max_thumbnail'));
                        $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''), $sViewImage, Phpfox::getParam('attachment.attachment_max_medium'), Phpfox::getParam('attachment.attachment_max_medium'));

                        $iFileSizes += (filesize($sThumbnail) + filesize($sThumbnail));
                        $aAttachment = Phpfox_Database::instance()->select('*')
                            ->from(Phpfox::getT('attachment'))
                            ->where('attachment_id = ' . (int) $iId)
                            ->execute('getSlaveRow');
                        $sImageLink = Phpfox::getLib('image.helper')->display(array('server_id' => $aAttachment['server_id'], 'path' => 'core.url_attachment', 'file' => $aAttachment['destination'], 'suffix' => '_view', 'max_width' => 'attachment.attachment_max_medium', 'max_height' =>'attachment.attachment_max_medium', 'return_url' => true));
                    }
                    $aReturnParams[] = [
                        'attachment_id' => $iId,
                        'is_image' => $bIsImage,
                        'image_link' => $sImageLink
                    ];
                }
            }
        }
        // Update user space usage
        if ($iUploaded) {
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'attachment', $iFileSizes);
            return $this->success($aReturnParams, [_p('{{ item }} successfully added.', ['item' => _p('attachment')])]);
        } else {
            return $this->error(_p('uploaded_file_is_not_valid'));
        }
    }
}
<?php

namespace Apps\Core_eGifts\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox_Error;
use Phpfox;

class AddGiftController extends Admincp_Component_Controller_App_Index
{
    const PHOTO_FIELD = 'file';

    public function process()
    {
        $bIsEdit = false;
        $iEditId = $this->request()->getInt('edit');
        $iMaxFileSize = (Phpfox::getUserParam('photo.photo_max_upload_size') === 0 ? null : ((Phpfox::getUserParam('photo.photo_max_upload_size') / 1024) * 1048576));
        if ($iEditId) {
            $aRow = Phpfox::getService('egift')->getEgift($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms' => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        $aValidation = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('fill_title_for_egift')
            ),
            'currency' => array(
                'def' => 'currency',
                'min' => '0',
                'title' => _p('provide_a_valid_price_for_egift'),
            ),
        );
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'core_js_egift_form',
                'aParams' => $aValidation
            )
        );

        $aCategories = Phpfox::getService('egift.category')->getCategories();
        $this->template()
            ->setTitle($bIsEdit ? _p('edit_egift') : _p('add_egift'))
            ->setBreadCrumb($bIsEdit ? _p('edit_egift') : _p('add_egift'))
            ->assign([
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'aCategories' => $aCategories,
                'bIsEdit' => $bIsEdit,
                'iEditId' => $iEditId,
                'iMaxFileSize' => $iMaxFileSize,
            ]);
        if ($aVals = $this->request()->getArray('val')) {
            if ($oValid->isValid($aVals)) {
                // Verify image before processing
                $aFile = $this->request()->get(self::PHOTO_FIELD);
                if ((!$bIsEdit && $aFile['error'] != UPLOAD_ERR_NO_FILE) || (!empty($aFile) && $aFile['error'] != UPLOAD_ERR_NO_FILE)) {
                    // Try to load image
                    $aType = array('jpg', 'gif', 'png');
                    Phpfox::getLib('file')->load(self::PHOTO_FIELD, $aType);
                    if ($iMaxFileSize && isset($aFile['size']) && $aFile['size'] > $iMaxFileSize) {
                        Phpfox_Error::set(_p('upload_failed_your_file_size_is_larger_then_our_limit_file_size', array(
                            'size' => Phpfox::getLib('phpfox.file')->filesize($aFile['size']),
                            'file_size' => Phpfox::getLib('phpfox.file')->filesize($iMaxFileSize)
                        )));
                    } else {
                        $aVals = array_merge($aVals, $aFile);
                    }
                } else {
                    return Phpfox_Error::set(_p('image_is_required'));
                }

                if (Phpfox_Error::isPassed()) {
                    //Prevent null in currency
                    foreach ($aVals['currency'] as &$currency) {
                        if (!$currency) {
                            $currency = 0;
                        }
                    }

                    // Process update or add a new egift
                    if ($bIsEdit) {
                        $bSuccess = Phpfox::getService('egift.process')->updateGift($iEditId, $aVals);
                        $this->url()->send('admincp.egift.manage-gifts', array(),
                            $bSuccess ? _p('egift_successfully_updated') : _p('egift_updated_fail'));
                    } else {
                        $iId = Phpfox::getService('egift.process')->addGift($aVals);
                        $this->url()->send('admincp.egift.manage-gifts', array(),
                            $iId ? _p('egift_successfully_added') : _p('egift_added_fail'));
                    }
                }
            }
        }
    }
}

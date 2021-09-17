<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Validator;

defined('PHPFOX') or exit('NO DICE!');


class AddController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        Phpfox::isUser(true);

        $bIsEdit = false;
        $bIsSetup = ($this->request()->get('req4') == 'setup' ? true : false);
        $sAction = $this->request()->get('req3');

        if ($iEditId = $this->request()->getInt('id')) {
            if (($aListing = Phpfox::getService('marketplace')->getForEdit($iEditId))) {
                $bIsEdit = true;
                $this->setParam('aListing', $aListing);
                $this->setParam(array(
                        'country_child_value' => $aListing['country_iso'],
                        'country_child_id' => $aListing['country_child_id']
                    )
                );
                $this->template()->setHeader(array(
                        '<script type="text/javascript">$Behavior.marketplaceEditCategory = function(){ var aCategories = explode(\',\', \'' . $aListing['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).prop(\'selected\', true); } $Behavior.marketplaceEditCategory = function(){} }</script>'
                    )
                )
                    ->assign(array(
                            'aForms' => $aListing
                        )
                    );
            }
        } else {
            Phpfox::getUserParam('marketplace.can_create_listing', true);
            $this->template()->assign('aForms', array('price' => '0.00'));
        }

        $aValidation = array(
            'title' => _p('provide_a_name_for_this_listing'),
            'country_iso' => _p('provide_a_location_for_this_listing'),
            'price' => array(
                'def' => 'money',
                'title' => _p('please_type_valid_price')
            )
        );

        $oValidator = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_marketplace_form',
                'aParams' => $aValidation
            )
        );

        if ($aVals = $this->request()->get('val')) {
            if ($oValidator->isValid($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('marketplace.process')->update($aListing['listing_id'], $aVals)) {
                        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_add_process_update_complete')) ? eval($sPlugin) : false);

                        if ($bIsSetup) {
                            switch ($sAction) {
                                case 'customize':
                                    $this->url()->send('marketplace.add.invite.setup',
                                        array('id' => $aListing['listing_id']),
                                        _p('successfully_uploaded_images_for_this_listing'));
                                    break;
                                case 'invite':
                                    $this->url()->permalink('marketplace', $aListing['listing_id'], $aListing['title'],
                                        true, _p('successfully_invited_users_for_this_listing'));
                                    break;
                            }

                        } else {
                            switch ($this->request()->get('page_section_menu')) {
                                case 'js_mp_block_customize':
                                    $sMessage = _p('successfully_uploaded_images');
                                    break;
                                case 'js_mp_block_invite':
                                    $sMessage = _p('successfully_invited_users');
                                    break;
                                default:
                                    $sMessage = _p('listing_successfully_updated');
                                    break;
                            }

                            $this->url()->send('marketplace.add', ['id' => $aListing['listing_id'], 'tab' => empty($aVals['current_tab']) ? '' : $aVals['current_tab']], $sMessage);
                        }
                    }
                } else {
                    if (($iFlood = Phpfox::getUserParam('marketplace.flood_control_marketplace')) !== 0) {
                        $aFlood = array(
                            'action' => 'last_post', // The SPAM action
                            'params' => array(
                                'field' => 'time_stamp', // The time stamp field
                                'table' => Phpfox::getT('marketplace'), // Database table we plan to check
                                'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                                'time_stamp' => $iFlood * 60 // Seconds);
                            )
                        );

                        // actually check if flooding
                        if (Phpfox::getLib('spam')->check($aFlood)) {
                            Phpfox_Error::set(_p('you_are_creating_a_listing_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                        }
                    }

                    if (Phpfox_Error::isPassed()) {
                        if ($iId = Phpfox::getService('marketplace.process')->add($aVals)) {
                            if ($aListing = Phpfox::getService('marketplace')->getForEdit($iId)) {
                                $this->url()->send('marketplace.add.customize.setup', array('id' => $iId),
                                    _p('listing_successfully_added'));
                            } else {
                                $this->url()->permalink('marketplace', $iId, $aVals['title'], true,
                                    _p('listing_successfully_added'));
                            }
                        }
                    }
                }
            }
        }

        $aCurrencies = Phpfox::getService('core.currency')->get();
        if (!$aCurrencies || !count($aCurrencies)) {
            return Phpfox_Error::display(_p('marketplace_missing_currency'));
        }
        foreach ($aCurrencies as $iKey => $aCurrency) {
            $aCurrencies[$iKey]['is_default'] = '0';

            if (Phpfox::getService('core.currency')->getDefault() == $iKey) {
                $aCurrencies[$iKey]['is_default'] = '1';
            }
        }

        $iTotalImage = 0;
        if ($bIsEdit) {
            $aMenus = array(
                'detail' => _p('listing_details'),
                'customize' => _p('photos'),
                'invite' => _p('invite')
            );

            if (!$bIsSetup) {
                $aMenus['manage'] = _p('manage_invites');
            }

            $iTotalImage = Phpfox::getService('marketplace')->countImages($aListing['listing_id']);
            $this->template()->buildPageMenu('js_mp_block',
                $aMenus,
                array(
                    'link' => $this->url()->permalink('marketplace', $aListing['listing_id'], $aListing['title']),
                    'phrase' => _p('view_this_listing')
                )
            );
        }

        $this->template()->setTitle(($bIsEdit ? _p('editing_listing') . ': ' . $aListing['title'] : _p('create_a_marketplace_listing')))
            ->setBreadCrumb(_p('marketplace'), $this->url()->makeUrl('marketplace'))
            ->setBreadCrumb(($bIsEdit ? _p('editing_listing') . ': ' . $aListing['title'] : _p('create_a_listing')),
                $this->url()->makeUrl('marketplace.add', ['id' => $iEditId]), true)
            ->setEditor()
            ->setPhrase(array(
                    'select_a_file_to_upload'
                )
            )
            ->setHeader(array(
                    'country.js' => 'module_core'
                )
            )
            ->assign(array(
                    'sMyEmail' => Phpfox::getUserBy('email'),
                    'sCreateJs' => $oValidator->createJS(),
                    'sGetJsForm' => $oValidator->getJsForm(false),
                    'bIsEdit' => $bIsEdit,
                    'sCategories' => Phpfox::getService('marketplace.category')->get(),
                    'iMaxFileSize' => (Phpfox::getUserParam('marketplace.max_upload_size_listing') === 0 ? '' : (Phpfox::getUserParam('marketplace.max_upload_size_listing'))),
                    'aCurrencies' => $aCurrencies,
                    'iTotalImage' => $iTotalImage,
                    'iTotalImageLimit' => Phpfox::getUserParam('marketplace.total_photo_upload_limit'),
                    'iRemainUpload' => Phpfox::getUserParam('marketplace.total_photo_upload_limit') - $iTotalImage,
                    'sUserSettingLink' => $this->url()->makeUrl('user.setting')
                )
            );
        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                    'type' => 'marketplace',
                    'id' => 'js_marketplace_form',
                    'edit_id' => ($bIsEdit ? $iEditId : 0)
                )
            );
        }
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_add_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}
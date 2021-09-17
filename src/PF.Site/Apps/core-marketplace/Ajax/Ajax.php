<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Ajax;

use Phpfox;
use Phpfox_Ajax;

defined('PHPFOX') or exit('NO DICE!');


class Ajax extends Phpfox_Ajax
{
    public function delete()
    {
        $iId = $this->get('id');
        $bIsDetail = $this->get('is_detail', 0);

        if (!$iId) {
            return false;
        }
        else {
            if (Phpfox::getService('marketplace.process')->delete($iId)) {
                Phpfox::addMessage(_p('successfully_deleted_listing'));
            }
            else {
                $this->alert(_p('you_do_not_have_sufficient_permission_to_delete_this_listing'));
                return false;
            }
        }
        if (!$bIsDetail) {
            $this->call('window.location.reload();');
        } else {
            $sUrl = Phpfox::getLib('url')->makeUrl('marketplace');
            $this->call('window.location.href = "' . $sUrl . '";');
        }
    }

    public function setDefault()
    {
        if (Phpfox::getService('marketplace.process')->setDefault($this->get('id'))) {

        }
    }

    public function deleteImage()
    {

        $iTotalLimit = Phpfox::getUserParam('marketplace.total_photo_upload_limit');
        if ($aImage = Phpfox::getService('marketplace.process')->deleteImage($this->get('id'), true)) {
            if (isset($aImage['image_id'])) {
                $this->call('$(\'#js_photo_holder_'.$aImage['image_id'].'\').find(\'.is-default\').show();');
            }
        }
        $iTotalPhoto = Phpfox::getService('marketplace')->countImages($this->get('listing_id'));
        if ($iTotalPhoto < $iTotalLimit) {
            $this->call('$(\'#js_listing_upload_photo\').show();$(\'#js_listing_total_photo\').html(\'' . $iTotalPhoto . ' ' . (($iTotalPhoto == 1) ? _p('photo') : _p('photos')) . '\');');
            if ($iTotalPhoto == 0) {
                $this->call('$(\'.manage-photo .block\').append(\'<div class="help-block">'._p('no_photos_found').'</div>\').find(\'.item-container\').remove();');
            }
        }
    }

    public function listInvites()
    {
        Phpfox::getBlock('marketplace.list');

        $this->html('#js_mp_item_holder', $this->getContent(false));
    }

    public function feature()
    {
        if (Phpfox::getService('marketplace.process')->feature($this->get('listing_id'), $this->get('type'))) {
            // js_mp_item_holder_4
            if ($this->get('type')) {
                $this->addClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_featured');
                $this->alert(_p('listing_successfully_featured'), _p('feature'), 300, 150, true);
            } else {
                $this->removeClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_featured');
                $this->alert(_p('listing_successfully_un_featured'), _p('un_feature'), 300, 150, true);
            }
        }
    }

    /**
     * @return null|string
     */
    public function sponsor()
    {
        if (!Phpfox::isModule('ad')) {
            return $this->alert(_p('your_request_is_invalid'));
        }
        if (Phpfox::getService('marketplace.process')->sponsor($this->get('listing_id'), $this->get('type'))) {
            $aListing = Phpfox::getService('marketplace')->getListing($this->get('listing_id'));
            if ($this->get('type') == '1') {
                $sModule = _p('marketplace');
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'marketplace',
                    'item_id' => $this->get('listing_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => $sModule, 'name' => $aListing['title']])
                ));
                // listing was sponsored
                $sHtml = '<a href="#" title="' . _p('unsponsor_this_listing') . '" onclick="$(\'#js_sponsor_phrase_' . $this->get('listing_id') . '\').hide(); $.ajaxCall(\'marketplace.sponsor\', \'listing_id=' . $this->get('listing_id') . '&amp;type=0\', \'GET\'); return false;"><i class="ico ico-sponsor mr-1"></i>' . _p('unsponsor_this_listing') . '</a>';
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('marketplace', $this->get('listing_id'));
                $sHtml = '<a href="#" title="' . _p('unsponsor_this_listing') . '" onclick="$(\'#js_sponsor_phrase_' . $this->get('listing_id') . '\').show(); $.ajaxCall(\'marketplace.sponsor\', \'listing_id=' . $this->get('listing_id') . '&amp;type=1\', \'GET\'); return false;"><i class="ico ico-sponsor mr-1"></i>' . _p('sponsor_this_listing') . '</a>';
            }
            $this->html('#js_sponsor_' . $this->get('listing_id'),
                $sHtml)->alert($this->get('type') == '1' ? _p('listing_successfully_sponsored') : _p('listing_successfully_un_sponsored'));
            if ($this->get('type') == '1') {
                $this->addClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_sponsored');
            } else {
                $this->removeClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_sponsored');
            }
        }
    }

    public function approve()
    {
        if (Phpfox::getService('marketplace.process')->approve($this->get('listing_id'))) {
            $this->alert(_p('listing_has_been_approved'), _p('listing_approved'), 300, 100, true);
            $this->hide('#js_item_bar_approve_image');
            $this->hide('.js_moderation_off');
            $this->show('.js_moderation_on');
            $this->call('window.location.reload();');
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sMessage = '';
        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('marketplace.can_approve_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('marketplace.process')->approve($iId);
                }
                Phpfox::addMessage(_p('listing_s_successfully_approved'));
                $this->call('window.location.reload();');
                break;
            case 'delete':
                Phpfox::getUserParam('marketplace.can_delete_other_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('marketplace.process')->delete($iId);
                    $this->slideUp('#js_mp_item_holder_' . $iId);
                }
                Phpfox::addMessage(_p('listing_s_successfully_deleted'));
                $this->call('window.location.reload();');
                break;
            case 'feature':
                Phpfox::getUserParam('marketplace.can_feature_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('marketplace.process')->feature($iId, 1);
                    $this->addClass('#js_mp_item_holder_' . $iId, 'row_featured');
                }
                $sMessage = _p('listing_s_successfully_featured');
                break;
            case 'un-feature':
                Phpfox::getUserParam('marketplace.can_feature_listings', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('marketplace.process')->feature($iId, 0);
                    $this->removeClass('#js_mp_item_holder_' . $iId, 'row_featured');
                }
                $sMessage = _p('listing_s_successfully_un_featured');
                break;
            default:
                $sMessage = '';
                break;
        }
        if (!empty($sMessage)) {
            $this->alert($sMessage, _p('Moderation'), 300, 150, true);
        }
        $this->call('$(\'.moderator_active\').remove();');
        $this->hide('.moderation_process');
    }

    /**
     * @deprecated from v4.7
     */
    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering([
            'table' => 'marketplace_category',
            'key' => 'category_id',
            'values' => $aVals['ordering']
        ]);
        Phpfox::getLib('cache')->remove();
    }

    public function toggleActiveCategory()
    {
        $iCategoryId = $this->get('id');
        $iActive = $this->get('active');
        Phpfox::getService('marketplace.category.process')->toggleActiveCategory($iCategoryId, $iActive);
    }

    public function toggleUploadSection()
    {
        $bShowUpload = $this->get('show_upload');
        $iId = $this->get('id');
        $aListing = Phpfox::getService('marketplace')->getForEdit($iId);
        if (!$iId) {
            return false;
        }
        if ($bShowUpload) {
            $iTotalImage = Phpfox::getService('marketplace')->countImages($iId);
            $this->template()->
            assign([
                'aForms' => $aListing,
                'iTotalImage' => $iTotalImage,
                'iTotalImageLimit' => Phpfox::getUserParam('marketplace.total_photo_upload_limit'),
                'iRemainUpload' => Phpfox::getUserParam('marketplace.total_photo_upload_limit') - $iTotalImage,
                'iListingId' => $iId,
                'iMaxFileSize' => (Phpfox::getUserParam('marketplace.max_upload_size_listing') === 0 ? '' : (Phpfox::getUserParam('marketplace.max_upload_size_listing'))),
            ])->getTemplate('marketplace.block.upload-photo');
            $this->call('$(\'#js_mp_block_customize\').html(\'' . $this->getContent() . '\');');
            $this->call('$Core.loadInit();');
        } else {
            Phpfox::getBlock('marketplace.photo', ['aListing' => $aListing]);
            $this->call('$(\'#js_mp_block_customize\').html(\'' . $this->getContent() . '\');');
            $this->call('$Core.loadInit();');
        }
    }
}
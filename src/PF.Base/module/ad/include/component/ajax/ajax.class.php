<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Ajax_Ajax
 */
class Ad_Component_Ajax_Ajax extends Phpfox_Ajax
{
    /**
     *
     */
    public function update()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_ajax_update__start')) ? eval($sPlugin) : false);
        Phpfox::getBlock('ad.display', array('block_id' => $this->get('block_id')));

        $this->html('#js_ad_space_' . $this->get('block_id'), $this->getContent(false));

        (($sPlugin = Phpfox_Plugin::get('ad.component_ajax_update__end')) ? eval($sPlugin) : false);
    }

    /**
     *
     */
    public function sample()
    {
        echo '<iframe src="' . Phpfox_Url::instance()->makeUrl('ad', array(
                'sample',
                'get-block-layout' => 'true',
                'click' => ($this->get('click') ? '1' : '0'),
                'no-click' => ($this->get('no-click') ? '1' : '0')
            )) . '" width="100%" height="500" frameborder="0" scrolling="yes"></iframe>';
    }

    /**
     *
     */
    public function getAdPrice()
    {
        if (Phpfox::getParam('ad.multi_ad')) {
            if ($this->get('placement_id')) {
                $aPlan = Phpfox::getService('ad')->getPlan($this->get('placement_id'), false);
            } else {
                $aPlan = Phpfox::getService('ad')->getPlan(50, true);
            }
        } else {
            $aPlan = Phpfox::getService('ad')->getPlan($this->get('block_id'), false);
        }

        if ($aPlan) {
            if ($aPlan['is_cpm']) {
                $this->html('#js_ad_cpm', _p('impressions') . ":");
            } else {
                $this->html('#js_ad_cpm', _p('clicks') . ":");
            }
            $this->val('#js_is_cpm', $aPlan['is_cpm']);
            $this->html('#js_ad_cost', Phpfox::getService('core.currency')->getCurrency($aPlan['default_cost']));
            $this->val('#js_total_ad_cost', $aPlan['default_cost']);
            $this->show('#js_ad_continue_next_step');
            $this->hide('#js_ad_continue_form_button');
            $this->call('$Core.Ad.recalculate();');
        }
    }

    /**
     * Used when calculating the cost of sponsoring an item
     * @param sTargetId DOM id of the element displaying the value
     * @param fCost
     */
    public function getCost()
    {
        $this->html('#' . $this->get('sTargetId'),
            Phpfox::getService('core.currency')->getCurrency($this->get('fCost')));
        $this->call("$('#" . $this->get('sTargetId') . "').show();");
    }

    /**
     *
     */
    public function recalculate()
    {
        if (((int)$this->get('total') < 1000) && ($this->get('isCPM'))) {
            $this->alert(_p('there_is_minimum_of_1000_impressions'));
        } else {
            $iBlockId = $this->get('block_id');
            if ($aPlan = Phpfox::getService('ad')->getPlan($iBlockId, ($iBlockId == 50) ? true : false)) {
                $iTotal = ($this->get('isCPM') ? (($this->get('total') / 1000) * $aPlan['default_cost']) : ($this->get('total') * $aPlan['default_cost']));
                $this->html('#js_ad_cost', Phpfox::getService('core.currency')->getCurrency($iTotal))
                    ->hide('#js_ad_cost_recalculate')
                    ->show('#js_ad_cost');
            }
        }
    }

    /**
     * Update Ad activity
     */
    public function updateAdActivity()
    {
        Phpfox::getService('ad.process')->updateActivityAjax($this->get('id'), $this->get('active'));
    }

    /**
     * Update Sponsor Activity
     */
    public function updateSponsorActivity()
    {
        if (Phpfox::getService('ad.process')->updateSponsorActivity($this->get('id'), $this->get('active'))) {
            Phpfox::getLib('cache')->removeGroup('ad');
            Phpfox::getLib('cache')->removeGroup('feed');
            if ($this->get('active') == '1') {
                $this->alert(_p('enabled'));
            } else {
                $this->alert(_p('disabled'));
            }
        }
    }

    /**
     *
     */
    public function updateAdActivityUser()
    {
        Phpfox::getService('ad.process')->updateActivityAjax($this->get('id'), $this->get('active'),
            Phpfox::getUserId());
    }

    /**
     * Update Ad Placement Activity
     */
    public function updateAdPlacementActivity()
    {
        Phpfox::getService('ad.process')->updateAdPlacementActivity($this->get('id'), $this->get('active'));
    }

    /**
     * Check Ad Form
     * @throws Exception
     */
    public function checkAdForm()
    {
        $aVals = $this->get('val');
        $oFormat = Phpfox::getLib('parse.format');

        if ($aVals['type_id'] == '2') {
            if ($oFormat->isEmpty($aVals['title'])) {
                Phpfox_Error::set(_p('provide_a_title_for_your_ad'));
            }
            if ($oFormat->isEmpty($aVals['body_text'])) {
                Phpfox_Error::set(_p('provide_text_for_your_ad'));
            }
        } else {
            $sImage = $this->get('image');
            if ($oFormat->isEmpty($sImage)) {
                Phpfox_Error::set(_p('select_an_image_for_your_ad'));
            }
        }

        if ($oFormat->isEmpty($aVals['url_link'])) {
            Phpfox_Error::set(_p('provide_a_url_for_your_ad'));
        } else {
            if (strpos($aVals['url_link'], 'https://') === false && strpos($aVals['url_link'], 'http://') === false) {
                $aVals['url_link'] = 'http://' . $aVals['url_link'];
            }
            if (!Phpfox_Validator::instance()->verify('url', $aVals['url_link'])) {
                Phpfox_Error::set(_p('provide_a_url_for_your_ad'));
            }
        }
        if ($oFormat->isEmpty($aVals['name'])) {
            Phpfox_Error::set(_p('provide_a_campaign_name'));
        }
        if ($oFormat->isEmpty($aVals['total_view'])) {
            Phpfox_Error::set(_p('there_is_minimum_of_1000_impressions'));
        } else {
            if ((int)$aVals['total_view'] < 1000 && $aVals['is_cpm']) {
                Phpfox_Error::set(_p('there_is_minimum_of_1000_impressions'));
            }
        }

        if (Phpfox_Error::isPassed()) {
            $this->call('$(\'#js_custom_ad_form\').submit();');
        }
    }

    public function removeSponsor()
    {
        if (Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && ($iSponsorId = Phpfox::getService('feed')->canSponsoredInFeed($this->get('type_id'),
                $this->get('item_id')))) {
            if ($iSponsorId === true) {
                $this->alert(_p('Cannot find the feed!'));
                return;
            }
            if (Phpfox::getService('ad.process')->deleteSponsor($iSponsorId, true)) {
                Phpfox::addMessage(_p('This item in feed has been unsponsored successfully!'));
                $this->call('$Core.reloadPage();');
            } else {
                $this->alert(_p('Cannot unsponsor this item in feed!'));
                return;
            }

        } else {
            $this->alert(_p('Cannot unsponsor this item in feed!'));
            return;
        }
    }
}

<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Polls\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class SponsoredBlock extends \Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        if (!Phpfox::isModule('ad')) {
            return false;
        }
        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        $iCacheTime = $this->getParam('cache_time', 5);
        $aSponsorPolls = Phpfox::getService('poll')->getSponsored($iLimit, $iCacheTime);

        if (empty($aSponsorPolls)) {
            return false;
        }

        foreach ($aSponsorPolls as $aPoll) {
            Phpfox::getService('ad.process')->addSponsorViewsCount($aPoll['sponsor_id'], 'poll', 'sponsor');
        }

        $this->template()->assign(array(
                'sHeader' => _p('sponsored'),
                'aSponsorPolls' => $aSponsorPolls,
            )
        );
        if (Phpfox::getUserParam('poll.can_sponsor_poll') || Phpfox::getUserParam('poll.can_purchase_sponsor_poll')) {
            $this->template()->assign([
                'aFooter' => array(
                    _p('encourage_sponsor_poll') => $this->url()->makeUrl('poll', array('view' => 'my', 'sponsor' => 1))
                )
            ]);
        }
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Sponsored Polls Limit'),
                'description' => _p('Define the limit of how many sponsored polls can be displayed when viewing the polls section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Polls Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Polls</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Sponsored Polls Limit" must be greater than or equal to 0')
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('poll.component_block_sponsor_clean')) ? eval($sPlugin) : false);
    }
}
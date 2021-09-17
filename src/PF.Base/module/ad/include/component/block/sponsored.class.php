<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Block_Sponsored
 */
class Ad_Component_Block_Sponsored extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->template()->assign(array(
                'sHeader' => _p('sponsored'),
            )
        );

        if (Phpfox::getUserParam('ad.can_create_ad_campaigns')) {
            $this->template()->assign(array(
                    'aFooter' => array(
                        _p('create_an_ad') => $this->url()->makeUrl('ad.add')
                    )
                )
            );
        }

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ad.component_block_sponsored_clean')) ? eval($sPlugin) : false);
    }
}

<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Module_Subscribe
 */
class Subscribe_Component_Controller_Index extends Phpfox_Component
{
    public function process()
    {
        $aPackages = Phpfox::getService('subscribe')->getPackages();
        if (Phpfox::getParam('subscribe.enable_subscription_packages')) {
            $this->template()->setTitle(_p('membership_packages'))
                ->setBreadCrumb(_p('membership_packages'))
                ->assign(array(
                        'aPackages' => $aPackages,
                        'sDefaultImagePath' => Phpfox::getParam('core.url_module') . 'subscribe/static/image/membership_thumbnail.jpg'
                    )
                );

            $aForCompare = Phpfox::getService('subscribe')->getPackagesForCompare();

            $this->template()
                ->setHeader([
                    'subscribe.js' => 'module_subscribe',
                ])
                ->assign('aComparePackages', $aForCompare);
        } else {
            $this->template()->setTitle(_p('membership_notice'))->setBreadCrumb(_p('membership_notice'));
        }

        if (!$aPackages) {
            return false;
        }

        return 'controller';
    }
}

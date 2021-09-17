<?php

namespace Apps\Core_Pages\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

class ClaimController extends \Phpfox_Component
{
    public function process()
    {
        $aClaims = \Phpfox::getService('pages')->getClaims();

        $this->template()->setTitle(_p('Claims'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Pages"), $this->url()->makeUrl('admincp.pages'))
            ->setBreadCrumb(_p('Claims'))
            ->assign(array(
                'aClaims' => $aClaims
            ))
            ->setPhrase(array(
                    'are_you_sure_you_want_to_transfer_ownership',
                    'are_you_sure_you_want_to_deny_this_claim_request'
                )
            );
    }
}

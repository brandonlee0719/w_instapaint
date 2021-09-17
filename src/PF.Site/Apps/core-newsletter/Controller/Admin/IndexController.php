<?php

namespace Apps\Core_Newsletter\Controller\Admin;

use Phpfox_Component;
use Phpfox;

class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        // Check if we want to delete a newsletter
        if ($iId = $this->request()->get('delete')) {
            if (Phpfox::getService('newsletter.process')->delete($iId)) // purge users
            {
                $this->url()->send('admincp.newsletter', null, _p('newsletter_successfully_deleted'));
            }
        }

        // Check if we want to stop the processing newsletter
        if ($iId = $this->request()->get('stop')) {
            if (Phpfox::getService('newsletter.process')->stop($iId)) // purge users
            {
                $this->url()->send('admincp.newsletter', null, _p('newsletter_successfully_stopped'));
            }
        }

        // check if there is any pending job or any user pending their newsletter.
        if ($sLink = Phpfox::getService('newsletter')->checkPending()) {
            $this->template()->assign(array(
                    'sError' => $sLink
                )
            );
        }
        $aNewsletters = Phpfox::getService('newsletter')->get();
        foreach ($aNewsletters as &$aNewsletter) {
            $obj = storage()->get('CORE_NEWSLETTER_TOTAL_USERS_SENT_' . $aNewsletter['newsletter_id']);
            if ($obj) {
                $aNewsletter['total_sent'] = (int)$obj->value;
            } elseif ($aNewsletter['state'] == CORE_NEWSLETTER_STATUS_COMPLETED) {
                $aNewsletter['total_sent'] = $aNewsletter['total_users'];
            } else {
                $aNewsletter['total_sent'] = 0;
            }
        }

        $this->template()
            ->setTitle(_p('newsletter'))
            ->setBreadCrumb(_p('manage_newsletters'), null, true)
            ->assign(array(
                'aNewsletters' => $aNewsletters
            ));
    }
}

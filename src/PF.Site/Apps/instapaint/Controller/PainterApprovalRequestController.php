<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class PainterApprovalRequestController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        // Allow access to unapproved painters only
        $securityService->allowAccess([
            $securityService::PAINTER_GROUP_ID
        ]);

        // Get instance of Painter service class
        $painterService = \Phpfox::getService('instapaint.painter');

        // The form values
        $vals = $_POST['val'];

        if ($vals) { // Form was sent

            // If security token is not valid, show CSRF error message:
            if (!$securityService->checkCSRFToken($vals['token'])) {
                $this->template()->assign([
                    'csrfError' => true
                ]);
                return;
            }

            if (\Phpfox_Error::isPassed()) {
                if ($painterService->requestApproval()) {
                    // Save promotional permissions
                    if ($vals['share-permission']) {
                        $painterService->addSharePermission(user()->id);
                    }

                    \Phpfox::getService('notification.process')->add('instapaint_PainterApprovalRequestSent', 0, user()->id, user()->id, true);
                    $this->url()->send('painter-dashboard',null,'Your approval request was sent successfully');
                } else {
                    $this->url()->send('painter-dashboard',null,'There was an error submitting your approval request', null, 'danger');
                }
            }
        } else {
            $this->url()->send('painter-dashboard');
        }
    }
}

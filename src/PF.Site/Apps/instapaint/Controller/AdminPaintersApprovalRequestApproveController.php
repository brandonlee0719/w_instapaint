<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

class AdminPaintersApprovalRequestApproveController extends \Phpfox_Component
{
    public function process()
    {
        // Require user to be logged in:
        \Phpfox::isUser(true);

        // Get instance of Instapaint Security service class
        $securityService = \Phpfox::getService('instapaint.security');

        // Allow access to admins only
        $securityService->allowAccess([$securityService::ADMIN_GROUP_ID]);

        //Get instance of Painter service class
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

            if (empty($vals['approval_request_id'])) {
                \Phpfox_Error::set(_p('Request Id is required'));
            }

            if (\Phpfox_Error::isPassed()) {
                // Try to get approval request form DB
                $approvalRequest = $painterService->getApprovalRequestById($vals['approval_request_id']);

                if ($approvalRequest) {
                    if ($painterService->approve($approvalRequest)) {
                        \Phpfox::getService('notification.process')->add('instapaint_PainterApprovalRequestApproved', 0, $approvalRequest['user_id'], $approvalRequest['user_id'], true);
                        $this->url()->send('admin-dashboard/painters/approval-requests',null,'Painter was approved successfully');
                    } else {
                        $this->url()->send('admin-dashboard/painters/approval-requests',null,'There was an error approving painter', null, 'danger');
                    }

                } else {
                    // If no approval request found, show error in template
                    $this->template()->assign([
                        'error' => 'The approval request you are trying to approve does not exist.'
                    ]);
                }

            }
        } else {
            // If no form is sent, simply redirect to approval requests list
            $this->url()->send('admin-dashboard/painters/approval-requests');
        }
    }
}

<?php

namespace Apps\Instapaint\Service;

use Phpfox;

class Callback extends \Phpfox_Service
{
    public function getUploadParams()
    {
        return array(
            'max_size' => 30,
            'max_file' => 1,
            'type_list' => ['jpg', 'jpeg', 'png'],
            'type_list_string' => 'image/jpeg,image/png',
            'thumbnail_sizes' => array(50, 240, 500),
            'label' => null,
            'is_required' => true,
            'submit_button' => 'submit-button',
            'type_description' => 'You can upload a JPG or PNG file.',
            'max_size_description' => 'The file size limit is 30 MB. If your upload does not work, try uploading a smaller picture.',
            'upload_dir' => Phpfox::getParam('photo.dir_photo'),
            'upload_path' => Phpfox::getParam('photo.dir_photo'),
            'js_events' => [
                'success' => '$Core.Instapaint.dropzoneOnSuccess',
                'error' => '$Core.Instapaint.dropzoneOnError',
                'removedfile' => '$Core.Instapaint.dropzoneOnRemovedFile'
            ]
        );
    }

    /* Client Notifications */

    public function getNotificationClientWelcome($aNotification) {
        return [
            'link'    => '/client-dashboard/',
            'message' => 'Thank you for joining InstaPaint! Now you can turn any photo into an awesome oil painting!',
            'icon'    => ''
        ];
    }

    public function getNotificationClientOrderPayed($aNotification) {
        return [
            'link'    => "/client-dashboard/order/{$aNotification['item_id']}/",
            'message' => "Your order with number #{$aNotification['item_id']} was successfully paid. We will start painting your photo as soon as possible!",
            'icon'    => ''
        ];
    }

    public function getNotificationClientOrderShipped($aNotification) {
        return [
            'link'    => "/client-dashboard/order/{$aNotification['item_id']}/",
            'message' => "Your order with number #{$aNotification['item_id']} has been shipped! Click here to see the shipping notes",
            'icon'    => ''
        ];
    }

    /* Painter Notifications */

    public function getNotificationPainterWelcome($aNotification) {
        return [
            'link'    => '/painter-dashboard/',
            'message' => 'Welcome to InstaPaint! To become a Verified Painter please complete your profile and submit an approval request!',
            'icon'    => ''
        ];
    }

    public function getNotificationPainterApprovalRequestSent($aNotification) {
        return [
            'link'    => '/painter-dashboard/',
            'message' => 'We received your approval request! An administrator will verify your profile as soon as possible!',
            'icon'    => ''
        ];
    }

    public function getNotificationPainterApprovalRequestApproved($aNotification) {
        return [
            'link'    => '/painter-dashboard/',
            'message' => 'Congratulations! Now you are an Approved Painter and you can start taking orders from your dashboard',
            'icon'    => ''
        ];
    }

    public function getNotificationPainterOrderApprovedForShipping($aNotification) {
        return [
            'link'    => "/painter-dashboard/orders/#orders-approved-for-shipping-section",
            'message' => "Congratulations! The order with number #{$aNotification['item_id']} has been approved for shipping",
            'icon'    => ''
        ];
    }
}

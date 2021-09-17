<?php

/*
 * This plugin moves each new user to either one of two groups: client, or painter
 * depending on the option they choose in the registration form.
 */

// Get instance of Instapaint Security service class
$securityService = \Phpfox::getService('instapaint.security');
$eventsService = \Phpfox::getService('instapaint.events');

// Id of selected option for the "User Type" field
$selectedUserTypeOptionId = $aCustom[$securityService::CUSTOM_FIELDS['user_type']['id']];

# If user type is client move user to client group, if it's painter move user to painter group
if ($selectedUserTypeOptionId == $securityService::CUSTOM_FIELDS['user_type']['options']['client']['id']) {
    db()->update(':user', ['user_group_id' => $securityService::CLIENT_GROUP_ID], ['user_id' => $iId]);
    \Phpfox::getService('notification.process')->add('instapaint_ClientWelcome', 0, $iId, $iId, true);
} elseif ($selectedUserTypeOptionId == $securityService::CUSTOM_FIELDS['user_type']['options']['painter']['id']) {
    \Phpfox::getService('notification.process')->add('instapaint_PainterWelcome', 0, $iId, $iId, true);
    db()->update(':user', ['user_group_id' => $securityService::PAINTER_GROUP_ID], ['user_id' => $iId]);
}

# Friend new user with administrators
$securityService->friendWithAdmins($iId);

# If partial order, assign user:
if(isset($_GET['partial_order_id']) && $_GET['partial_order_id']) {
    \Phpfox::getService('instapaint.packages')->partialOrderSetUser($iId, $_GET['partial_order_id']);
}

# Create CRM record
$eventsService->createSubscriber($iId);
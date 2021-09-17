<?php

/*
 * This plugin runs after a user updates their details
 */

$eventsService = \Phpfox::getService('instapaint.events');

# Update CRM record
$eventsService->updateSubscriber($iUserId, $aVals);
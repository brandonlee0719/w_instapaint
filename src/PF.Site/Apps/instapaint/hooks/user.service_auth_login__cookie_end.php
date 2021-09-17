<?php

/*
 * This plugin runs after a user logs in
 */

$eventsService = \Phpfox::getService('instapaint.events');

# Update CRM record
$eventsService->updateSubscriber($aRow['user_id'], $aRow);
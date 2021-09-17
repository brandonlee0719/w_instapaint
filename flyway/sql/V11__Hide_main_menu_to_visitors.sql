# Hide the main menu if a user is not logged into the site
UPDATE `phpfox_setting`
SET `value_actual` = 1
WHERE `setting_id` = 315;

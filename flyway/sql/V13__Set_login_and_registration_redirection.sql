# Set route for redirect_after_login and redirect_after_signup
# to 'my-dashboard', which will in turn redirect to the corresponding
# dashboard for the current user.

UPDATE `phpfox_setting`
SET `value_actual` = 'my-dashboard'
WHERE `setting_id` IN (264, 280);

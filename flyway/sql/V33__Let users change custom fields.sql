# Let users change custom fields again since we have hidden the user type field
# This way they can write their "about me" field
DELETE FROM phpfox_user_setting
WHERE setting_id = 36;

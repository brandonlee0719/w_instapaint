# Don't restrict messages to friends
UPDATE phpfox_user_group_setting
SET default_user = 'false'
WHERE name = 'restrict_message_to_friends';

# Everyone can see each other's profile and they can click the "Message" button
UPDATE phpfox_setting
SET value_actual = 0
WHERE var_name = 'friends_only_community' OR var_name = 'friends_only_profile';

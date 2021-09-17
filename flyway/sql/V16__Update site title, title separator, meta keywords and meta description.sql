# Update site title
UPDATE `phpfox_setting`
SET `value_actual` = 'Instapaint'
WHERE `setting_id` = 85;

# Update title separator
UPDATE `phpfox_setting`
SET `value_actual` = '-'
WHERE `setting_id` = 29;

# Update meta keywords and meta description
UPDATE `phpfox_setting`
SET `value_actual` = 'Instapaint - Turn any photo into an oil painting'
WHERE `setting_id` BETWEEN 34 AND 35;

# Update logo
UPDATE `phpfox_cache`
SET `cache_data` = '"9cda9e384b9400f3c12256aaa6a81e45.png"'
WHERE `file_name` = 'flavor/logos/material';

# Set favicon
INSERT INTO `phpfox_cache` (`file_name`, `cache_data`, `data_size`, `time_stamp`)
VALUES ('flavor/favicons/material', '"dcf96b6cf9cafc48ee86853943e0fa98.ico"', 0, 1);

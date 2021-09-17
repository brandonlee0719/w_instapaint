# Create table to keep painter's daily jobs limit
# We'll use painter_user_id = 0 for the default value

CREATE TABLE `phpfox_instapaint_painter_daily_jobs_limit` (
  `painter_user_id` int(10) unsigned DEFAULT NULL,
  `daily_limit` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `painter_user_id` (`painter_user_id`)
);

INSERT INTO phpfox_instapaint_painter_daily_jobs_limit
SET painter_user_id = 0, daily_limit = 3;

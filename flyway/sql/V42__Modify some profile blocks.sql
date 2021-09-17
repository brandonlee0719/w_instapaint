# Remove feed from profile
UPDATE phpfox_block
SET is_active = 0
WHERE block_id = 10;

# Remove friends block from profile
UPDATE phpfox_block
SET is_active = 0
WHERE block_id = 14;

# Make block of recent photos in profile bigger
UPDATE phpfox_block
SET is_active = 1, location = 2
WHERE block_id = 31;

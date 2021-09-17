# Set all ordering values to 100
update phpfox_country
set ordering = 100;

# Move US to the top
update phpfox_country
set ordering = 0
WHERE country_iso = 'US';
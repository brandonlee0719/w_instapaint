# Set dashboard icon to null (the template will handle it)
UPDATE phpfox_menu
SET mobile_icon = NULL
WHERE menu_id = 40;


# Disable Home, Members and Photos menus
UPDATE phpfox_menu
SET is_active = 0
WHERE menu_id IN (3, 28, 39);

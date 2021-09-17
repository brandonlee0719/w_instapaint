UPDATE phpfox_setting
SET value_actual = 'InstaPaint - Simply upload your photo, select your options, and professional artists will hand paint and ship your painting to you! We employ professional artists in art galleries to paint your custom piece, stroke by stroke. Order now.'
WHERE phrase_var_name = 'setting_description';

UPDATE phpfox_setting
SET value_actual = 'Simply upload your photo, select your options, and professional artists will hand paint and ship your painting to you! We employ professional artists in art galleries to paint your custom piece, stroke by stroke. Order now.'
WHERE phrase_var_name = 'setting_meta_description_profile';

UPDATE phpfox_setting
SET value_actual = 'instapaint, painting, insta-paint, photo to painting, canvas print, print photo'
WHERE phrase_var_name = 'setting_keywords';

UPDATE phpfox_setting
SET value_actual = 'InstaPaint'
WHERE phrase_var_name = 'setting_site_title' OR phrase_var_name = 'setting_global_site_title';

UPDATE phpfox_setting
SET value_actual = 'InstaPaint Â©'
WHERE phrase_var_name = 'setting_site_copyright';

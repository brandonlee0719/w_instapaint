# Update email welcome message

UPDATE `phpfox_language_phrase`
SET `text` = 'Thank you for joining Instapaint!\n\nNow you can start converting any photo into a Professional Oil Painting.\n\nOur artists are excited to create absolutely stunning works of art that will WOW your friends and colleagues.\n\nVisit <a href=\'http://instapaint.com\'>instapaint.com</a> to get started!', `text_default` = 'Thank you for joining Instapaint!\n\nNow you can start converting any photo into a Professional Oil Painting.\n\nOur team of certified professional artists will create absolutely stunning works of art that will WOW your friends and colleagues.\n\nVisit <a href=\'http://instapaint.com\'>instapaint.com</a> to get started!'
WHERE `phrase_id` = 1125 AND `language_id` = 'en';
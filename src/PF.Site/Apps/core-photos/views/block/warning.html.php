<?php
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{_p var='the_photo_you_are_about_to_view_may_contain_nudity_sexual_themes_violence_gore_strong_language_or_ideologically_sensitive_subject_matter'}
<p>
	{_p var='would_you_like_to_view_this_image'}
</p>
<ul class="action">
	<li><a href="{$sLink}">{_p var='yes'}</a></li>
	<li><a href="#" onclick="tb_remove(); return false;">{_p var='no_thanks'}</a></li>
</ul>
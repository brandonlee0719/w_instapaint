<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="message" style="margin-top:5px; font-weight:normal;">
	<div class="error_message">
		{_p var='username_is_not_available_here_are_other_suggestions_you_may_like'}
	</div>
	<ul class="action">
	{foreach from=$aNames name=sUser item=sItem}
		<li><a href="#" id="js_suggested_name" onclick="$Core.registration.useSuggested(this); return false;">{$sItem}</a></li>
	{/foreach}
	</ul>
</div>
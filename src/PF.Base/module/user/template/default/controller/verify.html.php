<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($sTime)}
	<div>
	{_p var='the_link_that_brought_you_here_is_not_valid_it_may_already_have_expired' time=$sTime}
	</div>
{/if}

{if !isset($sTime)}
	<div>
		{_p var='this_site_is_very_concerned_about_security'}
	</div>
	<div>
		<input type="button" value="{_p var='resend_verification_email'}" class="button btn-primary" onclick="$.ajaxCall('user.verifySendEmail', 'iUser={$iVerifyUserId}'); return false;" />
	</div>
{/if}
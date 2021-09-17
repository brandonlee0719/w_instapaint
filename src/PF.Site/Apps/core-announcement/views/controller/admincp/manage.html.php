<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aAnnouncements)}
<div id="js_announcements">
	{module name='announcement.manage' sLanguage=$sDefaultLanguage aAnnouncements=$aAnnouncements}
</div>
{else}
<div class="alert alert-empty">
    {_p var='no_announcements_have_been_created'}
</div>
{/if}
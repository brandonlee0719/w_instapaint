<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !Phpfox::getService('profile')->timeline()}
<div class="go_left t_center" style="width:125px;">
	{img user=$aUser suffix='_120' max_width='120' max_height='120'}
</div>
<div style="margin-left:125px;">
{/if}
	<div class="extra_info">
		{_p var='profile_is_private'}
	</div>

{if !Phpfox::getService('profile')->timeline()}
</div>
<div class="clear"></div>
{/if}
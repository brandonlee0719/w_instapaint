<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="admincp_apps_holder">
	<section>
		<div class="themes">
		{foreach from=$themes item=theme}
			<article {if ($theme.is_default)} id="is-default"{/if} {$theme.image}>
				<h1>
					<a class= href="{url link='admincp.theme.manage' id=$theme.theme_id}">
						<span>{$theme.name|clean}</span>
						<em>{_p var='edit'}</em>
					</a>
					<a class="touch_screen" href="{url link='admincp.theme.manage' id=$theme.theme_id}">
					</a>
				</h1>
			</article>
		{/foreach}
		</div>
	</section>

	<section class="preview">
		<h1>{_p var='featured_themes'}</h1>
		<div class="phpfox_store_featured" data-type="themes" data-parent="{url link='admincp.store' load='themes'}"></div>
	</section>
</div>
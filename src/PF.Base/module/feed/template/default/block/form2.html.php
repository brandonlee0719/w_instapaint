<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<span class="user_block_toggle"><i class="fa"></i></span>
<div class="user_block">
	<div class="content">
		<div class="feed_form_user">
			{img user=$aGlobalUser suffix='_50_square'}
			<div class="feed_form_user_info">
				{$aGlobalUser|user}
				<div>
					<a href="{url link='profile'}">{_p var='view_profile'}</a>
				</div>
				<span class="feed_form_toggle"><i class="fa fa-toggle-down"></i></span>
			</div>
		</div>

		{if $bShowMenu}
		<div class="feed_form_menu">
			<nav class="nav_header">
				<ul>
					<li><a href="{url link='user.setting'}"><i class="fa fa-cog"></i>{_p var='account_settings'}</a></li>
					<li><a href="{url link='user.profile'}"><i class="fa fa-edit"></i>{_p var='edit_profile'}</a></li>
					<li><a href="{url link='friend'}" class="no_ajax"><i class="fa fa-group"></i>{_p var='manage_friends'}</a></li>
					<li>
						<a href="{url link='user.privacy'}"><i class="fa fa-shield"></i>{_p var='privacy_settings'}</a>
					</li>
					<li>
						<a href="{url link='user.logout'}" class="no_ajax logout"><i class="fa fa-toggle-off"></i>{_p var='logout'}</a>
					</li>
				</ul>
			</nav>
		</div>
		{/if}
		<a href="#" class="_panel _load_is_feed" data-open="{url link='feed.form'}" data-class="is_feed">{_p var='what_s_up'}</a>

	</div>
</div>
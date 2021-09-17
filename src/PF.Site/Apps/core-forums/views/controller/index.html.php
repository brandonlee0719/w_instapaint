<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !PHPFOX_IS_AJAX}
{template file='forum.block.search'}
{if Phpfox::isUser()}
<div class="forum_quick_link_wrapper">
	<div class="dropdown pull-right forum_quick_link">
		<a href="#" class="btn-link" data-toggle="dropdown">{_p var='quick_links'} <i class="fa fa-caret-down"></i></a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li><a href="{url link='forum.read'}">{_p var='mark_forums_read'}</a></li>
			<li><a href="{url link='forum.search' view='new'}">{_p var='new_posts'}</a></li>
			<li><a href="{url link='forum.search' view='my-thread'}">{_p var='my_threads'}</a></li>
			<li><a href="{url link='forum.search' view='subscribed'}">{_p var='subscribed_threads'}</a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>
{/if}
{/if}

{if !count($aForums)}
<div class="extra_info">
	{_p var='no_forums_have_been_created'}
	{if Phpfox::getUserParam('forum.can_add_new_forum')}
	<ul class="action">
		<li><a href="{url link='admincp.forum.add'}" class="no_ajax">{_p var='create_a_new_forum'}</a></li>
	</ul>
	{/if}
</div>
{else}
	{template file='forum.block.entry'}
{/if}
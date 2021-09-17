<div class="forum-app feed">
	<h4 class="fw-bold forum-title"><a href="{$aThread.link}">{$aThread.title|clean}</a></h4>
	<ul class="forum-feed-breakbrum clearfix">
        {foreach from=$aThread.breadcrumb item=aForum}
		<li class="text-capitalize"><a href="{$aForum.1}">{$aForum.0}</a></li>
        {/foreach}
	</ul>
	<div class="forum-description item_view_content">{$aThread.text|stripbb|feed_strip|split:55|max_line}</div>
</div>
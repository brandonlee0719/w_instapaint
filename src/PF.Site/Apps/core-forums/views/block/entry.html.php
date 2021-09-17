<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

{if $aCallback === null && count($aForums)}
	{if isset($isSubForumList)}
	<div class="content">
		{/if}
		<div class="item-container forum-app forum-main level-1">
			{foreach from=$aForums name=forums item=aForum}
				{if $aForum.is_category}
					<article class="forum_holder{if isset($aForum.toggle_class)} {$aForum.toggle_class}{/if} item-category" data-forum-id="{$aForum.forum_id}">
						<div class="item-outer pt-2 pb-2">
							<div class="item-title">
								<h4>
									<i class="ico mr-1 text-transition item-category"></i>
									<a href="{permalink module='forum' id=$aForum.forum_id title=$aForum.name}"{if !empty($aForum.description)} title="{softPhrase var=$aForum.description}" {/if} class="fw-bold forum-text-overflow">{softPhrase var=$aForum.name}</a>
								</h4>
                                {if count($aForum.sub_forum)}
								<span class="toggle">
									<i class="ico ico-angle-down"></i>
								</span>
                                {/if}
							</div>
							{if !empty($aForum.description)}
							<div class="item-description mt-1">
								<h6 class="text-transition">
                                    {softPhrase var=$aForum.description}
								</h6>
							</div>
							{/if}
							<div class="item-container forum-app forum-main level-2">
								{foreach from=$aForum.sub_forum item=aForum}
									{template file='forum.block.forum'}
								{/foreach}
							</div>
						</div>
					</article>
				{else}
	                {template file='forum.block.forum'}
				{/if}
			{/foreach}
		</div>
		{if isset($isSubForumList)}
	</div>
	{/if}
{/if}
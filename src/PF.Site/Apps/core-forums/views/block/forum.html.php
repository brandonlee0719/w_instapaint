<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<article itemscope itemtype="http://schema.org/Thing" class="item-forum">
	<div class="item-outer pt-2 pb-2">
		<div class="item-inner">
			<div class="item-title">
					<h5 itemprop="name">
						<a href="{permalink module='forum' id=$aForum.forum_id title=$aForum.name}"{if !empty($aForum.description)} title="{softPhrase var=$aForum.description}" {/if} class="forum-text-overflow fw-bold" itemprop="url">{if $aForum.is_category}<i class="ico mr-1 text-transition item-category"></i>{else}<i class="ico mr-1 item-forum"></i>{/if}{softPhrase var=$aForum.name}</a>
                    </h5>
					{if !empty($aForum.description)}
						<h6 class="item-description text-transition">
                            {softPhrase var=$aForum.description}
                        </h6>
					{/if}
			</div>
			<div class="item-stastistic">
				<ul class="item-stastistic-inner">
					<li class="text-center"><strong>{$aForum.total_thread|short_number}</strong><span>{_p var='threads'}</span></li>
					<li class="text-center"><strong>{$aForum.total_post|short_number}</strong><span>{_p var='posts'}</span></li>
				</ul>
			</div>
		</div>
	</div>
</article>
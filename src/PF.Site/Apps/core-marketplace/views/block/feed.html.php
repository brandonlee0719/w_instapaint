<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

<div class="item-container market-app feed">
	<article>
			<div class="item-outer flex">
				<div class="item-media">
					<a href="{$aListing.url}" target="_blank" style="background-image: url(
                        {if !empty($aListing.image_path)}
                            {img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path  suffix='_200_square' return_url=true}
                        {else}
                            {param var='marketplace.marketplace_default_photo'}
                        {/if}
					)"></a>
				</div>
				<div class="item-inner overflow">
					<a href="{$aListing.url}" class="item-title">{$aListing.title|clean|shorten:100:'...'|split:25}</a>
					<div class="item-price">
						{if $aListing.price == '0.00'}
							{_p var='free'}
						{else}
							{$aListing.currency_id|currency_symbol}{$aListing.price|number_format:2}
						{/if}
					</div>
					<div class="item-category"><span>{if !empty($aListing.city)} {$aListing.city|clean}, {/if}{if !empty($aListing.country_child_id)} {$aListing.country_child_id|location_child}, {/if}{$aListing.country_iso|location}</span>&nbsp;.&nbsp;<span>{_p var='category'}: {$aListing.categories|category_links|shorten:64:'...'}</span>
					</div>
					<div class="item-description item_view_content">{$aListing.mini_description|stripbb|feed_strip|split:55|max_line|shorten:100}</div>
				</div>
		</div>
	</article>
</div>
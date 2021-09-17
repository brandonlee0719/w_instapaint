<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="market-app detail-extra-info">
	{if is_array($aListing.categories) && count($aListing.categories)}
	<div class="item-location">
		<div class="item-label text-uppercase mb-1">{_p var="location"}</div>
        {if !empty($aListing.city)} {$aListing.city|clean} &raquo; {/if}
        {if !empty($aListing.country_child_id)} {$aListing.country_child_id|location_child} &raquo; {/if}
        {$aListing.country_iso|location}
	</div>
    {if is_array($aListing.categories) && count($aListing.categories)}
    {/if}
    {if !empty($aListing.mini_description) || !empty($aListing.description)}
        <div class="item-description item_view_content">
            <div class="item-label text-uppercase mb-1">{_p var='description'}</div>
            <div class="item-short fw-bold">{$aListing.mini_description}</div>
            <div class="item-full mt-2" itemprop="description">
                {$aListing.description|parse|shorten:200:'feed.view_more':true|split:70|max_line}
                {if $aListing.total_attachment}
                    {module name='attachment.list' sType=marketplace iItemId=$aListing.listing_id}
                {/if}
            </div>
        </div>
    {/if}
	{/if}
</div>
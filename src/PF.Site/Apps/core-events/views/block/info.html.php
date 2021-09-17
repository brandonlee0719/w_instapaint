<?php 
/**
 * [PHPFOX_HEADER]
 *
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<div class="event-detail-view">
	<div class="event-detail-top">
		<div class="event-info">
	        <div class="event-info-image">{img user=$aEvent suffix='_50_square'}</div>
	        <div class="event-info-main">
	            <span class="event-author">{_p var='by'} {$aEvent|user}</span>
	            <span class="event-time">{_p var='on'} {$aEvent.time_stamp|convert_time:'core.global_update_time'}</span>
	        </div>
	    </div>
	    <div class="js_event_rsvp item-choice">
		    {module name='event.rsvp'}
		</div>
	</div>
	
	{if $aEvent.hasPermission}
	<div class="item_bar event-button-option">
        <div class="item_bar_action_holder">
            <a href="#" class="item_bar_action" data-toggle="dropdown" role="button"><span>{_p('Actions')}</span><i class="ico ico-gear-o"></i></a>
            <ul class="dropdown-menu dropdown-menu-right">
                {template file='event.block.menu'}
            </ul>
        </div>
    </div>
	{/if}
	<div class="event-detail-main">
		{if $aEvent.view_id == 1}
            {template file='core.block.pending-item-action'}
		{/if}
		<div class="item-location-info">
			<div class="item-map-img">
				{if isset($aEvent.map_location) && !empty($sExtraParam)}
				<div class="item-map" style="background-image: url(//maps.googleapis.com/maps/api/staticmap?center={$aEvent.map_location}&amp;zoom=16&amp;size=600x200&amp;sensor=false&amp;maptype=roadmap{$sExtraParam});">
                        <div style="margin-left:-8px; margin-top:-8px; position:absolute; background:#fff; border:8px blue solid; width:12px; height:12px; left:50%; top:50%; z-index:1; overflow:hidden; text-indent:-1000px; border-radius:12px;">Marker</div>
                        <a href="//maps.google.com/?q={$aEvent.map_location}" target="_blank" title="{_p var='view_this_on_google_maps'}"></a>
				</div>
				{/if}
			</div>
			<div class="item-map-info">
				<div class="item-location">
					<span class="ico ico-checkin-o"></span>
                    <div class="item-info">
                    	<div class="item-info-1">{$aEvent.location|clean|split:60}</div>
                    	<div class="item-info-2">
								{if !empty($aEvent.address)}
								<div class="item-address" itemprop="streetAddress">{$aEvent.address|clean}</div>
								{/if}
								{if !empty($aEvent.city)}
								<div class="item-city" itemprop="addressLocality">{$aEvent.city|clean}</div>
								{/if}
								{if !empty($aEvent.postal_code)}
								<div class="item-postal-code" itemprop="postalCode">{$aEvent.postal_code|clean}</div>
								{/if}
								{if !empty($aEvent.country_child_id)}
								<div class="item-region" itemprop="addressRegion">{$aEvent.country_child_id|location_child}</div>
								{/if}
								<div class="item-country" itemprop="addressCountry">{$aEvent.country_iso|location}</div>
					
                    	</div>
                    </div>
				</div>
				<div class="item-time">
					<span class="ico ico-clock-o"></span>
					<div class="item-info">
						<div class="item-info-1">{$aEvent.event_date}</div>
					</div>
				</div>
				{if isset($aEvent.map_location)}
				<div class="item-map-viewmore">
					<a href="//maps.google.com/?q={$aEvent.map_location}" target="_blank">{_p var='view_on_google_maps'}</a>
				</div>
				{/if}
			</div>
		</div>
		<div class="item-banner image_load" data-src="{if $aEvent.image_path}{img server_id=$aEvent.server_id title=$aEvent.title path='event.url_image' file=$aEvent.image_path suffix='' return_url=true}{else}{param var='event.event_default_photo'}{/if}"></div>
		<div class="event-item-content item_view_content">
			{$aEvent.description|parse|shorten:200:'feed.view_more':true|split:55|max_line}
            {if $aEvent.total_attachment}
                {module name='attachment.list' sType=event iItemId=$aEvent.event_id}
            {/if}
		</div>
		<div id="js_guests_list_detail" class="item-member-attending">
            {template file='event.block.guests-list-tab'}
        </div>
        {if is_array($aEvent.categories) && count($aEvent.categories)}
        <div class="item-category">
        	<span class="item-category-title">{_p var='category'}</span>
        	{$aEvent.categories|category_display}
        </div>
        {/if}
        {addthis url=$aEvent.bookmark title=$aEvent.title description=$sShareDescription}
        <div class="event-detail-feedcomment item-detail-feedcomment">
            {module name='feed.comment'}
        </div>
        {unset var=$sFeedType}
	</div>
</div>
<div class="marvic_separator clearfix"></div>
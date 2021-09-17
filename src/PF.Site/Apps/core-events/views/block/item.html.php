<article class="event-item" data-url="" data-uid="{$aEvent.event_id}" id="js_event_item_holder_{$aEvent.event_id}">
    <div class="item-outer">
        <div class="{if $bShowModerator} moderation_row{/if}">
            {if !empty($bShowModerator)}
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aEvent.event_id}" id="check{$aEvent.event_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            {/if}
        </div>
        <div class="item-icon">
            {if isset($sView) && $sView == 'my' && (isset($aEvent.view_id) && $aEvent.view_id == 1)}
            <div class="sticky-label-icon sticky-pending-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-clock-o"></i>
            </div>
            {/if}
            <!-- Sponsor -->
            {if $aEvent.is_sponsor}
            <div class="sticky-label-icon sticky-sponsored-icon">
                <span class="flag-style-arrow"></span>
               <i class="ico ico-sponsor"></i>
            </div>
            {/if}
            {if $aEvent.is_featured}
            <!-- Featured -->
            <div class="sticky-label-icon sticky-featured-icon">
                <span class="flag-style-arrow"></span>
               <i class="ico ico-diamond"></i>
            </div>
            {/if}
        </div>
        <!-- image -->
        <a class="item-media" href="{$aEvent.url}">
            <span style="background-image: url({if $aEvent.image_path}{img server_id=$aEvent.server_id title=$aEvent.title path='event.url_image' file=$aEvent.image_path suffix='' return_url=true}{else}{param var='event.event_default_photo'}{/if})"  alt="{$aEvent.title}"></span>
        </a>

        <div class="item-inner">
            <div class="item-top-info">
                <div class="item-title">
                    <a href="{$aEvent.url}" class="link" itemprop="url">{$aEvent.title|clean}</a>
                </div> 
                <div class="item-statistic">
                        <span class="item-count-like">{$aEvent.total_like|short_number} {if $aEvent.total_like == 1}{_p var='like_lowercase'}{else}{_p var='likes_lowercase'}{/if}</span>
                        <span class="item-count-view">{$aEvent.total_view|short_number} {if $aEvent.total_view == 1}{_p var='view__l'}{else}{_p var='views_lowercase'}{/if}</span>
                </div>
            </div>
            <div class="item-main-info">
                <div class="item-date-month" title="{$aEvent.start_time|convert_time:'event.event_basic_information_time'}">
                    <div class="item-date">{$aEvent.start_time_short_day}</div>
                    <div class="item-month">{$aEvent.start_time_month}</div>  
                    <div class="item-hour">{$aEvent.start_time_phrase_stamp}</div>
                </div>
                <div class="item-info-detail">  
                    
                    <!-- author -->
                    <div class="item-author">
                        <span class="item-user">{_p var='by'} {$aEvent|user} </span>
                    </div>
                    <!-- location -->
                    <div class="item-location"> 
                    <span class="ico ico-checkin-o"></span>
                        <span class="item-info">{$aEvent.location}</span>
                    </div>
                    <div class="item-time">
                        <span class="ico ico-sandclock-end-o"></span>
                        <span class="item-info" title="{$aEvent.end_time_phrase_stamp} - {$aEvent.end_time_micro}">{$aEvent.end_time_phrase_stamp} - {$aEvent.end_time_micro}</span>
                    </div>
                </div>
            </div>
            {if $aEvent.hasPermission}
            <div class="item-option">
                <div class="dropdown">
                    <a type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                        <i class="ico ico-gear-o"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        {template file='event.block.menu'}
                    </ul>
                </div>
            </div>
            {/if}
            {if isset($sView) && $sView == 'invites'}
                <div class="item-bottom-info">
                    {template file='event.block.rsvp-action'}
                    <div class="item-count-attending">
                        <span>{if isset($aEvent.total_attending) && $aEvent.total_attending > 0} {$aEvent.total_attending} {_p var='attending'}{/if}</span>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</article>
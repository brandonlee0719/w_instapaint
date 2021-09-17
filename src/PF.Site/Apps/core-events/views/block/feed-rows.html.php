<article class="event-item" data-url="" data-uid="{$aEvent.event_id}" id="js_event_item_holder_{$aEvent.event_id}">
    <div class="item-outer">

        <!-- image -->
        <a class="item-media" href="{$aEvent.url}">
            <span style="background-image: url({if $aEvent.image_path}{img server_id=$aEvent.server_id title=$aEvent.title path='event.url_image' file=$aEvent.image_path suffix='' return_url=true}{else}{param var='event.event_default_photo'}{/if})"  alt="{$aEvent.title}"></span>
        </a>

        <div class="item-inner">
            <!-- title -->

            <div class="item-title">
                {if isset($sView) && $sView == 'my'}
                    {if (isset($aEvent.view_id) && $aEvent.view_id == 1)}
                        <span class="pending-label">{_p('pending_label')}</span>
                    {/if}
                {/if}
                <a href="{$aEvent.url}" class="link" itemprop="url">{$aEvent.title|clean}</a>
            </div>

            <!-- location -->
            <div class="item-location">
            <span class="ico ico-checkin-o"></span>
                <span class="item-info">{$aEvent.location}</span>
            </div>
            
            <div class="item-time-date">
                <span class="ico ico-calendar-o"></span>
                <span class="item-info">{$aEvent.start_time_micro} - {$aEvent.end_time_micro}</span>
            </div>

            <div class="item-time-hour"> 
                <span class="ico ico-clock-o"></span>
                <span class="item-info">{$aEvent.start_time_phrase_stamp} - {$aEvent.end_time_phrase_stamp}</span>
            </div>       
        </div>
    </div>
</article>
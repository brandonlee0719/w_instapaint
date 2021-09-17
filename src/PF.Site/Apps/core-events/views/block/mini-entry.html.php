<?php
defined('PHPFOX') or exit('NO DICE!');
?>

    <div class="event-mini-block-item">
        <div class="item-outer">
            <!-- image -->
            <a class="item-media" href="{permalink module='event' id=$aEvent.event_id title=$aEvent.title}">
                <span style="background-image: url({if $aEvent.image_path}{img server_id=$aEvent.server_id title=$aEvent.title path='event.url_image' file=$aEvent.image_path suffix='' return_url=true}{else}{param var='event.event_default_photo'}{/if})"  alt="{$aEvent.title}"></span>
            </a>  
            <div class="item-inner">
               <div class="item-title">
                    {if isset($sView) && $sView == 'my'}
                        {if (isset($aEvent.view_id) && $aEvent.view_id == 1)}
                            <span class="pending-label">{_p('pending_label')}</span>
                        {/if}
                    {/if}
                    <a href="{permalink module='event' id=$aEvent.event_id title=$aEvent.title}" class="link" itemprop="url">{$aEvent.title|clean}</a>
                </div> 
                <div class="item-info">
                    <div class="item-time"> 
                        <span class="item-hour">{$aEvent.start_time_phrase_stamp} - {$aEvent.start_time_micro}</span>
                    </div>
                    <div class="item-location"> 
                        {$aEvent.location}
                    </div>
                </div>
                <div class="item-action">  
                    {if isset($bIsInviteBlock) && $bIsInviteBlock}
                        {template file='event.block.rsvp-action'}
                    {/if}
                    {if !empty($aEvent.total_view)}
                    <div class="item-view-count">
                        {$aEvent.total_view|short_number} {if $aEvent.total_view == 1}{_p var='view__l'}{else}{_p var='views_lowercase'}{/if}
                    </div>
                    {/if}
                </div>
            </div> 
        </div>   
    </div>


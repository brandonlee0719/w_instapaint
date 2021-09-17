<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($aItem.duration)}
<meta itemprop="duration" content="{$aItem.duration}" />
{/if}

<div class="video-view ">
    <div class="video-info">
        <div class="video-info-image">{img user=$aItem suffix='_50_square'}</div>
        <div class="video-info-main">
            <span class="video-author">{_p var='by_user' full_name=$aItem|user:'':'':50:'':'author'}</span>
            <span class="video-time">{_p var='on'} {$aItem.time_stamp|convert_time:'core.global_update_time'}</span>
        </div>
    </div>
    
    <div class="video-total-viewlike item-comment">
        {if $aItem.view_id == 0}
            <div class="video-mini-feed">
                {module name='feed.mini-feed-action'}
            </div>
        {/if}
        <span class="video-total-view item-total-view"><span class="video-view-number video-number">{$aItem.total_view}</span> {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}</span>
    </div>
    {if $aItem.hasPermission}
    <div class="item_bar video-button-option">
        <div class="item_bar_action_holder">
            <a href="#" class="item_bar_action" data-toggle="dropdown" role="button"><span>{_p('Actions')}</span><i class="ico ico-gear-o"></i></a>
            <ul class="dropdown-menu dropdown-menu-right">
                {template file='v.block.menu'}
            </ul>
        </div>
    </div>
    {/if}

    {if $aItem.in_process > 0}
        <div class="alert alert-info">
            {_p('video_is_being_processed')}
        </div>
    {else}
        {if $aItem.view_id == 2}
            {template file='core.block.pending-item-action'}
        {/if}
    {/if}

    <div class="t_center pf_video_wrapper {if $aItem.is_stream && empty($aItem.is_facebook_embed)}pf_video_wrapper_iframe{/if} {if $aItem.destination}pf_video_wrapper_uploader{/if}">
        {$aItem.embed_code}
        {if PHPFOX_IS_AJAX_PAGE}
            <span class="_a_back"><i class="ico ico-arrow-left"></i>{_p var='back'}</span>
        {/if}
    </div>
    <div class="addthis_share pf_video_addthis">
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={$sAddThisPubId}" data-title="{$aItem.title|clean}"></script>
        {plugin call='video.template_controller_play_addthis_start'}
        {addthis url=$aItem.link title=$aItem.title description=$sShareDescription}
        {plugin call='video.template_controller_play_addthis_end'}
    </div>

    {if !empty($aItem.text)}
        <div class="video-content item_view_content">
            <div class="video-content-title">{_p var='description'}</div>
            {$aItem.text|shorten:200:'view_more':true|parse}
        </div>
    {/if}
    {if $aItem.sHtmlCategories}
        <div class="video_category">
            <span>{_p('posted_in')}:</span>
            {$aItem.sHtmlCategories}
        </div>
    {/if}
    <div {if $aItem.view_id}style="display:none;" class="js_moderation_on"{/if}>
    {if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && isset($aItem.location_name)}
        <div class="activity_feed_location">
            <span class="activity_feed_location_at">{_p('at')} </span>
            <span class="js_location_name_hover activity_feed_location_name" {if isset($aItem.location_latlng) && isset($aItem.location_latlng.latitude)}onmouseover="$Core.Feed.showHoverMap('{$aItem.location_latlng.latitude}','{$aItem.location_latlng.longitude}', this);"{/if}>
                <span class="ico ico-checkin"></span>
                <a href="{if Phpfox::getParam('core.force_https_secure_pages')}https://{else}http://{/if}maps.google.com/maps?daddr={$aItem.location_latlng.latitude},{$aItem.location_latlng.longitude}" target="_blank">{$aItem.location_name}</a>
            </span>
        </div>
    {/if}
    <div class="item-detail-feedcomment">
    {module name='feed.comment'}
    </div>
    </div>
</div>

{if $bLoadCheckin}
<script type="text/javascript">
    var bCheckinInit = false;
    $Behavior.prepareInit = function()
    {l}
        $Core.Feed.sIPInfoDbKey = '';
        $Core.Feed.sGoogleKey = '{param var="core.google_api_key"}';

    {if isset($aVisitorLocation)}
        $Core.Feed.setVisitorLocation({$aVisitorLocation.latitude}, {$aVisitorLocation.longitude} );
    {else}

    {/if}

    $Core.Feed.googleReady('{param var="core.google_api_key"}');
    {r}
</script>
{/if}
<?php

defined('PHPFOX') or exit('NO DICE!');

?>

{if (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) && ( $aListing.time_stamp < (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400)) )}
    <div class="error_message">
        {_p var='listing_expired_and_not_available_main_section'}
    </div>
{/if}
{if $aListing.view_id == '1'}
    {template file='core.block.pending-item-action'}
{/if}
<div class="item_view market-app market-view-detail">
    <div class="item_info">
        {img user=$aListing suffix='_50_square'}
        <div class="item_info_author">
            <div>{_p var="By"} {$aListing|user:'':'':50}</div>
            <div>{$aListing.time_stamp|convert_time}</div>
        </div>
    </div>
    {if $aListing.view_id == 0}
    <div class="item-comment mb-2">
            <div>
               {module name='feed.mini-feed-action'}
           </div>
       
       <span class="item-total-view">
           <span>{$aListing.total_view|short_number}</span> {if $aListing.total_view == 1}{_p('view')}{else}{_p('views_lowercase')}{/if}
       </span>
    </div>
    {/if}
     <div class="item_info_more">
        <span class="listing_view_price" itemprop="price">{$sListingPrice}</span>
        {if Phpfox::isUser() && $aListing.user_id != Phpfox::getUserId()}
            <div class="listing_purchase">
                {if $aListing.canContactSeller}
                    <button class="btn btn-primary" onclick="$Core.composeMessage({l}user_id: {$aListing.user_id}, listing_id: {$aListing.listing_id}{r}); return false;">
                        <i class="ico ico-user2-next-o mr-1"></i>{_p var='contact_seller'}
                    </button>
                {/if}
                {if ($aListing.is_sell && $aListing.view_id != '2' && $aListing.price != '0.00')}
                    <form method="post" action="{url link='marketplace.purchase'}" class="form">
                        <div><input type="hidden" name="id" value="{$aListing.listing_id}" /></div>
                        <button type="submit" value="{_p var='buy_it_now'}" class="btn btn-success ml-1">
                            <i class="ico ico-cart-o mr-1"></i>{_p var='buy_it_now'}</button>
                    </form>
                {/if}
            </div>
        {/if}
    </div>

    {if $aListing.hasPermission}
    <div class="item_bar">
        <div class="dropdown">
            <span role="button" data-toggle="dropdown" class="item_bar_action">
                <i class="ico ico-gear-o"></i>
            </span>
            <ul class="dropdown-menu dropdown-menu-right">
                {template file='marketplace.block.menu'}
            </ul>
        </div>
    </div>
    {/if}
    <div class="market-detail-not-right">
        {if $aImages}
            <div class="ms-marketplace-detail-showcase dont-unbind market-app">
                <div class="ms-vertical-template ms-tabs-vertical-template dont-unbind" id="marketplace_slider-detail">
                    {foreach from=$aImages name=images item=aImage}
                    <div class="ms-slide ms-skin-default dont-unbind">
                        <img src="{img theme='misc/blank.gif' return_url=true}" data-src="{img server_id=$aImage.server_id path='marketplace.url_image' file=$aImage.image_path return_url=true}"/>
                        <div class="ms-thumb">
                            <img class="dont-unbind" src="{img server_id=$aImage.server_id path='marketplace.url_image' file=$aImage.image_path suffix='_120_square' return_url=true}" alt="thumb" />
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
        {/if}

        {module name='marketplace.info'}
    </div>

    <div {if $aListing.view_id != 0}style="display:none;" class="js_moderation_on market-addthis"{/if} class="market-addthis">
        <div class="item-categories">
            <span class="item-label">{_p var="Categories"}:</span>
            {$aListing.categories|category_display}
        </div>
        
        {addthis url=$aListing.bookmark_url title=$aListing.title description=$sShareDescription}
        <div class="item-detail-feedcomment">
            {module name='feed.comment'}
        </div>
    </div>
</div>

{literal}
<script type="text/javascript">
    $Behavior.initDetailSlide = function() {
        PF.event.on('on_page_column_init_end', function() {
        var ele = $('#marketplace_slider-detail');
        if (ele.prop('built') || !ele.length) return false;
        ele.prop('built', true).addClass('dont-unbind-children');
            var slider = new MasterSlider();
        
        var mp_direction;
        if($(window).width() < 481){
            mp_direction = 'h';
        } else {
            mp_direction = 'v';
        }

        slider.setup('marketplace_slider-detail' , {
            width: ele.width(),
            height: ele.width(),
            space:5,
            view:'basic',
            dir:mp_direction,
        });

        slider.control('arrows');   
        slider.control('scrollbar' , {dir:'v'});    
        slider.control('thumblist' , {autohide:false ,dir:mp_direction});

        slider.api.addEventListener(MSSliderEvent.CHANGE_END , function(){
            if ($('.ms-thumbs-cont .ms-thumb-frame').length < 2){
                $('.ms-thumbs-cont').parents('.ms-vertical-template.market-app').addClass('one-slide');
            }
            if (slider.api.count() < 5){
                $('#marketplace_slider-detail').addClass('less-4-slide');
            }
        });
        })
    };

</script>
{/literal}
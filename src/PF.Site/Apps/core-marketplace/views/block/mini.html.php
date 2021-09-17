<?php 
    defined('PHPFOX') or exit('NO DICE!');
?>
<article>
    <div class="item-outer flex">
        <div class="item-media">
            <a href="{permalink module='marketplace' id=$aMiniListing.listing_id title=$aMiniListing.title}" class="mp_listing_image" 
            style="background-image: url(
                {if !empty($aMiniListing.image_path)}
                    {img server_id=$aMiniListing.server_id title=$aMiniListing.title path='marketplace.url_image' file=$aMiniListing.image_path  suffix='_200_square' return_url=true}
                {else}
                    {param var='marketplace.marketplace_default_photo'}
                {/if}
            )" >
            </a>
        </div>
        <div class="item-inner overflow">
            <div class="item-price">
                {if $aMiniListing.price == '0.00'}
                    {_p var='free'}
                {else}
                    {$aMiniListing.price|currency:$aMiniListing.currency_id}
                {/if}
            </div>
            <a class="item-title" href="{permalink module='marketplace' id=$aMiniListing.listing_id title=$aMiniListing.title}">{$aMiniListing.title|clean}</a>
            <div class="item-author hide">{_p var='by'} {$aMiniListing|user:'':'':50}</div>
            <div class="item-statistic"><span>{$aMiniListing.total_view|short_number}</span> {if $aMiniListing.total_view == 1}{_p var='view__l'}{else}{_p var='views_lowercase'}{/if}</div>
        </div>
    </div>
</article>
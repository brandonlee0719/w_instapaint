<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
<div class="block pages-people-also-like">
    <div class="title">
        {_p var='people_also_like'}
    </div>
    <div class="content item-container">
        {foreach from=$aPages item=aAnother}
        <div class="page-item">
        <div class="page-cover"
            style="background-image:url(
            {if !empty($aAnother.cover_image_path)}
                {img server_id=$aAnother.cover_image_server_id path='photo.url_photo' file=$aAnother.cover_image_path return_url=true}
            {else}
                {img file=$sDefaultCoverPath return_url=true}
            {/if}
        )">
            <div class="page-shadow">
                <div class="page-avatar">
                    {img user=$aAnother suffix='_50_square'}
                </div>
                
                <div class="page-like">
                    <b>{$aAnother.total_like}</b>
                    <span>
                        {if $aAnother.total_like == 1}{_p var='like'}{else}{_p var='likes'}{/if}
                    </span>
                </div>
            </div>
        </div>

        <div class="page-info">
            <div class="page-name">
                <a href="{if $aAnother.vanity_url}{url link=$aAnother.vanity_url}{else}{url link='pages'}{$aAnother.page_id}{/if}" title="{$aAnother.title}">
                    {$aAnother.title}
                </a>
            </div>

            <div class="category-name">
                {_p var=$aAnother.category}
            </div>
        </div>
    </div>
        {/foreach}
    </div>
</div>
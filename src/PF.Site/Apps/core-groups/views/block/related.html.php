<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="block related-groups">
    <div class="title">
        {_p var='related_groups'}
    </div>
    <div class="content item-container">
        {foreach from=$aGroups item=aAnother}
        <div class="group-item ">
            <div class="group-cover"
                 style="background-image:url(
                {if !empty($aAnother.cover_image_path)}
                    {img server_id=$aAnother.cover_image_server_id path='photo.url_photo' file=$aAnother.cover_image_path return_url=true}
                {else}
                    {img file=$sDefaultCoverPath return_url=true}
                {/if}
            )">
                <div class="group-shadow">
                    <div class="group-avatar">
                        <a href="{if $aAnother.vanity_url}{url link=$aAnother.vanity_url}{else}{url link='groups'}{$aAnother.page_id}{/if}" title="{$aAnother.title}">
                            <div class="img-wrapper">
                                {img server_id=$aAnother.image_server_id title=$aAnother.title path='pages.url_image' file=$aAnother.image_path suffix='_50' max_width='50' max_height='50' is_page_image=true}
                            </div>
                        </a>
                    </div>
                    <div class="group-like">
                        <b>{$aAnother.total_like}</b>
                        <span>
                            {if $aAnother.total_like == 1}{_p var='member'}{else}{_p var='members'}{/if}
                        </span>
                    </div>
                </div>
            </div>
            <div class="group-info">
                <div class="group-name">
                    <a href="{if $aAnother.vanity_url}{url link=$aAnother.vanity_url}{else}{url link='groups'}{$aAnother.page_id}{/if}" title="{$aAnother.title}">
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
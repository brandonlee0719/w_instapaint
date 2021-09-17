<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="profile-joined-groups">
    {foreach from=$aPagesList name=pages item=user}
    <div class="page-item">
        <div class="page-cover" style="background-image:url(
        {if $user.cover_image_path}
            {img server_id=$user.cover_image_server_id path='photo.url_photo' file=$user.cover_image_path return_url=true}
        {else}
            {img file=$sDefaultCoverPath return_url=true}
        {/if}
        )">
            <div class="page-shadow">
                <div class="page-avatar">
                    {img user=$user}
                </div>

                <div class="page-like">
                    <b>{$user.total_like}</b>
                    <span>
                        {if $user.total_like == 1}{_p var='member'}{else}{_p var='members'}{/if}
                    </span>
                </div>
            </div>
        </div>

        <div class="page-info">
            <div class="page-name">
                {$user|user}
            </div>

            <div class="category-name">
                {_p var=$user.type_name}
                {if $user.category_name}
                » {_p var=$user.category_name}
                {/if}
            </div>
        </div>
    </div>
    {/foreach}
</div>

<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $aVideos}
    <div class="item-container with-video video-listing " id="container-video">
        {foreach from=$aVideos item=aItem}
            {template file='v.block.entry'}
        {/foreach}
    </div>
    {if $bShowModerator}
        {moderation}
    {/if}
    {pager}
{else}
    {if !PHPFOX_IS_AJAX}
        {_p('no_videos_found')}
    {/if}
{/if}
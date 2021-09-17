<?php
	defined('PHPFOX') or exit('NO DICE!');
?>
{if !isset($bIsInFeed) || (isset($bIsInFeed) && !$bIsInFeed)}
    {template file='music.block.mini-entry'}
{else}
    <div class="item-container music-listing">
        {foreach from=$aSongs item=aSong key=iKey name=song}
            {template file='music.block.mini-feed-entry'}
        {/foreach}
    </div>
{/if}
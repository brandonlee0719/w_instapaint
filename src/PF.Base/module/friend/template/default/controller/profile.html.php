<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($sNotShareFriend) && $sNotShareFriend}
    <div class="alert alert-warning">{$sNotShareFriend}</div>
{else}
    {if !PHPFOX_IS_AJAX}
    <form method="get" action="{$sProfileLink}" class="form">
        <div class="form-group">
            {$aFilters.search}
        </div>
    </form>
    <br/>
    {/if}
    {if ($aFriends)}
    {if !PHPFOX_IS_AJAX}
    <div class="wrapper-items user-listing item-container" id="collection-user-profiles">
    {/if}
    {foreach from=$aFriends name=friend item=aUser}
        <article class="user-item">
            {template file='user.block.rows_wide'}
        </article>
    {/foreach}
    {pager}
    {if !PHPFOX_IS_AJAX}
    </div>
    {/if}
    {elseif !PHPFOX_IS_AJAX}
    <p class="help-block">
        {_p var='No friends found.'}
    </p>
    {/if}
{/if}
<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !PHPFOX_IS_AJAX}
<div id="js_mp_item_holder" class="mp_item_holder">
{/if}
{if count($aInvites)}
    {foreach from=$aInvites name=invites item=aInvite}
        <div class="go_left t_center" id="js_mp_member_{$aInvite.invite_id}">
            <div class="p_4">
                {if !$aInvite.invited_user_id}

                <a href="javascript:void(0)" class="no_image_user _size__50 _gender_ _first_{$aInvite.invited_email|lower|shorten:2}"><span style="text-transform: uppercase">{$aInvite.invited_email|shorten:2}</span></a>
                    <div>
                    {$aInvite.invited_email|hide_email}
                    </div>
                {else}
                {img user=$aInvite suffix='_50' max_width=50 max_height=50}
                    <div>
                    {$aInvite|user}
                    </div>
                {/if}
            </div>
        </div>
        {if is_int($phpfox.iteration.invites / 3)}
        <div class="clear"></div>
        {/if}
    {/foreach}
{else}
    <div class="extra_info">
        {if $iType == 1}
            {_p var='no_visits'}
        {else}
            {_p var='no_results'}
        {/if}
    </div>
{/if}
{pager}
{if !PHPFOX_IS_AJAX}
</div>
{/if}
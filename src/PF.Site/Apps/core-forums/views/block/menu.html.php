<?php
?>
{if $aThread.canApprove}
    <li>
        <a href="javascript:void(0);" onclick="$.ajaxCall('forum.approveThread', 'inline=true&amp;id={$aThread.thread_id}', 'GET'); return false;""><i class="ico ico-check-square-alt mr-1"></i>{_p var='approve_thread'}</a>
    </li>
{/if}
{if $aThread.canEdit}
    <li>
        <a href="{if $aCallback === null}{url link='forum.post.thread' edit=$aThread.thread_id}{else}{url link='forum.post.thread' module=$aCallback.module_id item=$aCallback.item_id edit=$aThread.thread_id}{/if}"><i class="ico ico-pencilline-o mr-1"></i>{phrase var='forum.edit_thread'}</a>
    </li>
{/if}
{if $aCallback === null}
    {if $aThread.canMove}
        <li>
            <a href="javascript:void()" onclick="tb_show('{_p var='move_thread' phpfox_squote=true}', $.ajaxBox('forum.move', 'height=200&amp;width=550&amp;thread_id={$aThread.thread_id}')); return false;"><i class="ico ico-goright mr-1"></i>{_p var='move_thread'}</a>
        </li>
    {/if}
    {if $aThread.canCopy}
        <li>
            <a href="javascript:void()" onclick="tb_show('{_p var='copy_thread' phpfox_squote=true}', $.ajaxBox('forum.copy', 'height=200&amp;width=550&amp;thread_id={$aThread.thread_id}')); return false;"><i class="ico ico-copy-o mr-1"></i>{_p var='copy_thread'}</a>
        </li>
    {/if}
{/if}
{if $aThread.canStick}
    {if $aThread.order_id == 1}
        <li id="js_stick_thread_{$aThread.thread_id}">
            <a href="javascript:void()" onclick="return $Core.forum.stickThread('{$aThread.thread_id}', 0);"><i class="ico ico-thumb-tack-o mr-1"></i>{_p var='unstick_thread'}</a>
        </li>
    {else}
        <li id="js_stick_thread_{$aThread.thread_id}">
            <a href="javascript:void()" onclick="return $Core.forum.stickThread('{$aThread.thread_id}', 1);"><i class="ico ico-thumb-tack mr-1"></i>{_p var='stick_thread'}</a>
        </li>
    {/if}
{/if}
{if $aThread.canClose}
    {if $aThread.is_closed}
        <li id="js_close_thread_{$aThread.thread_id}">
            <a href="javascript:void()" onclick="return $Core.forum.closeThread('{$aThread.thread_id}', 0);"><i class="ico ico-check mr-1"></i>{_p var='open_thread'}</a>
        </li>
    {else}
        <li id="js_close_thread_{$aThread.thread_id}">
            <a href="javascript:void()" onclick="return $Core.forum.closeThread('{$aThread.thread_id}', 1);"><i class="ico ico-close-circle-o mr-1"></i>{_p var='close_thread'}</a>
        </li>
    {/if}
{/if}

{if $aThread.canMerge}
    <li>
        <a href="javascript:void()" onclick="tb_show('{_p var='merge_threads' phpfox_squote=true}', $.ajaxBox('forum.merge', 'height=200&amp;width=550&amp;thread_id={$aThread.thread_id}')); return false;"><i class="ico ico-merge-file-o mr-1"></i>{_p var='merge_threads'}</a>
    </li>
{/if}
<li role="separator" class="divider"></li>
<li id="js_subscribe_{$aThread.thread_id}"{if $aThread.is_subscribed} style="display:none;"{/if}>
    <a href="javascript:void()" onclick="$(this).parent().hide(); $.ajaxCall('forum.subscribe', 'thread_id={$aThread.thread_id}&amp;subscribe=1'); return false;"><i class="ico ico-check-circle-alt mr-1"></i>{_p var='subscribe'}</a>
</li>
<li id="js_unsubscribe_{$aThread.thread_id}"{if !$aThread.is_subscribed} style="display:none;"{/if}>
    <a href="javascript:void()" onclick="$(this).parent().hide(); $.ajaxCall('forum.subscribe', 'thread_id={$aThread.thread_id}&amp;subscribe=0'); return false;"><i class="ico ico-ban mr-1"></i>{_p var='unsubscribe'}</a>
</li>

{if $aThread.canSponsor}
    <li>
        <a id="js_sponsor_thread_{$aThread.thread_id}" {if $aThread.order_id == 2}style="display:none;"{/if} href="javascript:void()" onclick="$.ajaxCall('forum.sponsor','thread_id={$aThread.thread_id}&type=2');return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor'}
        </a>
        <a id="js_unsponsor_thread_{$aThread.thread_id}" {if $aThread.order_id != 2}style="display:none;"{/if} href="javascript:void()" onclick="$.ajaxCall('forum.sponsor','thread_id={$aThread.thread_id}&type=0');return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor'}
        </a>
    </li>
    <li role="separator" class="divider"></li>
{elseif $aThread.canPurchaseSponsor}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aThread.thread_id}section_forum_thread/"><i class="ico ico-sponsor mr-1"></i>{_p var='sponsor'}</a>
    </li>
{/if}
{if $aThread.canDelete}
    <li class="item_delete">
        <a href="javascript:void()" onclick="return $Core.forum.deleteThread('{$aThread.thread_id}');"><i class="ico ico-trash-o mr-1"></i>{_p var='delete_thread'}</a>
    </li>
{/if}

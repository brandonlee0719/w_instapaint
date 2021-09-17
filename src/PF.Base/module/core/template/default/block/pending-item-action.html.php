<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @package 		Phpfox
 */

defined('PHPFOX') or exit('NO DICE!');
?>

<div class="item-pending alert alert-info">
    <span class="pending-message">{$aPendingItem.message}</span>
    {if !empty($aPendingItem.actions) && is_array($aPendingItem.actions)}
        {foreach from=$aPendingItem.actions item=aAction key=sKey}
            <a {if !empty($aAction.is_ajax)}
                    onclick="{$aAction.action}; return false;" class="pending-action"
               {else}
                    href="{$aAction.action}" class="pending-action {if !empty($aAction.is_confirm)}sJsConfirm{/if}"
                    {if !empty($aAction.confirm_message)}data-message="{$aAction.confirm_message}"{/if}
               {/if}
            >
                <span class="btn btn-xs {if $sKey == 'approve'}btn-primary{else}btn-default{/if}">
                    {if $sKey == 'approve'}
                        <i class="ico ico-check-square-alt mr-1"></i>
                    {elseif $sKey == 'edit'}
                        <i class="ico ico-pencilline-o mr-1"></i>
                    {elseif $sKey == 'delete'}
                        <i class="ico ico-trash-o mr-1"></i>
                    {elseif !empty($aAction.custom_icon)}
                        <i class="{$aAction.custom_icon} mr-1"></i>
                    {/if}
                    {if !empty($aAction.label)}
                        <span class="pending-action-label">{$aAction.label}</span>
                    {/if}
                </span>
            </a>
        {/foreach}
    {/if}
</div>

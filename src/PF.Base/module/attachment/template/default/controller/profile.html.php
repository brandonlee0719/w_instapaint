<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $aRows && count($aRows)}
    {if !PHPFOX_IS_AJAX}
    <div id="attachment_manage">
    {/if}
        {assign var='close_div' value=0}
        {foreach from=$aRows key=iKey item=aRow}
            {if isset($aRow.time_name)}
                {if $close_div}
                    </div>
                    </div>
                    {assign var='close_div' value=0}
                {/if}
                <div class="attachment_time_same_block attachment_profile_view">
                <div class="attachment_time_title">{$aRow.time_name}</div>
                <div class="attachment_list">
                {assign var='close_div' value=1}
            {/if}
            {template file='attachment.block.item'}
        {/foreach}
        {if $close_div}
            </div>
            </div>
        {/if}
        {pager}
    {if !PHPFOX_IS_AJAX}
    </div>
    {/if}
{elseif !PHPFOX_IS_AJAX}
    {_p var="No attachments found"}
{/if}
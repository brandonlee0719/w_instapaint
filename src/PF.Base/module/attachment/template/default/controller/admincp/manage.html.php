<?php
/**
 * Date: 5/12/16
 * Time: 16:02
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form-search" method="post" action="{url link='admincp.attachment.manage'}">
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="">{_p var="Attachment name"}</label>
                {$aFilters.name}
            </div>
            <div class="form-group col-sm-2">
                <label for="">{_p var="Display"}</label>
                {$aFilters.display}
            </div>
            <div class="form-group col-sm-2">
                <label for="">{_p var="Sort by"}</label>
                {$aFilters.sort}
            </div>
            <div class="form-group col-sm-2">
                <label>&nbsp;</label>
                {$aFilters.sort_by}
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <div><button type="submit" name="search[submit]" class="btn btn-primary" >{_p var='submit'}</button></div>
            </div>
        </div>

    </div>
</div>
</form>
{if $aRows && count($aRows)}
<div id="attachment_manage">
    {assign var='close_div' value=0}
    {foreach from=$aRows key=iKey item=aRow}
    {if isset($aRow.time_name)}
    {if $close_div}
</div>
{assign var='close_div' value=0}
{/if}
<div class="attachment_time_same_block">
    <h3 class="time">{$aRow.time_name}</h3>
    {assign var='close_div' value=1}
    {/if}
    {template file='attachment.block.item-admin'}
    {/foreach}
    {if $close_div}
</div>
{/if}
{pager}
</div>
{elseif !PHPFOX_IS_AJAX}
<div class="alert alert-empty">
{_p var="No attachments found"}
</div>
{/if}
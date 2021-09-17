<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='add_filter'}</div>
    </div>
    <div class="panel-body">
        <form method="post" action="{url link=$aBanFilter.url}" onsubmit="$Core.onSubmitForm(this, true);">
            <div class="form-group">
                <label for="find_value">{$aBanFilter.form}:</label>
                <input type="text" name="find_value" value="{ $sFindValue}" size="30" class="form-control" id="find_value"/>
                <span class="help-block">{_p var='use_the_asterisk_for_wildcard_entries'}</span>
            </div>
            {if isset($aBanFilter.replace)}
            <div class="form-group">
                <label for="replacement">{_p var='replacement'}:</label>
                <input type="text" name="replacement" value="" size="30" class="form-control" id="replacement"/>
            </div>
            {/if}
            {module name='ban.form'}
            <input type="submit" value="{_p var='add'}" class="btn btn-primary" />
        </form>
    </div>
</div>

<div class="block_content">
	{if count($aFilters)}
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
                <tr>
                    <th style="width:20px;"></th>
                    <th>{$aBanFilter.form}</th>
                    {if isset($aBanFilter.replace)}
                    <th>{_p var='replacement'}</th>
                    {/if}
                    <th style="width:150px;">{_p var='added_by'}</th>
                    <th style="width:150px;">{_p var='added_on'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aFilters name=filters item=aFilter}
                <tr{if !is_int($phpfox.iteration.filters/2)} class="tr"{/if}>
                    <td class="t_center">
                        <a href="{url link=$aBanFilter.url delete={$aFilter.ban_id}"  data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a>
                    </td>
                    <td>{$aFilter.find_value}</td>
                    {if isset($aBanFilter.replace)}
                    <td>{$aFilter.replacement}</td>
                    {/if}
                    <td>{if empty($aFilter.user_id)}{_p var='n_a'}{else}{$aFilter|user}{/if}</td>
                    <td>{$aFilter.time_stamp|date}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
	{else}
	<div class="alert alert-empty">
		{_p var='no_bans_found_dot'}
	</div>
	{/if}
</div>
<?php
defined('PHPFOX') or exit('NO DICE!');
/**
 * @author Neil <neil@phpfox.com>
 */
?>

{if !PHPFOX_IS_AJAX_PAGE}
<div id="app-custom-holder" class="hide" style="min-height:400px;"></div>
<div id="app-content-holder">
{/if}
    {if isset($userGroupSettings) && $userGroupSettings}
    <section class="app_grouping _is_user_group_settings">
        <form class="on_change_submit " method="post" action="{url link='current'}" enctype="multipart/form-data">
            <h1>{_p var='user_group_settings'}</h1>
            {foreach from=$userGroupSettings item=group key=var}
            <div class="user_group_rows">
                <div class="_title">
                    {$group.name}
                </div>
                <div class="_settings">form
                    {foreach from=$group.settings item=setting key=var}
                    <div class="table_header2 ">
                        {$setting.info}
                    </div>
                    <div class="table3 settings">
                        <div class="row_right">
                            {if $setting.type == 'input:text'}
                            <input type="text" name="user_group_setting[{$group.id}][{$var}]"
                                   value="{$setting.value|clean}" class="form-control">
                            {elseif $setting.type == 'currency'}
                            <div class="currency_setting">
                                {foreach from=$setting.value key=sName item=aValue}
                                <div class="currency">
                                    <span class="js_hover_title"><span class="js_hover_info">{_p var=$aValue.name}</span>{$aValue.symbol}</span>
                                    <input type="text" name="user_group_setting[{$group.id}][{$var}][{$sName}]" value="{$aValue.value}" size="10" />
                                </div>
                                {/foreach}
                            </div>
                            {elseif $setting.type == 'input:radio'}
                            <div class="item_is_active_holder">
									<span class="js_item_active item_is_active">
										<input class="form-control" type="radio" {if $setting.value== 1}
                                               checked="checked" {/if} name="user_group_setting[{$group.id}][{$var}]" value="1"> {_p var="Yes"}
									</span>
                                        <span class="js_item_active item_is_not_active">
										<input class="form-control" type="radio" {if $setting.value !=1}
                                               checked="checked" {/if} name="user_group_setting[{$group.id}][{$var}]" value="0"> {_p var="No"}
									</span>
                                    </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
            {/foreach}
            <div class="form-group submit_btn">
                <input type="submit" class="btn btn-danger" value="{_p var='Save Changes'}">
            </div>
    </form>
</section>
{/if}
{if !PHPFOX_IS_AJAX_PAGE}
</div>
<div id="app-details">
    {if (!$ActiveApp.is_core)}
    <ul>
        <li><a {if $App.is_module}class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}" {/if}
            href="{$uninstallUrl}">{_p var='uninstall'}</a></li>
        {if $export_path && defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE}
        <li><a href="{$export_path}">{_p var="Export"}</a></li>
        {/if}
    </ul>
    {/if}
    <div class="app-copyright">
        {if $ActiveApp.vendor}
        ©{$ActiveApp.vendor}
        {/if}
        {if $ActiveApp.credits}
        <div class="app-credits">
            <div>{_p var="Credits"}</div>
            {foreach from=$ActiveApp.credits item=url key=name}
            <ul>
                <li><a href="{$url}">{$name|clean}</a></li>
            </ul>
            {/foreach}
        </div>
        {/if}
    </div>
</div>
{/if}
<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: setting.html.php 3826 2011-12-16 12:30:19Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aModules key=iBlock item=aSubBlocks}
	<div class="table_header2">
		{_p var='block_block_number' block_number=$iBlock}
	</div>
    <div class="table-responsive">
        <table class="table table-admin js_drag_drop">
            <tbody>
                {foreach from=$aSubBlocks key=iKey item=aBlock}
                <tr class="checkRow tr">
                    <td class="drag_handle"><input type="hidden" name="val[ordering][{$aBlock.block_id}]" value="{$aBlock.ordering}" /></td>
                    <td class="text-center w20">
                        <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                        <div class="link_menu">
                            <ul class="dropdown-menu">
                                <li><a href="{url link='admincp.block.add.' id=$aBlock.block_id}">{_p var='edit'}</a></li>
                                <li><a href="{url link='admincp.block.setting.' id=$aBlock.block_id}">{_p var='settings'}</a></li>
                                <li><a href="{url link='admincp.block.' delete=$aBlock.block_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        {if !empty($aBlock.title)}
                            {$aBlock.title}
                        {else}
                            {if $aBlock.type_id > 0}
                                {if $aBlock.type_id == 1}
                                    {_p var='php_code'}
                                {else}
                                    {_p var='html_code'}
                                {/if}
                            {else}
                                {$aBlock.module_name}::{$aBlock.component}
                            {/if}
                        {/if}
                    </td>
                    <td class="on_off">
                        <div class="js_item_is_active"{if !$aBlock.is_active} style="display:none;"{/if}>
                            <a href="#?call=admincp.updateBlockActivity&amp;id={$aBlock.block_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active"{if $aBlock.is_active} style="display:none;"{/if}>
                            <a href="#?call=admincp.updateBlockActivity&amp;id={$aBlock.block_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                        </div>
                    </td>

                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{/foreach}
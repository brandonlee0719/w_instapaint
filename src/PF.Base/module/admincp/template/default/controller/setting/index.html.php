<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: index.html.php 2831 2011-08-12 19:44:19Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<h3>{_p var='global_settings'}</h3>
{foreach from=$aGroups item=aGroup name=group}
<div class="go_left p_4" style="width:30%;">
	<a href="{url link="admincp.setting.edit" group-id=""$aGroup.group_id""}">{$aGroup.var_name}</a>{if PHPFOX_DEBUG} ({$aGroup.total_settings}){/if}
</div>
{if is_int($phpfox.iteration.group/3)}
<br class="clear" />
{/if}
{/foreach}
<div class="clear"></div>

<h3>{_p var='module_settings'}</h3>
{foreach from=$aModules item=aModule name=module}
<div class="go_left p_4" style="width:30%;">
	<a href="{url link="admincp.setting.edit" module-id=""$aModule.module_id""}">{$aModule.module_id|translate:'module'}</a>{if PHPFOX_DEBUG} ({$aModule.total_settings}){/if}

</div>
{if is_int($phpfox.iteration.module/3)}
<br class="clear" />
{/if}
{/foreach}

<div class="clear"></div>
{if count($aProductGroups)}
<h3>{_p var='product_settings'}</h3>
{foreach from=$aProductGroups item=aProductGroup name=product}
<div class="go_left p_4" style="width:30%;">
	<a href="{url link="admincp.setting.edit" product-id=""$aProductGroup.product_id""}">{$aProductGroup.var_name}</a>{if PHPFOX_DEBUG} ({$aProductGroup.total_settings}){/if}
</div>
{if is_int($phpfox.iteration.product/3)}
<br class="clear" />
{/if}
{/foreach}
<div class="clear"></div>
{/if}
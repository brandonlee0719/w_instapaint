<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !PHPFOX_IS_AJAX}
<div id="js_basic_info_data">
{/if}
	{if Phpfox::getParam('user.enable_relationship_status') && $sRelationship != ''}
	<div class="info">
	    <div class="info_left">
		{_p var='relationship_status'}
	    </div>
	    <div class="info_right">
		{$sRelationship}
	    </div>
	</div>
	{/if}
	{foreach from=$aUserDetails key=sKey item=sValue}
	{if !empty($sValue)}
	<div class="info">
		<div class="info_left">
			{$sKey}:
		</div>	
		<div class="info_right">
			{$sValue}
		</div>	
	</div>
	{/if}
	{/foreach}
    {module name='core.info'}
	{if $bShowCustomFields}
	{module name='custom.display' type_id='user_panel' template='info'}
	{/if}
	{plugin call='profile.template_block_info'}
{if !PHPFOX_IS_AJAX}
</div>
<div id="js_basic_info_form"></div>
{/if}
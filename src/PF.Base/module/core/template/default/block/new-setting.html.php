<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<script type="text/javascript">
	function saveWhatsNewSettings(oObj)
	{left_curly}
		$(oObj).ajaxCall('core.updateComponentSetting'); $(oObj).parents('.edit_bar:first').slideUp().html(''); return false;
	{right_curly}
</script>

<form method="post" action="#" onsubmit="return saveWhatsNewSettings(this);" class="form">
	<div><input type="hidden" name="val[var_name]" value="core.whats_new_blocks" /></div>
	<div><input type="hidden" name="val[load_block]" value="core.new" /></div>
	<div><input type="hidden" name="val[block_id]" value="js_block_border_core_new" /></div>
	<div><input type="hidden" name="val[load_entire_block]" value="true" /></div>
	<div><input type="hidden" name="val[load_init]" value="true" /></div>

    <div class="form-group">
        <label for="title">{_p var='display'}</label>
        <select name="val[display][]" multiple="multiple" style="width:90%; height:75px;">
            {foreach from=$aModuleItems key=sModuleName item=aModuleItem}
            <option value="{$aModuleItem.id}"{if $aModuleItem.is_used} selected="selected"{/if}>{$aModuleItem.name}</option>
            {/foreach}
        </select>
    </div>

	<div class="form-group">
		<input type="submit" value="{_p var='save'}" class="btn btn-primary" />
		<input type="button" value="{_p var='cancel'}" class="btn v_middle" onclick="$(this).parents('.edit_bar:first').slideUp().html('');" />
	</div>
</form>
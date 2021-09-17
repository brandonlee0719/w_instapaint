<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table form-group">
	<div class="table_left">
		{_p var=$aField.phrase_var_name}:
	</div>
	<div class="table_right">
		{if $aField.var_type == 'textarea'}
			<textarea class="form-control" name="static[{$aField.field_id}]"></textarea>
		{/if}
	</div>
</div>

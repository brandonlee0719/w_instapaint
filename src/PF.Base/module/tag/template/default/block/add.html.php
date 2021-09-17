<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="table form-group">
	<label>
		{_p var='topics'}:
	</label>
	<div >
		<input type="text" name="val{if $iItemId}[{$iItemId}]{/if}[tag_list]" value="{value type='input' id='tag_list'}" size="30" />
		<div class="help-block">
			{_p var='separate_multiple_topics_with_commas'}
		</div>
	</div>
	<div class="clear"></div>
</div>
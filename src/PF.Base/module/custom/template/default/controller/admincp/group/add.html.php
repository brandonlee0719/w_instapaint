<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{$sGroupCreateJs}
<div class="panel panel-default">
	<div class="panel-body">
        <form class="form" method="post" action="{url link='admincp.custom.group.add'}" id="js_group_field" onsubmit="{$sGroupGetJsForm}">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.group_id}" /></div>
            {/if}
            {template file='custom.block.group-form'}
            <div class="form-group">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
        </form>
    </div>
</div>
<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.music.add' edit=$iEditId}" onsubmit="$Core.onSubmitForm(this, true);">
    <div class="panel panel-default">
        <div class="panel-body">
        {if $bIsEdit}
            <div><input type="hidden" name="val[edit_id]" value="{$iEditId}" /></div>
            <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
        {/if}

        {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{if $bIsEdit}{_p var='edit_genre'}{else}{_p var='add_genre'}{/if}" class="button btn-primary" />
        </div>
    </div>
</form>
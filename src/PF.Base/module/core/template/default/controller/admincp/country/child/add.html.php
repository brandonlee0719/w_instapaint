<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.core.country.child.add'}" class="form">
    <div class="panel panel-default">
        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.child_id}" /></div>
            {else}
            {if !empty($sIso)}
            <div><input type="hidden" name="val[country_iso]" value="{$sIso}" /></div>
            {/if}
            {/if}

            {if empty($sIso)}
            <div class="form-group">
                <label for="country_iso">{required}{_p var='country'}</label>
                {select_location}
            </div>
            {/if}
            <div class="form-group">
                <label for="name">{required}{_p var='name'}</label>
                <input type="text" name="val[name]" value="{value id='name' type='input'}" size="40" id="name" class="form-control"/>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{if $bIsEdit}{_p var='update'}{else}{_p var='submit'}{/if}" class="btn btn-primary" />
        </div>
    </div>
</form>
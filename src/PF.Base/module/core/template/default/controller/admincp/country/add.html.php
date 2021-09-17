<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.core.country.add'}" class="form">
    <div class="panel pandel-default">
        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.country_iso}"/></div>
            {/if}
            <div class="form-group">
                <label for="country_iso" class="required">{_p var='iso'}</label>
                <input type="text" class="form-control" name="val[country_iso]" value="{value id='country_iso' type='input'}"
                       size="4"/>
            </div>
            <div class="form-group">
                <label for="name" class="required">{_p var='name'}</label>
                <input type="text" class="form-control" name="val[name]" value="{value id='name' type='input'}" size="40"/>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{if $bIsEdit}{_p var='update'}{else}{_p var='submit'}{/if}"
                   class="btn btn-primary"/>
        </div>
    </div>
</form>
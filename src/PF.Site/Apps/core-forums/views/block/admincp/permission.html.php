<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{foreach from=$aPerms key=sPerm item=aPerm}
<div class="form-group">
    <label for="{$sPerm}">
            {$aPerm.phrase}
    </label>
    <div class="item_is_active_holder">
        <span class="js_item_active item_is_active"><input type="radio" name="val[perm][{$sPerm}]" value="1"{if $aPerm.value} checked="checked"{/if}/> {_p var='yes'}</span>
        <span class="js_item_active item_is_not_active"><input type="radio" name="val[perm][{$sPerm}]" value="0"{if !$aPerm.value} checked="checked"{/if}/> {_p var='no'}</span>
    </div>

</div>
{/foreach}
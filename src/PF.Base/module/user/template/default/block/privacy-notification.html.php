<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="form-group">
    <label>{$aNotification.phrase}</label>
        <div class="item_is_active_holder">
            <span class="js_item_active item_is_active">
                <input type="radio" value="0" name="val[notification][{$sNotification}]" {if $aNotification.default} checked="checked"{/if} class="checkbox" /> {_p var='yes'}
            </span>
            <span class="js_item_active item_is_not_active">
                <input type="radio" value="1" name="val[notification][{$sNotification}]" {if !$aNotification.default} checked="checked"{/if} class="checkbox" /> {_p var='no'}
            </span>
        </div>
</div>
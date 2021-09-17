<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{foreach from=$aSettings item=aSetting}
<div class="table js_custom_groups{if isset($aSetting.group_id)} js_custom_group_{$aSetting.group_id}{/if}">
    <div class="table_left">
        {if $aSetting.is_required && !Phpfox::isAdminPanel()}{required}{/if}{_p var=$aSetting.phrase_var_name}:
    </div>
    <div class="table_right">
        {template file='custom.block.form'}
    </div>
</div>
{/foreach}
{plugin call='user.template_controller_profile_form'}
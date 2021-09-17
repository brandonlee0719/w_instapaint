<?php
defined('PHPFOX') or exit('No dice!');
?>

<div id="poke_{$aUser.user_id}_content" class="popup-poke-content">
    {_p var='you_are_about_to_poke_full_name' full_name=$aUser.full_name}
</div>
<div class="clear"></div>
<div class="p_top_15">
    <div class="table_clear text-right">
        <button class="btn btn-primary btn-sm" onclick="$.ajaxCall('poke.doPoke', 'user_id={$aUser.user_id}', 'GET');tb_remove();">{_p var='poke' full_name=''}</button>
    </div>
</div>
<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-body">
        {if Phpfox::getService('user')->isAdminUser('' . $iUserIdDelete . '')}
        <p>{_p var='you_are_unable_to_delete_a_site_administrator'}</p>
        {else}
        <p>
            {_p var='are_you_completely_sure_you_want_to_delete_this_user'}
        </p>
        {/if}
    </div>
    {if !Phpfox::getService('user')->isAdminUser('' . $iUserIdDelete . '')}
    <div class="panel-footer">
        <input type="button" class="btn btn-default" value="{_p var='no_cancel'}" onclick="tb_remove();">
        <input type="button" class="btn btn-danger" value="{_p var='yes_delete'}" onclick="$.ajaxCall('user.confirmedDelete', 'iUser={$aUser.user_id}');">
    </div>
    {/if}
</div>

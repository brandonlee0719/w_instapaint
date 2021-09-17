<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form class="form" action="{url link='admincp.user.ban' user=$aUser.user_id}" method="post">
	<div><input type="hidden" name="aBan[user]" value="{$aUser.user_id}"></div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='ban_user'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='you_are_about_to_ban_the_user'}</label>
                {$aUser|user}
            </div>
            {module name='ban.form' bShow=true bHideAffected=true}
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="{_p var='ban_user'}">
            </div>
        </div>
    </div>
</form>
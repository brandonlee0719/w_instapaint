<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="main_break"></div>
<div id="js_progress_cache_loader"></div>
<form method="post" class="form" action="{url link='current'}" onsubmit="$(this).ajaxCall('user.updatePassword'); return false;">
    {if empty($bPassOld) || !$bPassOld}
        <div class="form-group">
            <label for="old_password">{_p var='old_password'}</label>
            <input type="password" id="old_password" name="val[old_password]" value="" size="30" class="form-control" autocomplete="off" />
        </div>
	    <div class="separate"></div>
    {/if}

    <div class="form-group">
        <label for="new_password">{_p var='new_password'}</label>
        <input type="password" id="new_password" name="val[new_password]" value="" size="30" class="form-control" autocomplete="off" />
    </div>
    <div class="form-group">
        <label for="confirm_password">{_p var='confirm_password'}</label>
        <input type="password" id="confirm_password" name="val[confirm_password]" value="" size="30" class="form-control" autocomplete="off" />
    </div>
	<div class="form-group">
		<input type="submit" value="{_p var='update'}" class="button btn-primary" />
	</div>
</form>
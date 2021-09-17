<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="alert alert-danger">
	{_p var='b_notice_b_this_routine_is_highly_experimental'}
</div>
<div class="alert alert-empty">
	<p class="help-block">
        {_p var='all_items_on_the_site_store_certain_information_in_the_database'}
    </p>
</div>
<form method="post" action="{url link='admincp.sql.title'}" class="form">
    <div><input type="hidden" name="update" value="1" /></div>
    <input type="submit" value="{_p var='update_database_tables'}" class="btn btn-primary" />
</form>
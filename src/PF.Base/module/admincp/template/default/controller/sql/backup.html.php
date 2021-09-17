<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: backup.html.php 1268 2009-11-23 20:45:36Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bCanBackup}
<form method="post" action="{url link='admincp.sql.backup'}">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='sql_backup_header'}</div>
        <div class="panel-body">

        </div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="val_path_default" class="required">{_p var='path'}</label>
            <input id="val_path_default" type="text" name="path" value="{$sDefaultPath}"  class="form-control"/>
            <div class="help-block">
                {_p var='provide_the_full_path_to_where_we_should_save_the_sql_backup'}
            </div>
        </div>
        <div class="form-group">
            <input type="submit" value="{_p var='save'}" class="button btn-primary" />
        </div>
    </div>
</div>
</form>

{else}
<div class="alert alert-danger">
	{_p var='your_operating_system_does_not_support_the_method_of_backup_we_provide'}
</div>
{/if}
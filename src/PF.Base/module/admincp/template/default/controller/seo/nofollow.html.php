<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="#" class="form" onsubmit="$(this).ajaxCall('admincp.nofollow'); return false;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='add_new_url'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='url'}</label>
                <input type="input" name="val[url]" value="" size="60" id="js_nofollow_url" class="form-control"/>
                <p class="help-block">
                    {_p var='provide_the_full_url_to_the_page'}
                </p>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>

<br /><br />

<div id="js_nofollow_holder"{if !count($aNoFollows)} style="display:none;"{/if}>	
	<form method="post" class="from" action="#" onsubmit="$(this).ajaxCall('admincp.deleteNoFollow'); return false;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">{_p var='urls'}</div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table" id="js_nofollow_holder_table">
                        <thead>
                            <tr>
                                <th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" /></th>
                                <th>{_p var='url'}</th>
                                <th style="width:20%;">{_p var='added'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$aNoFollows item=aNoFollow key=iKey}
                            <tr id="js_id_row_{$aNoFollow.nofollow_id}" class="js_nofollow_row {if is_int($iKey/2)} tr{else}{/if}">
                                <td><input type="checkbox" name="id[]" class="checkbox" value="{$aNoFollow.nofollow_id}" id="js_id_row{$aNoFollow.nofollow_id}" /></td>
                                <td>{$aNoFollow.url}</td>
                                <td>{$aNoFollow.time_stamp|convert_time}</td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    <div class="panel-footer">
                        <input type="submit" name="delete" value="{_p var='delete'}" class="btn sJsConfirm disabled sJsCheckBoxButton" disabled="true" />
                    </div>
                </div>
            </div>
        </div>
	</form>
</div>
<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" class="form" action="#" onsubmit="$(this).ajaxCall('admincp.addMeta'); return false;" id="js_meta_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='add_new_element'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="val_type_id">{_p var='type'}</label>
                <select name="val[type_id]" class="form-control" id="val_type_id">
                    <option value="0">{_p var='keyword'}</option>
                    <option value="1">{_p var='description'}</option>
                    <option value="2">{_p var='title'}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="js_nofollow_url">{_p var='url'}</label>
                <input type="text" class="form-control" name="val[url]" value="" size="60" id="js_nofollow_url" />
                <p class="help-block">
                    {_p var='provide_the_full_url_to_add_your_custom_element'}
                </p>
            </div>
            <div class="form-group">
                <label for="val_content">{_p var='value'}</label>
                <textarea name="val[content]"  rows="6" class="form-control" id="val_content"></textarea>
                <p class="help-block">
                    {_p var='if_adding_keywords_make_sure_to_separate_them_with_commas'}
                </p>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>

<div id="js_meta_holder"{if !count($aMetas)} style="display:none;"{/if}>	
	<form method="post" action="#" class="form" onsubmit="$(this).ajaxCall('admincp.deleteMeta'); return false;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">{_p var='meta_keyword_descriptions'}</div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table" cellpadding="0" cellspacing="0" id="js_meta_holder_table">
                        <thead>
                            <tr>
                                <th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" /></th>
                                <th>{_p var='type'}</th>
                                <th>{_p var='url'}</th>
                                <th>{_p var='value'}</th>
                                <th class="w20">{_p var='added'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$aMetas item=aMeta key=iKey}
                            <tr id="js_id_row_{$aMeta.meta_id}" class="js_nofollow_row {if is_int($iKey/2)} tr{else}{/if}">
                                <td><input type="checkbox" name="id[]" class="checkbox" value="{$aMeta.meta_id}" id="js_id_row{$aMeta.meta_id}" /></td>
                                <td>{if $aMeta.type_id == '1'}{_p var='description'}{elseif $aMeta.type_id == '2'}Title{else}{_p var='keyword'}{/if}</td>
                                <td>{$aMeta.url}</td>
                                <td><textarea name="val[{$aMeta.meta_id}][content]" cols="30" rows="4" style="height:30px;">{$aMeta.content|clean}</textarea></td>
                                <td>{$aMeta.time_stamp|convert_time}</td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <input type="submit" name="delete" value="{_p var='delete'}" class="btn sJsConfirm disabled sJsCheckBoxButton" disabled />
                </div>
            </div>
        </div>
	</form>
</div>
<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" {if $bIsEdit} action="{url link='admincp.forum.add' id=$iId}" {else} action="{url link='admincp.forum.add'}" {/if} id="js_form" onsubmit="$Core.onSubmitForm(this, true);">
    <div class="panel-group">
        {if isset($aForms.forum_id)}
            <div><input type="hidden" name="val[edit_id]" value="{$aForms.forum_id}" /></div>
            <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
            <div><input type="hidden" name="val[description]" value="{$aForms.description}" /></div>
        {/if}
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title">{_p var='forum_details'}</div></div>
            <div class="panel-body">
                <div class="form-group">
                    {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=255 help_phrase='left_empty_will_have_same_value_with_default_language' required=true}
                </div>
                {if !empty($sForumParents)}
                <label for="parent_forum">{_p var='parent_forum'}:</label>
                <div class="form-group">
                    <select name="val[parent_id]" class="form-control">
                        <option value="">{_p var='select'}:</option>
                        {$sForumParents}
                    </select>
                </div>
                {/if}
                <div class="form-group">
                    {field_language phrase='description' label='description' field='description' format='val[description_' type='textarea' rows=5 maxlength=500 help_phrase='left_empty_will_have_same_value_with_default_language'}
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label for="is_a_category">{_p var='is_a_category'}:</label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active">
                            <input class='form-control' type="radio" name="val[is_category]" value="1" {value type='radio' id='is_category' default='1' }/> {_p var='yes'}
                        </span>
                        <span class="js_item_active item_is_not_active">
                            <input class='form-control' type="radio" name="val[is_category]" value="0" {value type='radio' id='is_category' default='0' selected='true'}/> {_p var='no'}
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="closed"> {_p var='closed'}:</label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active">
                            <input class='form-control' type="radio" name="val[is_closed]" value="1" {value type='radio' id='is_closed' default='1' }/> {_p var='yes'}
                        </span>
                        <span class="js_item_active item_is_not_active">
                            <input class='form-control' type="radio" name="val[is_closed]" value="0" {value type='radio' id='is_closed' default='0' selected='true'}/> {_p var='no'}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="panel panel-default">
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary btn_submit" />
            {if isset($aForms)}
            <input type="button" name="cancel" value="{_p var='cancel_uppercase'}" class="btn btn-default" onclick="window.location.href = '{url link='admincp.forum'}';" />
            {/if}
        </div>
	</div>
</form>
<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 4554 2012-07-23 08:44:50Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form" method="post" action="{url link='admincp.subscribe.add'}" enctype="multipart/form-data">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.package_id}" /></div>
{/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='subscription_details'}</div>
        </div>
        <div class="panel-body">
            {field_language phrase='sPhraseTitle' label='title' field='title' format='val[title][' size=40 maxlength=100 required=true}
            {field_language phrase='sPhraseDescription' label='description' field='description' format='val[description][' rows="10" required=true type="textarea"}
            <div class="form-group">
                <label for="image">{_p var='image'}</label>
                {if $bIsEdit && !empty($aForms.image_path)}
                <div id="js_subscribe_image_holder">
                    {img server_id=$aForms.server_id title=$aForms.title path='subscribe.url_image' file=$aForms.image_path suffix='_120' max_width='120' max_height='120'}
                    <p class="help-block">
                        <a href="#" onclick="$Core.jsConfirm({l}message: '{_p var='are_you_sure'}'{r}, function(){l} $('#js_subscribe_image_holder').remove(); $('#js_subscribe_upload_image').show(); $.ajaxCall('subscribe.deleteImage', 'package_id={$aForms.package_id}'); {r}, function(){l}{r}); return false;">{_p var='change_this_image'}</a>
                    </p>
                </div>
                {/if}
                <div id="js_subscribe_upload_image"{if $bIsEdit && !empty($aForms.image_path)} style="display:none;"{/if}>
                    <input type="file" id="image" name="image" accept="image/*" size="20" />
                    <p class="help-block">
                        {_p var='you_can_upload_a_jpg_gif_or_png_file'}
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="user_group_id">{required}{_p var='user_group_on_success'}</label>
                <select class="form-control" name="val[user_group_id]" id="user_group_id" required>
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aUserGroups item=aUserGroup}
                    <option value="{$aUserGroup.user_group_id}"{value type='select' id='user_group_id' default=$aUserGroup.user_group_id}>{$aUserGroup.title|convert|clean}</option>
                    {/foreach}
                </select>
                <p class="help-block">
                    {_p var='once_a_user_successfully_purchased_the_package_they_will_be_moved_to_this_user_group'}
                </p>
            </div>
            <div class="form-group">
                <label for="fail_user_group">{required}{_p var='user_group_on_failure'}</label>
                <select name="val[fail_user_group]" id="fail_user_group" required class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aUserGroups item=aUserGroup}
                    <option value="{$aUserGroup.user_group_id}"{value type='select' id='fail_user_group' default=$aUserGroup.user_group_id}>{$aUserGroup.title|convert|clean}</option>
                    {/foreach}
                </select>
                <p class="help-block">
                    {_p var='once_a_user_cancels_or_fails_to_pay_their_subscription_they_will_be_moved_to_this_user_group'}
                </p>
            </div>
            <div class="form-group form-group-follow">
                <label for="is_registration">{_p var='add_to_registration'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_registration]" value="1" id="is_registration" {value type='radio' id='is_registration' default='1'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_registration]" value="0" id="is_registration" {value type='radio' id='is_registration' default='0' selected='true'}/> {_p var='no'}</span>
                </div>
            </div>
            <div class="form-group form-group-follow">
                <label for="is_active">{_p var='is_active'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" id="is_active" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" id="is_active" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
                </div>
            </div>
            <hr />
            <h4>{_p var='subscription_costs'}</h4>
            <div class="form-group form-group-follow">
                <label for="show_price">{_p var='show_price'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" id="show_price" name="val[show_price]" value="1" {value type='radio' id='show_price' default='1' selected='true'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" id="show_price" name="val[show_price]" value="0" {value type='radio' id='show_price' default='0'}/> {_p var='no'}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="">{_p var='price'}</label>
                {module name='core.currency' currency_field_name='val[cost]'}
            </div>
            <div class="form-group form-group-follow">
                <label for="is_recurring">{_p var='recurring'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" id="is_recurring" name="val[is_recurring]" value="1" {value type='radio' id='is_recurring' default='1'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" id="is_recurring" name="val[is_recurring]" value="0" {value type='radio' id='is_recurring' default='0' selected='true'}/> {_p var='no'}</span>
                </div>
            </div>
            <div class="js_subscribe_is_recurring">
                <div class="form-group">
                    <label for="">{_p var='recurring_price'}</label>
                    {module name='core.currency' currency_field_name='val[recurring_cost]'}
                </div>
                <div class="form-group">
                    <label for="recurring_period">{_p var='recurring_period'}</label>
                    <select name="val[recurring_period]" id="recurring_period" class="form-control">
                        <option value="">{_p var='select'}:</option>
                        <option value="1"{value type='select' id='recurring_period' default='1'}>{_p var='monthly'}</option>
                        <option value="2"{value type='select' id='recurring_period' default='2'}>{_p var='quarterly'}</option>
                        <option value="3"{value type='select' id='recurring_period' default='3'}>{_p var='biannualy'}</option>
                        <option value="4"{value type='select' id='recurring_period' default='4'}>{_p var='annually'}</option>
                    </select>
                </div>
            </div>
            <div class="js_background_color">
                <div class="form-group">
                    <label for="background_color">{_p var='background_color_for_the_comparison_page'}</label>
                    <input type="text" name="val[background_color]" id="background_color" value="{value id='background_color' type='input'}" size="40" placeholder="#RRGGBB" class="form-control"/>
                    <div class="help-block">
                        Color format #RRGGBB, example: #802010
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>
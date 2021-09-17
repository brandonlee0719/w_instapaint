<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($sMessage)}
<div class="message">{$sMessage}</div>
{else}

<form method="post" id="frmNewsletter" action="{url link='admincp.newsletter.add'}" onsubmit="$(this).find('.btn_submit').prop('disabled', true);">
    {if $bIsEdit}
    <input type="hidden" name="newsletter_id" value="{$aForms.newsletter_id}">
    {/if}
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label for="archive">{_p var='archive'}:</label>
                <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active">
                            <input class='form-control' type="radio" name="val[archive]" value="1" {value type='radio' id='archive' default='1'}/> {_p var='yes'}
                        </span>
                    <span class="js_item_active item_is_not_active">
                            <input class='form-control' type="radio" name="val[archive]" value="0" {value type='radio' id='archive' default='0' selected='true'}/> {_p var='no'}
                        </span>
                </div>
                <div class="help-block">{_p var='newsletter_archive_description'}</div>
            </div>
            <div class="form-group">
                <label for="archive">{_p var='override_privacy'}:</label>
                <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active">
                            <input class='form-control' type="radio" name="val[privacy]" value="1" {value type='radio' id='privacy' default='1'}/> {_p var='yes'}
                        </span>
                    <span class="js_item_active item_is_not_active">
                            <input class='form-control' type="radio" name="val[privacy]" value="0" {value type='radio' id='privacy' default='0' selected='true'}/> {_p var='no'}
                        </span>
                </div>
                <div class="help-block">{_p var='newsletter_override_privacy_description'}</div>
            </div>
            <div class="form-group">
                <label for="archive">{_p var='run_immediately'}:</label>
                <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active">
                            <input class='form-control' type="radio" name="val[run_now]" value="1" {value type='radio' id='run_now' default='1' selected='true'}/> {_p var='yes'}
                        </span>
                    <span class="js_item_active item_is_not_active">
                            <input class='form-control' type="radio" name="val[run_now]" value="0" {value type='radio' id='run_now' default='0'}/> {_p var='no'}
                        </span>
                </div>
                <div class="help-block">{_p var='newsletter_run_immediately_description'}</div>
            </div>

            <div class="form-group">
                <label for="">{_p var='user_groups'}:</label>
                <select class="form-control" name="val[is_user_group]" id="js_is_user_group">
                    <option value="1"{value type='select' id='is_user_group' default='1'}>{_p var='all_user_groups'}</option>
                    <option value="2"{value type='select' id='is_user_group' default='2'} {if !empty($aAccess)}selected="true"{/if}>{_p var='selected_user_groups'}</option>
                </select>
                <div class="p_4" style="display:none;" id="js_user_group">
                    {foreach from=$aUserGroups item=aUserGroup}
                    <div class="p_4">
                        <label><input type="checkbox" name="val[user_group][]" value="{$aUserGroup.user_group_id}"{if isset($aAccess) && is_array($aAccess)}{if in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
                    </div>
                    {/foreach}
                </div>
            </div>
            <div class="form-group">
                <label>{_p var='location'}:</label>
                {select_location value_title='phrase var=core.any' class='form-control'}
            </div>
            <div class="form-group">
                <label>{_p var='gender'}:</label>
                {select_gender value_title='phrase var=core.any' class='form-control'}
            </div>
            <div class="form-group">
                <label for="age_from">{_p var='age_group_between'}:</label>
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control" name="val[age_from]" id="age_from">
                            <option value="">{_p var='all'}</option>
                            {foreach from=$aAge item=iAge}
                            <option value="{$iAge}"{value type='select' id='age_from' default=$iAge}>{$iAge}</option>
                            {/foreach}
                        </select>
                    </div>
                    <label>{_p var='and'}</label>
                    <div class="form-group">
                        <select class="form-control" name="val[age_to]" id="age_to">
                            <option value="">{_p var='all'}</option>
                            {foreach from=$aAge item=iAge}
                            <option value="{$iAge}"{value type='select' id='age_to' default=$iAge}>{$iAge}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="total">{required}{_p var='how_many_per_round'}:</label>
                <input class="form-control" type="text" name="val[total]" value="{value type='input' id='total' default='50'}" id="total" size="40" maxlength="150" />
            </div>

            <div class="form-group">
                <label for="subject">{required}{_p var='subject'}:</label>
                <input class="form-control" type="text" name="val[subject]" value="{value type='input' id='subject'}" id="subject" size="40" maxlength="150" />
            </div>

            <div class="form-group">
                <label for="text">{required}{_p var='html_text'}:</label>
                {editor id='text' rows='15' class='form-control'}
            </div>

            <div class="form-group">
                <label>{_p var='plain_text'}:</label>
                <textarea class="form-control" name="val[txtPlain]" id="txtPlain" cols="50" rows="15">{value type='textarea' id='txtPlain'}</textarea>
                <a href="javascript:void(0);" onclick="$Core.Newsletter.showPlain(); return false;">{_p var='get_plain_text_from_html'}</a>
            </div>

            <div class="help-block">
                {_p var='keyword_substitutions'}:
                <ul>
                    <li>{_p var='123_full_name_125_recipient_s_full_name'}</li>
                    <li>{_p var='123_user_name_125_recipient_s_user_name'}</li>
                    <li>{_p var='123_site_name_125_site_s_name'}</li>
                </ul>
            </div>
        </div>
        <div class="panel-footer">
            <input type="button" value="{if $bIsEdit}{_p var='edit_newsletter'}{else}{_p var='add_newsletter'}{/if}" class="btn btn-primary btn_submit" onclick="$Core.Newsletter.checkText();" />
        </div>
    </div>

</form>
{/if}

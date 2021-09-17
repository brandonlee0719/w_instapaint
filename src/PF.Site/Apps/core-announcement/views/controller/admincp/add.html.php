<?php 
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package 		Phpfox
 * @version 		$Id: add.html.php 4493 2012-07-10 15:07:29Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!'); 

?>
<form action="" method="post" onsubmit="$(this).find('.btn_submit').prop('disabled', true)">
    <div class="panel-group">
        {if $bIsEdit}
            <input type="hidden" name="announcement_id" value="{$iEditId}" />
            <input type="hidden" name="val[subject]" value="{$aAnnouncement.subject_var}" />
            <input type="hidden" name="val[intro]" value="{$aAnnouncement.intro_var}" />
            <input type="hidden" name="val[content]" value="{$aAnnouncement.content_var}" />
        {/if}
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title">{if $bIsEdit}{_p var='edit_announcement_title' title=$aAnnouncement.subject_var|convert}{else}{_p var='add_an_announcement'}{/if}</div></div>
            <div class="panel-body">
                <!-- Field Subject -->
                <div class="form-group">
                    {field_language phrase='subject_var' label='subject' field='subject' format='val[subject_' size=30 maxlength=255 required=true}
                </div>
                <!-- Field Intro -->
                <div class="form-group">
                    {field_language phrase='intro_var' label='intro' field='intro' format='val[intro_' type='textarea' rows=5 maxlength=500 required=true}
                    <p class="help-block">{_p var='maximum_is_number_characters' number=500}</p>
                </div>
                <!-- Field Content -->
                <div class="form-group">
                    <label for="content_{$aDefaultLanguage.language_id}">{_p var='announcement_content'} {_p var='in__l'} {$aDefaultLanguage.title}</label>
                    {assign var='value_name' value="content_"$aDefaultLanguage.language_id}
                        {editor id=$value_name rows='15' class='form-control'}
                    {if count($aOtherLanguages)}
                    <div class="clearfix collapse-placeholder">
                        <a role="button" data-cmd="core.toggle_placeholder">{_p var='announcement_content'} {_p var='in_other_languages'}</a>
                        <div class="inner">
                            <p class="help-block">{_p var='left_empty_will_have_same_value_with_default_language'}</p>
                            {foreach from=$aOtherLanguages item=aLanguage}
                            {assign var='value_name' value="content_"$aLanguage.language_id}
                            <div class="form-group">
                                <label for="{$value_name}"><strong>{$aLanguage.title}</strong>:</label>
                                {editor id=$value_name rows='15' class='form-control'}
                            </div>
                            {/foreach}
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title">{_p var='display_options'}</div></div>
            <div class="panel-body">
                <!-- Field Active -->
                <div class="form-group">
                    <label for="archive">{_p var='active'}:</label>
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active">
                            <input class='form-control' type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}
                        </span>
                        <span class="js_item_active item_is_not_active">
                            <input class='form-control' type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="style">{_p var='style'}</label>
                    <select name="val[style]" id="style" class="form-control">
                        {foreach from=$aDefaultStyle item=aStyle}
                            <option value="{$aStyle}" {value type="select" id="style" default=$aStyle}>{$aStyle}</option>
                        {/foreach}
                    </select>
                </div>
                <!-- Field Can Be Closed -->
                <div class="form-group">
                    <label for="archive">{_p var='can_be_closed'}:</label>
                    <div class="item_is_active_holder">
						<span class="js_item_active item_is_active">
							<input class='form-control' type="radio" name="val[can_be_closed]" value="1" {value type='radio' id='can_be_closed' default='1' selected='true'}/> {_p var='yes'}
                        </span>
                        <span class="js_item_active item_is_not_active">
							<input class='form-control' type="radio" name="val[can_be_closed]" value="0" {value type='radio' id='can_be_closed' default='0'}/> {_p var='no'}
                        </span>
                    </div>
                </div>
                <!-- Field Show In Dashboard -->
                <div class="form-group" style="display: none;">
                    <label for="archive">{required}{_p var='show_in_the_dashboard'}:</label>
                    <div class="item_is_active_holder">
						<span class="item_is_active">
							<input type="radio" name="val[show_in_dashboard]" value="1" {value type='radio' id='show_in_dashboard' default='1' selected='true'}/> {_p var='yes'}
                        </span>
						<span class=" item_is_not_active">
							<input type="radio" name="val[show_in_dashboard]" value="0" {value type='radio' id='show_in_dashboard' default='0'}/> {_p var='no'}
                        </span>
                    </div>
                </div>
                <!-- Field Show Author -->
                <div class="form-group" style="display: none;">
                    <label for="archive">{required}{_p var='show_author'}:</label>
                    <div class="item_is_active_holder">
						<span class="item_is_active">
							<input type="radio" name="val[user_id]" value="{$iUser}" id="show_author" {value type='radio' id='user_id' default=''$iUser'' selected='true'}/> {_p var='yes'}
						</span>
                        <span class=" item_is_not_active">
							<input type="radio" name="val[user_id]" value="0" id="show_author" {value type='radio' id='user_id' default='0' selected='true'}/> {_p var='no'}
						</span>
                    </div>
                </div>
                <!---->
                <div class="form-group">
                    <label for="">{_p var='start_date'}:</label>
                    {select_date id='announcement' prefix='announcement_start_' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='core.time_separator' class='form-control'}
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title">{_p var='target_viewers'}</div></div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="">{_p var='user_groups'}:</label>
                    <select class="form-control" name="val[is_user_group]" id="js_is_user_group">
                        <option value="1"{value type='select' id='is_user_group' default='1'}>{_p var='all_user_groups'}</option>
                        <option value="2"{value type='select' id='is_user_group' default='2'} {if !empty($aAccess)}selected="true"{/if}>{_p var='selected_user_groups'}</option>
                    </select>
                    <div style="display:none;" id="js_user_group">
                        {foreach from=$aUserGroups item=aUserGroup}
                        <div class="checkbox">
                            <label><input type="checkbox" name="val[user_group][]" value="{$aUserGroup.user_group_id}"{if !empty($aAccess) && is_array($aAccess)}{if in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
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
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-footer">
            <input type="submit" value="{if !empty($bIsEdit)}{_p var='edit_announcement'}{else}{_p var='add_announcement'}{/if}" class="btn btn-primary btn_submit" />
        </div>
    </div>
</form>

{literal}
<script>
    $Behavior.init_add_announcement = function () {
        var $intro = $('textarea[id^=intro]');
        if ($intro.length) {
            $intro.prop('maxlength', 500);
        }
    }
</script>
{/literal}

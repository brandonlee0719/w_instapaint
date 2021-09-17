<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}
<form class="form" method="post" action="{url link='admincp.ad.add'}" enctype="multipart/form-data" onsubmit="{$sGetJsForm}">
    <div class="panel panel-default">
        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="val[type_id]" value="{$aForms.type_id}" id="type_id" /></div>
            <div><input type="hidden" name="id" value="{$aForms.ad_id}" /></div>
            {/if}
            <div class="form-group">
                <label for="type_id">{required}{_p var='banner_type'}</label>
                {if $bIsEdit}
                {if $aForms.type_id == 1}
                {_p var='image'}
                {elseif $aForms.type_id == 2}
                {_p var='html'}
                {/if}

                {else}
                <select name="val[type_id]" id="type_id" class="form-control" required>
                    <option value="">{_p var='select'}:</option>
                    <option value="1"{value type='select' id='type_id' default='1'}>{_p var='image'}</option>
                    <option value="2"{value type='select' id='type_id' default='2'}>{_p var='html'}</option>
                </select>
                {/if}
            </div>

            {if $bIsEdit && $aForms.location == 50}
            <div>
                <input type="hidden" name="val[html_code]" value="{value type='input' id='html_code'}" size="25" maxlength="25" id="html_body" />
            </div>
            <div class="form-group">
                <label for="c_ad_title">{_p var='title'}</label>
                <input class="form-control" type="text" name="val[c_ad_title]" value="{value type='input' id='c_ad_title'}" size="25" maxlength="25" id="c_ad_title" />
            </div>
            <div class="form-group">
                <label for="c_ad_body">{_p var='body_text'}</label>
                <input class="form-control" type="text" name="val[c_ad_body]" value="{value type='input' id='c_ad_body'}" size="50" id="c_ad_body" />
            </div>
            {else}
            <div class="form-group js_add_hidden" id="js_type_html" style="display:none;">
                <label for="html_code">{required}{_p var='html'}</label>
                <textarea class="form-control" required name="val[html_code]" rows="8" id="html_code">{value type='textarea' id='html_code'}</textarea>
                <p class="help-block">
                    <a href="#" onclick="$Core.popup('{url link='ad.preview'}', {literal}{{/literal}scrollbars: 'yes', location: 'no', menubar: 'no', width: 900, height: 400, resizable: 'yes', center: true{literal}}{/literal}); return false;">{_p var='preview_this_ad'}</a>
                </p>
            </div>
            {/if}

            <div class="form-group js_add_hidden" id="js_type_image" style="display:none;">
                <label for="image">{required}{_p var='banner_image'}</label>
                {if $bIsEdit}
                <div id="js_ad_banner">
                    {img file=$aForms.image_path path='ad.url_image' server_id=$aForms.server_id}
                    <p class="help-block">
                        {_p var='click_here_to_change_this_banner_image'}
                    </p>
                </div>
                {/if}
                <div id="js_ad_upload_banner" {if $bIsEdit} style="display:none;"{/if} >
                    <input class="form-control" required type="file" accept="image/*" name="image" size="30" />{if $bIsEdit} - <a href="#" onclick="$('#js_ad_upload_banner').hide(); $('#js_ad_banner').show(); return false;">{_p var='cancel'}</a>{/if}
                    <p class="help-block">{_p var='you_can_upload_a_jpg_gif_or_png_file'}</p>
                </div>
            </div>
            <div class="form-group">
                <label for="url_link" class="required">{_p var='banner_link'}</label>
                <input class="form-control" required type="text" name="val[url_link]" value="{value type='input' id='url_link'}" id="url_link" size="40" />
            </div>
        </div>
    </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">{_p var='campaign_details'}</div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="name" class="required">{_p var='campaign_name'}</label>
                    <input class="form-control" required type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="40" maxlength="150" />
                </div>
                <div class="form-group">
                    <label for="">{_p var='start_date'}</label>
                    {select_date prefix='start_' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='core.time_separator'}
                    <p class="help-block">{_p var='note_the_time_is_set_to_your_registered_time_zone'}</p>
                </div>
                <div class="form-group">
                    <label for="">{_p var='end_date'}</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="val[end_option]" value="0" checked="checked" class="v_middle end_option" {value type='radio' id='end_option' default='0'}/>
                            {_p var='do_not_end_this_campaign'}
                        </label> <br />
                        <label>
                            <input type="radio" name="val[end_option]" value="1" class="v_middle end_option" {value type='radio' id='end_option' default='1'}/>
                            {_p var='end_on_a_specific_date'}
                        </label>
                    </div>
                    <div style="display:none;" id="js_end_option">
                        <div class="form-inline">
                            {select_date prefix='end_' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+10' time_separator='core.time_separator'}
                            <p class="help-block">
                                {_p var='note_the_time_is_set_to_your_registered_time_zone'}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="total_view">{_p var='total_views'}</label><br>
                    <input type="text" name="val[total_view]" value="{value type='input' id='total_view'}" id="total_view" class="{if (isset($aForms) && isset($aForms.view_unlimited)) || (!isset($aForms))}disabled {/if}v_middle" size="10"{if (isset($aForms) && isset($aForms.view_unlimited)) || (!isset($aForms))} disabled="disabled" {/if}/>
                    <label class="radio-inline">
                        <input type="checkbox" name="val[view_unlimited]" id="view_unlimited" class="v_middle"{if (isset($aForms) && isset($aForms.view_unlimited)) || (!isset($aForms))} checked="checked" {/if}/> {_p var='unlimited'}
                    </label>
                </div>

                <div class="form-group" id="js_total_click" style="display:none;">
                    <label for="total_click">{_p var='total_clicks'}</label><br>
                    <input type="text" name="val[total_click]" value="{value type='input' id='total_click'}" id="total_click" class="{if (isset($aForms) && isset($aForms.click_unlimited)) || (!isset($aForms))}disabled {/if}v_middle" size="10"{if (isset($aForms) && isset($aForms.click_unlimited)) || (!isset($aForms))} disabled="disabled" {/if}/>
                    <label class="radio-inline">
                        <input type="checkbox" name="val[click_unlimited]" id="click_unlimited" class="v_middle"{if (isset($aForms) && isset($aForms.click_unlimited)) || (!isset($aForms))} checked="checked" {/if}/> {_p var='unlimited'}
                    </label>
                </div>

                {if isset($aForms.is_custom) && $aForms.is_custom == '2'}
                <div><input type="hidden" name="val[is_active]" value="1" /></div>
                {else}
                <div class="form-group">
                    <label for="">{_p var='active'}</label>
                    <div class="radio">
                        <label><input type="radio" name="val[is_active]" value="1"{value type='radio' id='is_active' default='1' selected=true}/> {_p var='yes'}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="val[is_active]" value="0"{value type='radio' id='is_active' default='0'}/> {_p var='no'}</label>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='placement'}</div>
        </div>
        <div class="panel-body">
            {if $bIsEdit && $aForms.location == 50}
            <div style="display:none;">
                {/if}
                {module name='admincp.module.form' module_form_title='ad.module_placement' module_form_required=false module_form_value='All Modules' module_form_id='module_access'}
                {if $bIsEdit && $aForms.location == 50}
            </div>
            {/if}
            <div class="form-group"{if $bIsEdit && $aForms.location == 50} style="display:none;"{/if}>
                <label for="location">{_p var='placement'}</label>
                {if $bIsEdit && $aForms.location == 50}
                <input class="form-control" type="hidden" name="val[location]" value="50" />
                {else}
                <select name="val[location]" id="location" class="form-control">
                    <optgroup label="Blocks">
                        {for $i = 1; $i <= 12; $i++}
                        <option value="{$i}"{value type='select' id='location' default=$i}>{_p var='block_location_x' x=$i}</option>
                        {/for}
                        {if $bIsEdit && Phpfox::getParam('ad.multi_ad')}
                        <option value="50"{value type='select' id='location' default=50}>{_p var='block_location_x' x=50}</option>
                        {/if}
                    </optgroup>
                    <optgroup label="Specific Locations">
                        <option value="photo_theater"{if $bIsEdit && $aForms.location == 'photo_theater'} selected="selected"{/if}>Photo Theater Mode</option>
                    </optgroup>
                    <optgroup label="Component Block">
                        {foreach from=$aComponents key=sName item=aComponent}
                        <option value="{$sName}" style="font-weight:bold;"{value type='select' id='m_connection' default=$sName}>{$sName|translate:'module'}</option>
                        {foreach from=$aComponent item=aComp}
                        <option value="{$sName}|{$aComp.component}"{value type='select' id='component' default=''$sName'|'$aComp.component''}>-- {$aComp.component}</option>
                        {/foreach}
                        {/foreach}
                    </optgroup>
                </select>
                <a href="#?call=ad.sample&amp;width=scan&amp;click=1" class="inlinePopup" title="{_p var='sample_layout'}">{_p var='view_site_layout'}</a>
                <p class="help-block">{_p var='notice_the_ad_sizes_provided_is_a_recommendation'}</p>
                {/if}
            </div>
            <div class="form-group">
                <label for="">{_p var='disallow_controller'}</label>
                <input class="form-control" type="text" name="val[disallow_controller]" value="{value type='input' id='disallow_controller'}" id="name" size="40" />
                <p class="help-block">{_p var='separate_each_controller_with_a_comma_eg_blog_index_video_view'}</p>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='audience'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="js_is_user_group">{_p var='user_groups'}</label>
                <select name="val[is_user_group]" id="js_is_user_group" class="form-control">
                    <option value="1"{value type='select' id='is_user_group' default='1'}>{_p var='all_user_groups'}</option>
                    <option value="2"{value type='select' id='is_user_group' default='2'}>{_p var='selected_user_groups'}</option>
                </select>
                <div class="p_4" style="display:none;" id="js_user_group">
                    {foreach from=$aUserGroups item=aUserGroup}
                    <div class="p_4 checkbox">
                        <label><input type="checkbox" name="val[user_group][]" value="{$aUserGroup.user_group_id}"{if isset($aAccess) && is_array($aAccess)}{if in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
                    </div>
                    {/foreach}
                </div>
            </div>
            <div class="form-group">
                <label for="country_iso_custom">{_p var='location'}</label>
                {if isset($aAllCountries)}
                <select multiple="multiple" name="val[country_iso_custom][]" id="country_iso_custom" class="form-control">
                    <option value="">{_p var='any'}</option>
                    {foreach from=$aAllCountries key=sIso item=aCountry}
                    <option value="{$sIso}" {if isset($aForms) && isset($aForms.countries_list)}{foreach from=$aForms.countries_list item=sChosen} {if $sChosen == $sIso} selected="selected" {/if}{/foreach}{/if}> {$aCountry.name}</option>
                    {/foreach}
                </select>
                {else}
                {select_location value_title='phrase var=core.any' multiple=1 name='country_iso_custom'}
                {/if}
            </div>

            {if Phpfox::getParam('ad.advanced_ad_filters')}
            <div class="form-group tbl_province" style="display:none;">
                <label for="sct_country_{$aCountry.country_iso}">{_p var='states_provinces'}</label>
                {foreach from=$aAllCountries item=aCountry}
                {if is_array($aCountry.children) && !empty($aCountry.children)}
                <div id="country_{$aCountry.country_iso}" class="select_child_country" style="display:none;">
                    <div>{$aCountry.name}</div>
                    <select class="sct_child_country form-control" id="sct_country_{$aCountry.country_iso}" name="val[child_country][{$aCountry.country_iso}][]" multiple="multiple">
                        {foreach from=$aCountry.children item=aChild}
                        <option value="{$aChild.child_id}" data-id="{$aChild.child_id}">{$aChild.name_decoded}</option>
                        {/foreach}
                    </select>
                </div>
                {/if}
                {/foreach}
            </div>

            <div class="form-group">
                <label for="postal_code">{_p var='postal_code'}</label>
                <input class="form-control" type="text" name="val[postal_code]" id='postal_code' value="{value type='input' id='postal_code'}">
                <p class="help-block">{_p var='separate_multiple_postal_codes_by_a_comma'}</p>
            </div>

            <div class="form-group">
                <label for="city_location">{_p var='city'}</label>
                <input class="form-control" type="text" name="val[city_location]" id='city_location' value="{value type='input' id='city_location'}">
                <p class="help-block">{_p var='separate_multiple_cities_by_a_comma'}</p>
            </div>
            {/if}

            <div class="form-group">
                <label for="gender">{_p var='gender'}</label>
                {select_gender value_title='phrase var=core.any'}
            </div>
            <div class="form-group">
                <label for="age_from">{_p var='age_group_between'}</label>
                    <select name="val[age_from]" id="age_from" class="form-control">
                        <option value="">{_p var='any'}</option>
                        {foreach from=$aAge item=iAge}
                        <option value="{$iAge}"{value type='select' id='age_from' default=$iAge}>{$iAge}</option>
                        {/foreach}
                    </select>
                    <span id="js_age_to">
				    and
                    <select name="val[age_to]" id="age_to" class="form-control">
                        <option value="">{_p var='any'}</option>
                        {foreach from=$aAge item=iAge}
                            <option value="{$iAge}"{value type='select' id='age_to' default=$iAge}>{$iAge}</option>
                        {/foreach}
                    </select>
                    </span>
            </div>

            {if isset($aForms.is_custom) && $aForms.is_custom == '2'}
            <input type="submit" value="{_p var='approve'}" class="btn btn-primary" name="val[approve]"/>
            <input type="submit" value="{_p var='deny'}" class="btn btn-danger" name="val[deny]" />
            {else}
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            {/if}
        </div>
    </div>
</form>
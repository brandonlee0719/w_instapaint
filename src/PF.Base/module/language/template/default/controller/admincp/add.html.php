<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Phpfox
 * @version          $Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($bImportingPhrases)}
<div class="alert alert-warning">
    {_p var='importing_phrases_page_current_total' current=$iCurrentPage total=$iTotalPages}
</div>
{else}
<form class="form" method="post" action="{url link='admincp.language.add'}" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            Edit Language
        </div>
        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.language_id}"/></div>
            {/if}
            {if !$bIsEdit}
            <div class="form-group">
                <label for="parent_id">{required}{_p var='create_from'}</label>
                <select name="val[parent_id]" class="form-control" id="parent_id" autofocus>
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aLanguages item=aLanguage}
                    <option value="{$aLanguage.language_id}">{$aLanguage.title|clean}</option>
                    {/foreach}
                </select>
            </div>
            {/if}
            <div class="form-group">
                <label for="title">{required}{_p var='name'}</label>
                <input type="text" name="val[title]" id="title" value="{value type='input' id='title'}" size="40"
                       class="form-control"/>
            </div>
            <div class="form-group">
                <label for="language_code">{required}{_p var='language_abbreviation_code'}</label>
                <input type="text" name="val[language_code]" id="language_code"
                       value="{value type='input' id='language_code'}" size="2" maxlength="2" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="direction">{required}{_p var='text_direction'}</label>
                <div class="radio">
                    <label><input type="radio" name="val[direction]" value="ltr" {value type='radio' id='direction'
                                  default='ltr' selected=true}/> {_p var='left_to_right'}</label> <br/>
                    <label><input type="radio" name="val[direction]" value="rtl" {value type='radio' id='direction'
                                  default='rtl' }/> {_p var='right_to_left'}</label>
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group-follow hidden">
                <div class="table_left">
                    {required}{_p var='allow_user_selection'}:
                </div>
                <div class="table_right">
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active"><input type="radio" name="val[user_select]"
                                                                           value="1" {value type='radio'
                                                                           id='user_select' default='1' selected='true'
                                                                           }/> {_p var='yes'}</span>
                        <span class="js_item_active item_is_not_active"><input type="radio" name="val[user_select]"
                                                                               value="0" {value type='radio'
                                                                               id='user_select' default='0' }/> {_p var='no'}</span>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {_p var='icon'}:
                </div>
                <div class="table_right">
                    {if $bIsEdit && !empty($aForms.image)}
                    <div id="js_current_image">
                        <img src="{$aForms.image}" alt="" class="v_middle" width="32px"/> - <a href="#"
                                                                                               onclick="$('#js_current_image').hide(); $('#js_upload_new_icon').show();">{_p
                            var='change_icon'}</a>
                    </div>
                    {/if}
                    <div id="js_upload_new_icon" {if $bIsEdit && !empty($aForms.image)} style="display:none;" {
                    /if}>
                    <input type="file" name="icon" size="30"/>
                    {if $bIsEdit}
                    - <a href="#" onclick="$('#js_current_image').show(); $('#js_upload_new_icon').hide();">{_p
                        var='cancel'}</a>
                    {/if}
                    <div class="extra_info">
                        {_p
                        var='default_icon_to_represent_this_language_package_br_advised_size_is_max_16_pixels_width_height'}
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="table form-group" style="display:none;">
        </div>
        <div class="form-group form-group-follow hidden">
            <label for="user_select">{required}{_p var='allow_user_selection'}</label>
            <div class="item_is_active_holder radio">
                <span class="js_item_active item_is_active"><input type="radio" name="val[user_select]" value="1" {value
                                                                   type='radio' id='user_select' default='1'
                                                                   selected='true' }/> {_p var='yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[user_select]" value="0"
                                                                       {value type='radio' id='user_select' default='0'
                                                                       }/> {_p var='no'}</span>
            </div>
        </div>
    <div class="form-group">
        <label for="version">{_p var='version'} ({_p var="for developer only"})</label>
        <input type="text" name="val[version]" id="version"
               value="{value type='input' id='version'}" class="form-control"/>
    </div>
    <div class="form-group">
        <label for="store_id">{_p var='store_id'} ({_p var="for developer only"})</label>
        <input type="text" name="val[store_id]" id="store_id"
               value="{value type='input' id='store_id'}" class="form-control"/>
    </div>
    <div class="form-group" style="display:none;">
        <label for="created">{_p var='created_by'}</label>
        <input type="text" name="val[created]" id="created" value="{value type='input' id='created'}" size="40"/>
    </div>
    <div class="form-group" style="display:none;">
        <label for="site">{_p var='website'}</label>
        <input type="text" name="val[site]" id="site" value="{value type='input' id='site'}" size="40"/>
    </div>
    </div>
    <div class="panel-footer">
        <input type="submit" value="{_p var='submit'}" class="btn btn-primary"/>
    </div>
    </div>
</form>
{/if}
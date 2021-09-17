<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if $bIsEdit && $aForms.view_id == '2'}
<div class="error_message">
    {_p var='notice_this_listing_is_marked_as_sold'}
</div>
{/if}

{$sCreateJs}
<form method="post" class="form" action="{url link='current'}" enctype="multipart/form-data" onsubmit="$('#js_marketplace_submit_form').attr('disabled',true); return startProcess({$sGetJsForm}, false);" id="js_marketplace_form">
    {if $bIsEdit}
    <input type="hidden" name="id" value="{$aForms.listing_id}" />
    {/if}
    <div id="js_custom_privacy_input_holder">
        {if $bIsEdit && Phpfox::isModule('privacy')}
        {module name='privacy.build' privacy_item_id=$aForms.listing_id privacy_module_id='marketplace'}
        {/if}
    </div>
    <div><input type="hidden" name="page_section_menu" value="" id="page_section_menu_form" /></div>
    <div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
    <div><input type="hidden" name="val[current_tab]" value="" id="current_tab"></div>

    <div id="js_mp_block_detail" class="js_mp_block page_section_menu_holder market-app add" {if !empty($sActiveTab) && $sActiveTab != 'detail'}style="display:none;"{/if}>
        <div class="form-group">
            <label for="title">{required}{_p var='what_are_you_selling'}</label>
            <input class="form-control" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" maxlength="100" />
        </div>
        <div class="form-group">
            <div class="form-inline flex category">
                <div class="form-group category-left">
                    <label for="category">{required}{_p var='category'}</label>
                    {$sCategories}
                </div>
                <div class="form-group price-right">
                    <label for="price">{_p var='price'}</label>
                    {field_price price_name='price' currency_name='currency_id'}
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="mini_description">{_p var='short_description'}</label>
            <textarea id="mini_description" class="form-control" rows="1" name="val[mini_description]">{value type='textarea' id='mini_description'}</textarea>
        </div>

        <div class="form-group">
            <label for="description">{_p var='description'}</label>
            <div class="table_right">
                {editor id='description' rows='6'}
            </div>
        </div>

        {if Phpfox::getUserParam('marketplace.can_sell_items_on_marketplace')}
            <div class="form-group market-app create">
                <div class="privacy-block-content">
                    <div class="item_is_active_holder">
                        <span class="js_item_active item_is_active">
                            <input type="radio" name="val[is_sell]" value="1" class="checkbox" style="vertical-align:middle;" {value type='radio' id='is_sell' default='1'}/> {_p var='yes'}
                        </span>
                        <span class="js_item_active item_is_not_active">
                            <input type="radio" name="val[is_sell]" value="0" class="checkbox" style="vertical-align:middle;" {value type='radio' id='is_sell' default='0' selected='true'}/> {_p var='no'}
                        </span>
                    </div>
                    <div class="inner">
                        <label>{_p var='enable_instant_payment'}</label>
                        <div class="extra_info">
                            {_p var='if_you_enable_this_option_buyers_can_make_a_payment_to_one_of_the_payment_gateways_you_have_on_file_with_us_to_manage_your_payment_gateways_go_a_href_link_here_a' link=$sUserSettingLink}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group market-app create sold">
                <div class="privacy-block-content">
                    <div class="item_is_active_holder ">
                        <span class="js_item_active item_is_active"><input type="radio" name="val[auto_sell]" value="1" class="checkbox" style="vertical-align:middle;" {value type='radio' id='auto_sell' default='1' selected='true'}/> {_p var='yes'}</span>
                        <span class="js_item_active item_is_not_active"><input type="radio" name="val[auto_sell]" value="0" class="checkbox" style="vertical-align:middle;" {value type='radio' id='auto_sell' default='0'}/> {_p var='no'}</span>
                    </div>
                    <div class="inner">
                        <label>{_p var='auto_sold'}</label>
                        <div class="extra_info">
                            {_p var='if_this_is_enabled_and_once_a_successful_purchase_of_this_item_is_made'}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        <div class="form-group location">
            <div class="form-inline">
                <div class="form-group">
                    <label>{required}{_p var='location'}</label>
                    <div>
                        {select_location}
                        {module name='core.country-child'}
                    </div>
                </div>
                <div id="js_mp_add_city" {if !$bIsEdit} class="form-group" {/if}>
                    <div class="form-group">
                        <label for="city">{_p var='city'}</label>
                        <div>
                            <input class="form-control" type="text" name="val[city]" value="{value type='input' id='city'}" id="city" size="20" maxlength="200" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="postal_code">{_p var='zip_postal_code'}</label>
                        <div>
                            <input class="form-control" type="text" name="val[postal_code]" value="{value type='input' id='postal_code'}" id="postal_code" size="10" maxlength="20" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {if $bIsEdit && ($aForms.view_id == '0' || $aForms.view_id == '2')}
        <div class="form-group market-app create">
            <div class="privacy-block-content">
                <div class="item_is_active_holder {if !isset($aForms.view_id)}item_selection_not_active{else}{if $aForms.view_id}item_selection_active{else}item_selection_not_active{/if}{/if}">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[view_id]" value="2" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='view_id' default='2' selected=true}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[view_id]" value="0" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='view_id' default='0'}/> {_p var='no'}</span>
                </div>
                <div class="inner">
                    <label>{_p var='closed_item_sold'}</label>
                    <div class="extra_info">
                        {_p var='enable_this_option_if_this_item_is_sold_and_this_listing_should_be_closed'}
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {if Phpfox::isModule('privacy')}
        <div class="form-group">
        <label>{_p var='marketplace.control_who_can_see_this_listing'}</label>
        {module name='privacy.form' privacy_name='privacy' default_privacy='marketplace.display_on_profile'}
    </div>
    {/if}
        <div class="form-group footer">
        <button type="submit" class="btn btn-primary" id="js_marketplace_submit_form">{if $bIsEdit}{_p var='update'}{else}{_p var='submit'}{/if}</button>
    </div>
    </div>

    <div id="js_mp_block_customize" class="js_mp_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'customize'}style="display:none;"{/if}>
        {module name='marketplace.photo'}
    </div>

    <div id="js_mp_block_invite" class="js_mp_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'invite'}style="display:none;"{/if}>
        <div class="block">
            {if Phpfox::isModule('friend')}
            <div class="form-group">
                <label for="js_find_friend">{_p var='invite_friends'}</label>
                {if isset($aForms.listing_id)}
                <div id="js_selected_friends" class="hide_it"></div>
                {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.listing_id friend_module_id='marketplace'}
                {/if}
            </div>
            {/if}
            <div class="form-group invite-friend-by-email">
                <label for="emails">{_p var='invite_people_via_email'}</label>
                <input name="val[emails]" id="invite_people_via_email" class="form-control" data-component="tokenfield" data-type="email" >
                <p class="help-block">
                    {_p var='separate_multiple_emails_with_a_comma'}
                </p>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="val[invite_from]" value="1"> {_p var='send_from_my_own_address_semail' sEmail=$sMyEmail}
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="add_a_personal_message">{_p var='add_a_personal_message'}</label>
                <textarea rows="1" name="val[personal_message]" id="add_a_personal_message" class="form-control textarea-auto-scale" placeholder="{_p var='write_message'}"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary" name="invite_submit"/>
            </div>
        </div>
    </div>

    {if isset($aForms.listing_id) && $bIsEdit}
    <div id="js_mp_block_manage" class="js_mp_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'manage'}style="display:none;"{/if}>
        {module name='marketplace.list'}
    </div>
    {/if}
</form>
{section_menu_js}
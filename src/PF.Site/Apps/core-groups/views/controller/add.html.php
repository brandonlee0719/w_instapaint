<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bIsEdit}
<!-- START update group -->
<div id="js_groups_add_holder">
	<form class="form" method="post" action="{url link='groups.add'}?id={$aForms.page_id}" enctype="multipart/form-data">
		<div><input type="hidden" name="id" value="{$aForms.page_id}" /></div>
		<div><input type="hidden" name="val[category_id]" value="{value type='input' id='category_id'}" id="js_category_groups_add_holder" /></div>
        <div><input type="hidden" name="val[current_tab]" value="" id="current_tab"></div>

		<div id="js_groups_block_detail" class="js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'detail'}style="display:none;"{/if}>
            {if isset($aDetailErrors)}
            <div class="alert alert-danger">
                <strong>{_p var='error'}</strong>
                <ul>
                    {foreach from=$aDetailErrors item=sError}
                    <li>{$sError}</li>
                    {/foreach}
                </ul>
            </div>
            {/if}
			<div class="form-group">
                <label for="type_id">{_p var='Category'}</label>
                {if $aForms.is_app}
                    {_p var='App'}
                {else}
					<div class="groups_add_category form-group">
						<select name="val[type_id]" class="form-control inline" id="type_id">
						{foreach from=$aTypes item=aType}
							<option value="{$aType.type_id}"{value type='select' id='type_id' default=$aType.type_id}>
                                {if Phpfox::isPhrase($this->_aVars['aType']['name'])}
                                {_p var=$aType.name}
                                {else}
                                {$aType.name|convert}
                                {/if}
                            </option>
						{/foreach}
						</select>
					</div>
					<div class="groups_sub_category form-group">
						{foreach from=$aTypes item=aType}
							{if isset($aType.categories) && is_array($aType.categories) && count($aType.categories)}
								<div class="js_groups_add_sub_category form-inline" id="js_groups_add_sub_category_{$aType.type_id}"{if $aType.type_id != $aForms.type_id} style="display:none;"{/if}>
									<select name="js_category_{$aType.type_id}" class="form-control inline">
										<option value="">{_p var='select'}</option>
										{foreach from=$aType.categories item=aCategory}
										<option value="{$aCategory.category_id}" {value type='select' id='category_id' default=$aCategory.category_id}>
                                            {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                                            {_p var=$aCategory.name}
                                            {else}
                                            {$aCategory.name|convert}
                                            {/if}
                                        </option>
										{/foreach}
									</select>
								</div>
							{/if}
						{/foreach}
					</div>
                {/if}
			</div>

			<div class="table form-group">
				<label for="title">{_p var='Name'}</label>
                {if $aForms.is_app}
                <div><input type="hidden" name="val[title]" value="{$aForms.title|clean}" maxlength="64" size="40" /></div>
                <a href="{permalink module='apps' id=$aForms.app_id title=$aForms.title}">{$aForms.title|clean}</a>
                {else}
                <input type="text" name="val[title]" value="{value type='input' id='title'}" maxlength="64" size="40" class="form-control" id="title"/>
                {/if}
			</div>

			<div class="table_clear">
				<input type="submit" value="{_p var='Update'}" class="btn btn-primary"/>
			</div>
		</div>

		<div id="js_groups_block_url" class="block js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'url'}style="display:none;"{/if}>
			<div class="form-group">
				<label for="js_vanity_url_new">{_p var='Vanity url'}</label>
                <div>
                    <span class="help-block">{param var='core.path'}</span>
                    <input type="text" name="val[vanity_url]" value="{value type='input' id='vanity_url'}" size="20" id="js_vanity_url_new" class="form-control"/>
                </div>
			</div>

			<div class="table_clear" id="js_groups_vanity_url_button">
				<ul class="table_clear_button">
					<li>
						<div><input type="hidden" name="val[vanity_url_old]" value="{value type='input' id='vanity_url'}" size="20" id="js_vanity_url_old" /></div>
						<input type="button" value="{_p var='Check url'}" class="btn btn-primary" onclick="if ($('#js_vanity_url_new').val() != $('#js_vanity_url_old').val()) {l} $Core.processForm('#js_groups_vanity_url_button'); $($(this).parents('form:first')).ajaxCall('groups.changeUrl'); {r} return false;" />
					</li>
					<li class="table_clear_ajax"></li>
				</ul>
				<div class="clear"></div>
			</div>
		</div>

		<div id="js_groups_block_photo" class="js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'photo'}style="display:none;"{/if}>
            {if isset($aPhotoErrors)}
            <div class="alert alert-danger">
                <strong>{_p var='error'}</strong>
                <ul>
                    {foreach from=$aPhotoErrors item=sError}
                    <li>{$sError}</li>
                    {/foreach}
                </ul>
            </div>
            {/if}
			<div id="js_groups_block_customize_holder">
                <div class="form-group-follow">
                    {if $bIsEdit && !empty($aForms.image_path)}
                    {module name='core.upload-form' type='groups' current_photo=$aForms.image_path_200 id=$aForms.page_id}
                    {else}
                    {module name='core.upload-form' type='groups'}
                    {/if}
                </div>

                <div id="js_submit_upload_image" class="table_clear">
                    <input type="submit" value="{_p var='update_photo'}" class="btn btn-primary"/>
                </div>
			</div>
		</div>

		<div id="js_groups_block_info" class="js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'info'}style="display:none;"{/if}>
			{plugin call='groups.template_controller_add_1'}
			<div class="table form-group">
                {editor id='text'}
                <div class="pt-1">
                    <input type="submit" value="{_p var='Update'}" class="btn btn-primary"/>
                </div>
            </div>
		</div>

		<div id="js_groups_block_permissions" class="block js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'permissions'}style="display:none;"{/if}>
			<div id="privacy_holder_table">
				{if $bIsEdit}
				<div class="table form-group-follow hidden">
					<div class="table_left">
						{_p var='Group privacy'}:
					</div>
					<div class="table_right extra_info_custom">
						{module name='privacy.form' privacy_name='privacy' privacy_info='groups.control_who_can_see_this_page' privacy_no_custom=true}
						<div class="extra_info">
							{_p var='Group privacy information'}
						</div>
					</div>
				</div>
				{/if}

				{if $bIsEdit }
				<div class="table form-group">
					<div class="table_left">
						{_p('Groups privacy')}
					</div>
					<div class="table_right">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <label><input type="radio" name="val[reg_method]" id="reg_method" value="0" {if $aForms.reg_method == '0'} checked{/if}>&nbsp;<i class="fa fa-privacy fa-privacy-0"></i>&nbsp;{_p var='Public'}</label>
                                <div class="extra_info">{_p var="Anyone can see the group, its members and their posts."}</div>
                            </li>
                            <li class="list-group-item">
                                <label><input type="radio" name="val[reg_method]" id="reg_method" value="1" {if $aForms.reg_method == '1'} checked{/if}>&nbsp;<i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;{_p var='Closed'}</label>
                                <div class="extra_info">{_p var="Anyone can find the group and see who's in it. Only members can see posts."}</div>
                            </li>
                            <li class="list-group-item">
                                <label><input type="radio" name="val[reg_method]" id="reg_method" value="2" {if $aForms.reg_method == '2'} checked{/if}>&nbsp;<i class="fa fa-lock" aria-hidden="true"></i>&nbsp;{_p var='Secret'}</label>
                                <div class="extra_info">{_p var="Only members can find the group and see posts."}</div>
                            </li>
                        </ul>
					</div>
				</div>
				{/if}
                <div class="privacy-block-content">
				{foreach from=$aPermissions item=aPerm}
                    <div class="item-outer">
                        <div class="form-group">
                            <label>{$aPerm.phrase}</label>
                            <div>
                                <select name="val[perms][{$aPerm.id}]" class="form-control">
                                    <option value="1"{if $aPerm.is_active == '1'} selected="selected"{/if}>{_p var='Members only'}</option>
                                    <option value="2"{if $aPerm.is_active == '2'} selected="selected"{/if}>{_p var='Admins only'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
				{/foreach}
                </div>
				<div class="table_clear">
					<input type="submit" value="{_p var='Update'}" class="btn btn-primary"/>
				</div>
			</div>
		</div>

		<div id="js_groups_block_admins" class="js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'admins'}style="display:none;"{/if}>
			<div class="table form-group">
                {if Phpfox::getUserBy('profile_page_id')}
                    {_p var="Please login back as user to use this feature."}
                {else}
                    {module name='friend.search-small' input_name='admins' current_values=$aForms.admins}
                {/if}
			</div>

			<div class="table_clear">
				<input type="submit" value="{_p var='Update'}" class="btn btn-primary"/>
			</div>
		</div>

		<div id="js_groups_block_invite" class="js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'invite'}style="display:none;"{/if}>
			<div class="block">
                <div class="form-group">
                    <label for="js_find_friend">{_p var='invite_friends'}</label>
                    {if isset($aForms.page_id)}
                    <div id="js_selected_friends" class="hide_it"></div>
                    {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.page_id friend_module_id='groups' in_form=true}
                    {/if}
                </div>
                <div class="form-group invite-friend-by-email">
                    <label for="emails">{_p var='invite_people_via_email'}</label>
                    <input name="val[emails]" id="emails" class="form-control" data-component="tokenfield" data-type="email">
                    <p class="help-block">{_p var='separate_multiple_emails_with_a_comma'}</p>
                </div>
                <div class="form-group">
                    <label for="personal_message">{_p var='add_a_personal_message'}</label>
                    <textarea rows="1" name="val[personal_message]" id="personal_message" class="form-control textarea-auto-scale" placeholder="{_p var='write_message'}"></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary"/>
                </div>
			</div>
		</div>

		<div id="js_groups_block_widget" class="block js_groups_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'widget'}style="display:none;"{/if}>
			<div class="table form-group">
				<div class="groups_create_new_widget">
                    <a role="button" class="btn btn-primary" onclick="$Core.box('groups.widget', 700, 'page_id={$aForms.page_id}'); return false;">{_p var='Create new widget'}</a>
				</div>
                {if !empty($aBlockWidgets) && !empty($aMenuWidgets)}
                <p class="help-block">{_p var='drag_to_order_your_blocks'}</p>
                {/if}

                {if !empty($aBlockWidgets)}
                <div class="mt-2">
                    <label>{_p var='block_type'}</label>
                    <table class="table table-striped drag-drop-table" id="js_drag_drop_block_type_block" data-app="core_groups" data-action-type="init" data-action="init_drag" data-table="#js_drag_drop_block_type_block" data-ajax="groups.orderWidget">
                        <thead>
                        <tr>
                            <th style="width: 20px"></th>
                            <th>{_p var='title'}</th>
                            <th style="width: 20px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$aBlockWidgets item=aBlockWidget}
                        <tr id="js_groups_widget_{$aBlockWidget.widget_id}">
                            <td class="drag_handle" style="width: 30px; height: 30px;">
                                <input type="hidden" name="ordering[{$aBlockWidget.widget_id}]">
                            </td>
                            <td>{$aBlockWidget.title|clean}</td>
                            <td class="widget-actions">
                                <div class="dropdown">
                                    <a data-toggle="dropdown">
                                        <i class="fa fa-action"></i>
                                    </a>
                                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a role="button" onclick="$Core.box('groups.widget', 700, 'widget_id={$aBlockWidget.widget_id}'); return false;">
                                                <span class="ico ico-pencilline-o mr-1"></span>
                                                {_p var='edit'}
                                            </a>
                                        </li>
                                        <li class="item_delete">
                                            <a role="button" onclick="$Core.jsConfirm({l}{r}, function(){l} $.ajaxCall('groups.deleteWidget', 'widget_id={$aBlockWidget.widget_id}'); {r}, function(){l}{r}); return false;">
                                                <span class="ico ico-trash-alt-o mr-1"></span>
                                                {_p var='delete'}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>

                {/if}
                {if !empty($aMenuWidgets)}
                <div class="mt-2">
                    <label>{_p var='menu_type'}</label>
                    <table class="table table-striped drag-drop-table" id="js_drag_drop_block_type_menu" data-app="core_groups" data-action-type="init" data-action="init_drag" data-table="#js_drag_drop_block_type_menu" data-ajax="groups.orderWidget">
                        <thead>
                        <tr>
                            <th style="width: 20px"></th>
                            <th>{_p var='title'}</th>
                            <th>{_p var='menu_title'}</th>
                            <th>{_p var='url_title'}</th>
                            <th style="width: 20px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$aMenuWidgets item=aMenuWidget}
                        <tr>
                            <td class="drag_handle" style="width: 30px; height: 30px;">
                                <input type="hidden" name="ordering[{$aMenuWidget.widget_id}]">
                            </td>
                            <td>{$aMenuWidget.title|clean}</td>
                            <td>{$aMenuWidget.menu_title}</td>
                            <td>{$aMenuWidget.url_title}</td>
                            <td class="widget-actions">
                                <div class="dropdown">
                                    <a data-toggle="dropdown">
                                        <i class="fa fa-action"></i>
                                    </a>
                                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a role="button" onclick="$Core.box('groups.widget', 700, 'widget_id={$aMenuWidget.widget_id}'); return false;">
                                                <span class="ico ico-pencilline-o mr-1"></span>
                                                {_p var='edit'}
                                            </a>
                                        </li>
                                        <li class="item_delete">
                                            <a role="button" onclick="$Core.jsConfirm({l}{r}, function(){l} $.ajaxCall('groups.deleteWidget', 'widget_id={$aMenuWidget.widget_id}'); {r}, function(){l}{r}); return false;">
                                                <span class="ico ico-trash-alt-o mr-1"></span>
                                                {_p var='delete'}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                {/if}
                {if !count($aBlockWidgets) && !count($aMenuWidgets)}
                <div class="alert alert-info">{_p var='no_widget_found'}</div>
                {/if}
			</div>
		</div>
	</form>
</div>
<!-- END of edit group -->
{else}
<!-- START add group -->
    {if !Phpfox::getUserBy('profile_page_id')}
    <div id="js_groups_add_holder" class="item-container group-add">
        <div class="main_break"></div>
        {foreach from=$aTypes item=aType}
        <div class="group-item" data-app="core_groups" data-action="add_new_group" data-type-id="{$aType.type_id}" data-action-type="click">
            <div class="item-outer">
            <div class="group-photo"
                 {if !empty($aType.image_path)}
                    style="background-image: url('{img server_id=$aType.image_server_id path='core.path_actual' file=$aType.image_path suffix='_200' return_url=true}')"
                 {else}
                    style="background-image: url('{img path='core.path_actual' file='PF.Site/Apps/core-groups/assets/img/default-category/default_category.png' return_url=true}')"
                 {/if}
            >
                <div class="group-add-inner-link">
                    <div class="groups-add-info">
                        <span class="item-title">
                        {if Phpfox::isPhrase($this->_aVars['aType']['name'])}
                            {_p var=$aType.name}
                        {else}
                            {$aType.name|convert}
                        {/if}
                        </span>
                        <div class="item-number-group">
                            {if $aType.pages_count > 1}
                                {$aType.pages_count} {_p var='groups'}
                            {else}
                                {$aType.pages_count} {_p var='_group'}
                            {/if}
                        </div>
                    </div>
                    <a class="item-group-add" data-app="core_groups" data-action="add_new_group" data-type-id="{$aType.type_id}" data-action-type="click"><span class="ico ico-plus"></span></a>
                </div>
            </div>
        </div>
        </div>
        {/foreach}
        <div class="clear"></div>
    </div>
    {/if}
{/if}
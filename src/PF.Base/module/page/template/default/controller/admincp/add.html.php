<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}
<form method="post" style="display: none;" action="{url link='admincp.page.add'}" id="js_form_new_page" onsubmit="{$sGetJsForm}">
	<div><input type="hidden" name="val[attachment]" id="js_attachment" value="{value type='input' id='attachment'}" /></div>
	{if $bIsEdit}
	<div><input type="hidden" name="page_id" value="{$aForms.page_id}" /></div>
	<div><input type="hidden" name="val[old_url]" value="{$aForms.title_url}"/></div>
	<div><input type="hidden" name="val[menu_id]" value="{$aForms.menu_id}" /></div>
	{/if}
	<div><input type="hidden" name="val[module_id]" value="core"></div>
	<div><input type="hidden" name="val[product_id]" value="phpfox"></div>
	<div class="page_editor_data" style="bottom: auto">
		<div>
			<div class="panel panel-default">
              <div class="panel-body">
                  <div class="form-group">
                      <label for="page_title">{required}{_p var='page_title'}</label>
                      <input class="form-control" placeholder="{_p var='page_title'}" type="text" name="val[title]" id="title" value="{value type='input' id='title'}" size="40" onblur="if ($('#title_url').val() == '' && this.value != '') $.ajaxCall('page.admincp.addUrl', 'title=' + this.value);" tabindex="1" />
                      <div class="p_4" style="display:none;">
                          {_p var='phrase_from_language_package'}
                          <label><input type="radio" name="val[is_phrase]" id="is_phrase" value="1"{value type='radio' id='is_phrase' default='1'}/> {_p var='yes'}</label>
                          <label><input type="radio" name="val[is_phrase]" id="is_phrase" value="0"{value type='radio' id='is_phrase' default='0' selected=true}/> {_p var='no'}</label>
                      </div>
                      {help var='admincp.page_add_title'}
                  </div>
                  <div id="js_url_table" class="form-group"{if !$bIsEdit && !$bFormIsPosted} style="display:none;"{/if}>
                  <label for="title_url">{_p var='url_title'}</label>
                  <input class="form-control" type="text" name="val[title_url]" id="title_url" value="{value type='input' id='title_url'}" size="40" onblur="if($(this).val() != '') $.ajaxCall('page.admincp.checkUrl', 'title_url=' + this.value + '&old_url={if isset($aForms.title_url)}{$aForms.title_url}{/if}');"/>
                  <div class="help-block">{help var='admincp.page_add_title_url'}</div>
              </div>
                <div class="form-group">
                    <label for="keyword">{_p var='meta_keywords'}</label>
                    <input class="form-control" type="text" name="val[keyword]" id="keyword" value="{value type='input' id='keyword'}" size="40" tabindex="2" />
                    <div class="help-block">{help var='admincp.page_add_keyword'}</div>
                </div>
                <div class="form-group">
                    <label>{_p var='meta_keywords'}:</label>
                    <input class="form-control" type="text" name="val[keyword]" id="keyword" value="{value type='input' id='keyword'}" size="40" tabindex="2" />
                    <div class="help-block">{help var='admincp.page_add_keyword'}</div>
                </div>
                <div class="form-group">
                    <label>{_p var='meta_description'}:</label>
                    <textarea class="form-control" cols="35" rows="6" name="val[description]" id="description">{value type='textarea' id='description'}</textarea>
                    <div class="help-block">{help var='admincp.page_add_description'}</div>
                </div>
              </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    {_p var='options'}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="is_active">{_p var='active'}</label>
                        <div class="radio">
                            <label><input type="radio" name="val[is_active]" value="1"{value type='radio' id='is_active' default='1' selected=true}/> {_p var='yes'}</label>
                            <label><input type="radio" name="val[is_active]" value="0"{value type='radio' id='is_active' default='0'}/> {_p var='no'}</label>
                            {help var='admincp.page_add_is_active'}
                        </div>
                    </div>
                    <div class="form-group" style="display:none;">
                        <label for="full_size">{_p var='use_entire_page'}</label>
                        <div class="radio">
                            <label><input type="radio" name="val[full_size]" value="1"{value type='radio' id='full_size' default='1' selected=true}/> {_p var='yes'}</label>
                            <label><input type="radio" name="val[full_size]" value="0"{value type='radio' id='full_size' default='0'}/> {_p var='no'}</label>
                            {help var='admincp.page_add_full_size'}
                        </div>
                    </div>
                    <div class="form-group" style="display:none;">
                        <label for="has_bookmark">{_p var='add_bookmark_links'}</label>
                        <div class="radio">
                            <label><input type="radio" name="val[has_bookmark]" value="1"{value type='radio' id='has_bookmark' default='1' selected=true}/> {_p var='yes'}</label>
                            <label><input type="radio" name="val[has_bookmark]" value="0"{value type='radio' id='has_bookmark' default='0'}/> {_p var='no'}</label>
                            {help var='admincp.page_add_bookmark'}
                        </div>
                    </div>
                    <div class="form-group" style="display:none;">
                        <label for="add_view">{_p var='add_page_views'}</label>
                        <div class="radio">
                            <label><input type="radio" name="val[add_view]" value="1"{value type='radio' id='add_view' default='1'}/> {_p var='yes'}</label>
                            <label><input type="radio" name="val[add_view]" value="0"{value type='radio' id='add_view' default='0' selected=true}/> {_p var='no'}</label>
                            {help var='admincp.page_add_view'}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add_menu">{if !$bIsEdit || ($bIsEdit && !$aForms.menu_id)}{_p var='add_menu'}{else}{_p var='show_in_menu'}{/if}</label>
                        <div class="radio">
                            <label><input type="radio" name="val[add_menu]" value="1"{value type='radio' id='add_menu' default='1' selected=true}/> {_p var='yes'}</label>
                            <label><input type="radio" name="val[add_menu]" value="0"{value type='radio' id='add_menu' default='0'}/> {_p var='no'}</label>
                            {help var='admincp.page_add_menu'}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="m_connection">{_p var='menu_position'}</label>
                        <div class="radio">
                            <label><input type="radio" name="val[m_connection]" value="main"{value type='radio' id='m_connection' default='main' selected=true}/> {_p var='main'}</label>
                            <label><input type="radio" name="val[m_connection]" value="footer"{value type='radio' id='m_connection' default='footer'}/> {_p var='footer'}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    {_p var='user_group_access'}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="">{_p var='allow_access'}</label>
                        {foreach from=$aUserGroups item=aUserGroup}
                        <div class="p_4 checkbox">
                            <label><input type="checkbox" name="val[allow_access][]" value="{$aUserGroup.user_group_id}"{if isset($aAccess) && is_array($aAccess)}{if !in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
                        </div>
                        {/foreach}
                        {help var='admincp.page_add_access'}
                    </div>
                    {plugin call='page.template_controller_admincp_add_editor'}
                    <div class="p_4" style="display:none;">
                        Parser:
                        <label><input type="checkbox" name="val[parse_bbcode]" value="1" checked="checked" class="v_middle" /> {_p var='bbcode'}</label>
                        <label><input type="checkbox" name="val[parse_breaks]" value="1" checked="checked" class="v_middle" /> {_p var='add_smart_breaks'}</label>
                        <label><input type="checkbox" name="val[parse_php]" value="1" checked="checked" class="v_middle" /> {_p var='php'}</label>
                    </div>
                    <div class="page_editor_button" id="table_hover_action_holder">
                        <input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
                        <a class="btn btn-danger" href="{url link='admincp.page'}">{_p var='cancel'}</a>
                    </div>
                </div>
            </div>
		</div>
	</div>
	<div class="page_editor_content" id="">
        {if $bUseEditor}
        {editor id='text' rows='6' mode='full_height'}
        {else}
        <textarea name="text" class="form-control" id="page_editor" rows="20">{if $bIsEdit}{$aForms.text}{/if}</textarea>
        {/if}
	</div>
</form>
<script>
	{literal}
	$Ready(function() {
		$('#js_form_new_page').submit(function(evt) {
            var t = $(this);
            evt.preventDefault();
			$.ajax({
				url: t.attr('action'),
				type: 'POST',
				data: t.serialize() + '&val[text]=' + encodeURIComponent({/literal}{if $bUseEditor}Editor.getContent(){else}$('#page_editor').val(){/if}{literal}) + '&core[ajax]=true',
				success: function(e) {
                    if (typeof e.error !== 'undefined') {
                        $Core.jsConfirm({title: oTranslations['error'], no_yes: true, message: e.error, btn_no: 'OK'}, function(){}, function (){});
                    } else if (typeof e.redirect !== 'undefined'){
                        window.location.href = e.redirect;
                    }
				}
			}).always(function(){$('.page_editor_button input').removeClass('disabled')});

			return false;
		});
		$('#js_form_new_page').show();
	});
	{/literal}
</script>
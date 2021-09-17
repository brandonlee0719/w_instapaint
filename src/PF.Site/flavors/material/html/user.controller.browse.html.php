<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if defined('PHPFOX_IS_ADMIN_SEARCH')}

{if !PHPFOX_IS_AJAX}
{template file="user.block.user_filter_admin"}

<div class="block_content">
	{literal}
	<script>
		function process_admincp_browse() {
			$('input.button').hide();
			$('#table_hover_action_holder, .table_hover_action').prepend('<div class="t_center admincp-browse-fa"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
		}

		function delete_users(response, form, data) {
			// p(form);
			$('.admincp-browse-fa').remove();
			$('input.button').show();
			for (var i in data) {
				var e = data[i];
					// p('is delete...');
					form.find('input[type="checkbox"]').each(function() {
						if ($(this).is(':checked')) {
							if (e.name == 'delete') {
								$('#js_user_' + $(this).val()).remove();
							}
							else {
								$(this).prop('checked', false);
								var thisClass = $('#js_user_' + $(this).val());
								thisClass.removeClass('is_checked').addClass('is_processed');
								setTimeout(function() {
									thisClass.removeClass('is_processed');
								}, 600);
							}
						}
					});
			}
		};
	</script>
	{/literal}
	<form method="post" action="{url link='admincp.user.browse'}" class="ajax_post" data-include-button="true" data-callback-start="process_admincp_browse" data-callback="delete_users">
{/if}
		{if $aUsers}
        <div class="table-responsive">
            <table class="table table-admin" {if isset($bShowFeatured) && $bShowFeatured == 1} id="js_drag_drop"{/if}>
            <thead>
                <tr>
                    <th class="w20">
                        {if !PHPFOX_IS_AJAX}<input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" />{/if}
                    </th>
                    <th class="w20"></th>
                    <th {table_sort class="w60 centered" asc="u.user_id asc" desc="u.user_id desc" query="search[sort]"}>
                        {_p var='id'}
                    </th>
                    <th>{_p var='photo'}</th>
                    <th {table_sort class="centered" asc="u.full_name asc" desc="u.full_name desc" query="search[sort]"}>
                        {_p var='display_name'}
                    </th>
                    <th>{_p var='email_address'}</th>
                    <th>
                        {_p var='group'}
                    </th>
                    <th {table_sort class="centered" asc="u.last_activity asc" desc="u.last_activity desc" query="search[sort]"}>
                        {_p var='last_activity'}</th>
                    <th {table_sort class="centered" asc="u.last_ip_address asc" desc="u.last_ip_address desc" query="search[sort]"}>
                        {_p var='ip_address'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aUsers name=users key=iKey item=aUser}
                    {template file="user.block.user_entry_admin"}
                {/foreach}
            </tbody>
            </table>
        </div>

		{pager}
        {else}
            <div class="alert alert-empty">
            {_p var="no_members_found"}.
            </div>
		{/if}

	{/if}
    {if !PHPFOX_IS_AJAX && defined('PHPFOX_IS_ADMIN_SEARCH')}
	<div class="table_hover_action">
        <input type="submit" name="approve" value="{_p var='approve'}" class="btn sJsCheckBoxButton disabled" disabled="disabled" />
        <input type="submit" name="ban" value="{_p var='ban'}" class="sJsConfirm btn sJsCheckBoxButton disabled" disabled="disabled" />
        <input type="submit" name="unban" value="{_p var='un_ban'}" class="btn sJsCheckBoxButton disabled" disabled="disabled" />
        <input type="submit" name="verify" value="{_p var='verify'}" class="btn sJsCheckBoxButton disabled" disabled="disabled" />
        <input type="submit" name="resend-verify" value="{_p var='resend_verification_mail'}" class="btn sJsCheckBoxButton disabled" disabled="disabled" />
        {if Phpfox::getUserParam('user.can_delete_others_account')}
        <input type="submit" name="delete" value="{_p var='delete'}" class="sJsConfirm btn sJsCheckBoxButton disabled" disabled="disabled" />
        {/if}
    </div>
	</form>
</div>
{else}
	{if isset($highlightUsers) && !$bOldWay}
        {if !Phpfox::getParam('user.hide_recommended_user_block', false)}
        <div class="ajax" data-url="{url link='user.browse' recommend=1}"></div>
        {/if}
	<div class="ajax" data-url="{url link='user.browse' featured=1}"></div>
	{else}
		{if $aUsers}
		{if !PHPFOX_IS_AJAX}
		<div class="item-container user-listing" id="collection-users">
		{/if}
			{foreach from=$aUsers name=users item=aUser}
				<article class="user-item">
					{template file='user.block.rows_wide'}
				</article>
			{/foreach}
		{pager}
		{if !PHPFOX_IS_AJAX}
		</div>
		{/if}
        {elseif !PHPFOX_IS_AJAX}
        <div class="alert alert-info">
        {_p var="no_members_found"}.
        </div>
		{/if}
	{/if}
{/if}

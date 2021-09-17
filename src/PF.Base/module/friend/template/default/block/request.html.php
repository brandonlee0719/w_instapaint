<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: request.html.php 6551 2013-08-30 10:50:19Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
	<script type="text/javascript">
	$Core.loadStaticFile(getParam('sJsHome') + 'static/jscript/jquery/plugin/jquery.limitTextarea.js');
	function processList(sValue)
	{
		if (sValue == '')
		{
			return false;
		}
		
		if (sValue == 'create_new')
		{
			$('#js_list_options').hide(); 
			$('#js_add_new_list').show();
			return false;
		}
		
		return false;
	}

	function resetList()
	{
		$('#js_add_new_list').hide(); 
		$('#js_list_options').show();
		
		$('option').each(function()
		{
			this.selected = false;
		});
		
		return false;	
	}
	</script>
{/literal}


<form method="post" action="#" id="js_process_request" onsubmit="return false;">
{if $bInvite}
	<div>
		<input type="hidden" name="val[invite]" value="true" />
	</div>
{/if}
{if $bSuggestion}
	<div>
		<input type="hidden" name="val[suggestion]" value="true" />
	</div>
{/if}
{if $bPageSuggestion}
	<div>
		<input type="hidden" name="val[page_suggestion]" value="true" />
	</div>
{/if}
<div>
	<input type="hidden" name="val[user_id]" value="{$aUser.user_id}" />
</div>
<div class="go_left t_center" id="profile_picture_container">
	{img user=$aUser suffix='_50_square' max_width=50 max_height=50}
</div>
<div class="request_text">
{if $sError}
	{if $sError == 'already_asked'}
		<div>{_p var='you_have_already_asked_full_name_to_be_your_friend' full_name=$aUser.full_name}</div>
	{elseif $sError == 'user_asked_already'}
		<div>{_p var='full_name_has_already_asked_to_be_your_friend' full_name=$aUser.full_name}</div>
		<div class="p_4">
			{_p var='would_you_like_to_accept_their_request_to_be_friends'}
			<div class="p_4">
				<input type="submit" onclick="$('#js_process_request').ajaxCall('friend.processRequest', 'type=yes&amp;user_id={$aUser.user_id}');" value="{_p var='yes'}" class="button btn-primary" />
				<input type="submit" onclick="$('#js_process_request').ajaxCall('friend.processRequest', 'type=no&amp;user_id={$aUser.user_id}');" value="{_p var='no'}" class="button btn-primary" />
			</div>
		</div>	
	{elseif $sError == 'same_user'}
		<div>{_p var='cannot_add_yourself_as_a_friend'}</div>
	{elseif $sError == 'already_friends'}
		<div>{_p var='you_are_already_friends_with_full_name' full_name=$aUser.full_name}</div>
	{/if}
</div>
{else}
	{_p var='user_link_will_have_to_confirm_that_you_are_friends' user=$aUser}
	</div>
	<div class="clear"></div>
	<div class="p_top_15">
		<div class="table_clear" id="container_submit_friend_request">
			<input type="submit" onclick="{if $bSuggestion}$('#js_friend_suggestion').hide(); $('#js_friend_suggestion_loader').show(); {/if}$('#js_process_image').show(); $('#js_process_request').ajaxCall('friend.addRequest');" value="{_p var='send_friend_request'}" class="button btn-primary" /> <span id="js_process_image" style="display:none;">{img theme='ajax/add.gif'}</span>
		</div>
	</div>
{/if}
</form>
<script type="text/javascript">$Core.init();</script>